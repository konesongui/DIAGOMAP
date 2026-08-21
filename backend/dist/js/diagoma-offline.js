(function (window, document) {
    'use strict';

    if (!window.indexedDB) {
        return;
    }

    var config = window.diagomaOfflineConfig || {};
    var baseUrl = config.baseUrl || '/';
    var dbName = 'diagoma-offline-db';
    var storeName = 'requests';
    var bannerEl = null;
    var queueCount = 0;

    function openDb() {
        return new Promise(function (resolve, reject) {
            var request = window.indexedDB.open(dbName, 1);

            request.onupgradeneeded = function (event) {
                var db = event.target.result;
                if (!db.objectStoreNames.contains(storeName)) {
                    db.createObjectStore(storeName, { keyPath: 'id' });
                }
            };

            request.onsuccess = function (event) {
                resolve(event.target.result);
            };

            request.onerror = function () {
                reject(request.error);
            };
        });
    }

    function withStore(mode, callback) {
        return openDb().then(function (db) {
            return new Promise(function (resolve, reject) {
                var transaction = db.transaction(storeName, mode);
                var store = transaction.objectStore(storeName);
                var settled = false;
                var result;

                function resolveOnce(value) {
                    if (!settled) {
                        settled = true;
                        resolve(value);
                    }
                }

                function rejectOnce(error) {
                    if (!settled) {
                        settled = true;
                        reject(error);
                    }
                }

                result = callback(store, resolveOnce, rejectOnce);

                transaction.oncomplete = function () {
                    db.close();
                    if (!settled) {
                        resolveOnce(result);
                    }
                };

                transaction.onerror = function () {
                    db.close();
                    rejectOnce(transaction.error);
                };
            });
        });
    }

    function getQueueCount() {
        return withStore('readonly', function (store, resolve) {
            var request = store.count();
            request.onsuccess = function () {
                resolve(request.result || 0);
            };
        });
    }

    function putQueuedRequest(payload) {
        return withStore('readwrite', function (store) {
            store.put(payload);
        });
    }

    function serializeForm(form) {
        var formData = new window.FormData(form);
        var entries = [];

        formData.forEach(function (value, key) {
            if (value instanceof window.File) {
                entries.push({
                    name: key,
                    kind: 'file',
                    value: value,
                    filename: value.name || 'upload.bin'
                });
                return;
            }

            entries.push({
                name: key,
                kind: 'text',
                value: value == null ? '' : String(value)
            });
        });

        return {
            id: 'form-' + Date.now() + '-' + Math.random().toString(16).slice(2),
            url: form.action || window.location.href,
            method: (form.method || 'POST').toUpperCase(),
            headers: {
                'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'X-Diagoma-Offline-Queued': '1'
            },
            bodyType: 'formData',
            entries: entries,
            createdAt: new Date().toISOString(),
            referrer: window.location.href
        };
    }

    function isSameOrigin(url) {
        try {
            return new URL(url, window.location.href).origin === window.location.origin;
        } catch (error) {
            return false;
        }
    }

    function canInterceptForm(form) {
        if (!form) {
            return false;
        }

        var method = (form.method || 'GET').toUpperCase();
        if (method === 'GET') {
            return false;
        }

        if (form.hasAttribute('data-offline-ignore')) {
            return false;
        }

        if (!isSameOrigin(form.action || window.location.href)) {
            return false;
        }

        var action = (form.action || '').toLowerCase();
        return action.indexOf('/site/login') === -1 && action.indexOf('/logout') === -1;
    }

    function registerServiceWorker() {
        if (!('serviceWorker' in navigator)) {
            return;
        }

        window.addEventListener('load', function () {
            navigator.serviceWorker.register(baseUrl + 'service-worker.js')
                .catch(function (error) {
                    window.console.error('Diagoma offline service worker registration failed', error);
                });
        });
    }

    function ensureBanner() {
        if (bannerEl) {
            return bannerEl;
        }

        bannerEl = document.createElement('div');
        bannerEl.className = 'diagoma-offline-banner';
        bannerEl.innerHTML = ''
            + '<div class="diagoma-offline-banner__content">'
            + '  <span class="diagoma-offline-banner__title">Mode hors connexion</span>'
            + '  <span class="diagoma-offline-banner__text"></span>'
            + '</div>'
            + '<button type="button" class="diagoma-offline-banner__action" style="display:none;">Synchroniser</button>';

        document.body.appendChild(bannerEl);

        bannerEl.querySelector('.diagoma-offline-banner__action').addEventListener('click', function () {
            requestSync();
        });

        return bannerEl;
    }

    function notify(message, tone, autoclose) {
        ensureBanner();

        var title = 'Mode hors connexion';
        if (tone === 'online') {
            title = 'Connexion rétablie';
        } else if (tone === 'error') {
            title = 'Synchronisation en attente';
        }

        bannerEl.classList.add('is-visible');
        bannerEl.classList.remove('is-online', 'is-offline', 'is-error');
        bannerEl.classList.add(tone === 'online' ? 'is-online' : (tone === 'error' ? 'is-error' : 'is-offline'));
        bannerEl.querySelector('.diagoma-offline-banner__title').textContent = title;
        bannerEl.querySelector('.diagoma-offline-banner__text').textContent = message;

        var button = bannerEl.querySelector('.diagoma-offline-banner__action');
        button.style.display = navigator.onLine && queueCount > 0 ? 'inline-flex' : 'none';

        if (window.toastr) {
            if (tone === 'error') {
                window.toastr.warning(message);
            } else if (tone === 'online') {
                window.toastr.success(message);
            } else {
                window.toastr.info(message);
            }
        }

        if (autoclose !== false) {
            window.clearTimeout(notify._timer);
            notify._timer = window.setTimeout(function () {
                if (navigator.onLine && queueCount === 0) {
                    bannerEl.classList.remove('is-visible');
                }
            }, 5000);
        }
    }

    function updateStatusBanner() {
        ensureBanner();

        var button = bannerEl.querySelector('.diagoma-offline-banner__action');
        button.style.display = navigator.onLine && queueCount > 0 ? 'inline-flex' : 'none';

        if (!navigator.onLine) {
            notify('Vous êtes hors ligne. Les nouvelles écritures seront stockées localement puis synchronisées.', 'offline', false);
            return;
        }

        if (queueCount > 0) {
            notify(queueCount + ' action(s) en attente de synchronisation.', 'online', false);
            return;
        }

        bannerEl.classList.remove('is-visible');
    }

    function refreshQueueCount() {
        return getQueueCount()
            .then(function (count) {
                queueCount = count;
                updateStatusBanner();
            })
            .catch(function () {
                queueCount = 0;
                updateStatusBanner();
            });
    }

    function requestSync() {
        if (!navigator.onLine || !navigator.serviceWorker) {
            return;
        }

        navigator.serviceWorker.ready.then(function (registration) {
            var target = navigator.serviceWorker.controller || registration.active;
            if (!target) {
                return;
            }

            target.postMessage({
                type: 'SYNC_OFFLINE_QUEUE'
            });
        });
    }

    function handleServiceWorkerMessage(event) {
        var data = event.data || {};

        if (data.type === 'DIAGOMA_OFFLINE_QUEUE_UPDATED') {
            queueCount = data.count || 0;
            updateStatusBanner();
            return;
        }

        if (data.type === 'DIAGOMA_OFFLINE_SYNC_RESULT') {
            queueCount = data.remaining || 0;

            if ((data.synced || 0) > 0) {
                notify(data.synced + ' action(s) synchronisée(s) avec succès.', 'online');
            }

            if ((data.failed || 0) > 0) {
                notify(data.failed + ' action(s) restent en attente de synchronisation.', 'error', false);
            }

            updateStatusBanner();
        }
    }

    function setupFormOfflineQueue() {
        document.addEventListener('submit', function (event) {
            var form = event.target;

            if (!canInterceptForm(form) || navigator.onLine) {
                return;
            }

            event.preventDefault();

            putQueuedRequest(serializeForm(form))
                .then(function () {
                    return refreshQueueCount();
                })
                .then(function () {
                    notify('Votre saisie a été enregistrée hors ligne. Elle sera envoyée automatiquement dès le retour de la connexion.', 'offline', false);
                });
        }, true);
    }

    function init() {
        registerServiceWorker();
        ensureBanner();
        refreshQueueCount().then(function () {
            if (navigator.onLine && queueCount > 0) {
                requestSync();
            }
        });
        setupFormOfflineQueue();

        window.addEventListener('online', function () {
            refreshQueueCount().then(function () {
                requestSync();
            });
        });

        window.addEventListener('offline', function () {
            updateStatusBanner();
        });

        if (navigator.serviceWorker) {
            navigator.serviceWorker.addEventListener('message', handleServiceWorkerMessage);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})(window, document);
