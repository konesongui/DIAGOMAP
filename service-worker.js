const SW_VERSION = 'diagoma-offline-v1';
const RUNTIME_CACHE = SW_VERSION + '-runtime';
const APP_SCOPE = new URL(self.location.href).pathname.replace(/service-worker\.js$/, '');
const OFFLINE_URL = APP_SCOPE + 'offline.html';
const DB_NAME = 'diagoma-offline-db';
const STORE_NAME = 'requests';

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(RUNTIME_CACHE).then((cache) => {
            return cache.addAll([
                OFFLINE_URL,
                APP_SCOPE + 'manifest.json'
            ]);
        }).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(keys.map((key) => {
                if (key !== RUNTIME_CACHE) {
                    return caches.delete(key);
                }
                return Promise.resolve();
            }));
        }).then(() => self.clients.claim())
    );
});

self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SYNC_OFFLINE_QUEUE') {
        event.waitUntil(replayQueuedRequests());
    }
});

self.addEventListener('sync', (event) => {
    if (event.tag === 'diagoma-sync-requests') {
        event.waitUntil(replayQueuedRequests());
    }
});

self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    if (url.origin !== self.location.origin) {
        return;
    }

    if (request.method === 'GET') {
        if (request.mode === 'navigate') {
            event.respondWith(handleNavigationRequest(request));
            return;
        }

        event.respondWith(handleAssetRequest(request));
        return;
    }

    if (isQueueableMutationRequest(request)) {
        event.respondWith(handleMutationRequest(request));
    }
});

async function handleNavigationRequest(request) {
    const cache = await caches.open(RUNTIME_CACHE);

    try {
        const response = await fetch(request);
        if (response && response.ok) {
            cache.put(request, response.clone());
        }
        return response;
    } catch (error) {
        const cached = await cache.match(request);
        if (cached) {
            return cached;
        }

        const offlinePage = await cache.match(OFFLINE_URL);
        if (offlinePage) {
            return offlinePage;
        }

        throw error;
    }
}

async function handleAssetRequest(request) {
    const cache = await caches.open(RUNTIME_CACHE);
    const cached = await cache.match(request);

    if (cached) {
        fetch(request).then((response) => {
            if (response && response.ok) {
                cache.put(request, response.clone());
            }
        }).catch(() => {
        });

        return cached;
    }

    const response = await fetch(request);
    if (response && response.ok) {
        cache.put(request, response.clone());
    }
    return response;
}

async function handleMutationRequest(request) {
    try {
        return await fetch(request.clone());
    } catch (error) {
        await queueRequest(request.clone());
        await requestBackgroundSync();
        return buildQueuedResponse(request);
    }
}

function isQueueableMutationRequest(request) {
    if (!['POST', 'PUT', 'PATCH', 'DELETE'].includes(request.method)) {
        return false;
    }

    const path = new URL(request.url).pathname.toLowerCase();
    if (path.indexOf('/site/login') !== -1 || path.indexOf('/site/logout') !== -1 || path.indexOf('/login') !== -1) {
        return false;
    }

    return true;
}

async function requestBackgroundSync() {
    if ('sync' in self.registration) {
        try {
            await self.registration.sync.register('diagoma-sync-requests');
        } catch (error) {
        }
    }
}

function buildQueuedResponse(request) {
    const accept = request.headers.get('accept') || '';
    const isAjax = request.headers.get('x-requested-with') === 'XMLHttpRequest' || accept.indexOf('application/json') !== -1;

    if (isAjax) {
        return new Response(JSON.stringify({
            status: 'success',
            offline_queued: true,
            message: 'Action enregistree hors ligne. Synchronisation automatique a la reconnexion.'
        }), {
            status: 202,
            headers: {
                'Content-Type': 'application/json'
            }
        });
    }

    if (request.mode === 'navigate' || accept.indexOf('text/html') !== -1) {
        return new Response(getOfflineQueuedHtml(), {
            status: 202,
            headers: {
                'Content-Type': 'text/html; charset=utf-8'
            }
        });
    }

    return new Response('Action enregistree hors ligne. Synchronisation automatique a la reconnexion.', {
        status: 202,
        headers: {
            'Content-Type': 'text/plain; charset=utf-8'
        }
    });
}

function getOfflineQueuedHtml() {
    return '<!DOCTYPE html>'
        + '<html lang="fr"><head><meta charset="utf-8">'
        + '<meta name="viewport" content="width=device-width, initial-scale=1">'
        + '<title>Diagoma - Hors connexion</title>'
        + '<style>body{font-family:Arial,sans-serif;background:#f8fafc;margin:0;padding:30px;color:#0f172a}'
        + '.card{max-width:640px;margin:60px auto;background:#fff;padding:30px;border-radius:14px;box-shadow:0 20px 45px rgba(15,23,42,.1)}'
        + 'h1{margin-top:0;color:#273772}a{display:inline-block;margin-top:16px;color:#273772;font-weight:700;text-decoration:none}</style>'
        + '</head><body><div class="card"><h1>Action enregistree hors ligne</h1>'
        + '<p>Votre saisie a ete stockee localement. Elle sera synchronisee automatiquement des que la connexion internet reviendra.</p>'
        + '<p>Vous pouvez continuer a travailler sur les pages deja consultees.</p>'
        + '<a href="javascript:history.back()">Retour</a></div></body></html>';
}

function openDb() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, 1);

        request.onupgradeneeded = (event) => {
            const db = event.target.result;
            if (!db.objectStoreNames.contains(STORE_NAME)) {
                db.createObjectStore(STORE_NAME, { keyPath: 'id' });
            }
        };

        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

function getAllQueuedRequests() {
    return openDb().then((db) => {
        return new Promise((resolve, reject) => {
            const transaction = db.transaction(STORE_NAME, 'readonly');
            const store = transaction.objectStore(STORE_NAME);
            const request = store.getAll();

            request.onsuccess = () => {
                resolve((request.result || []).sort((a, b) => {
                    return new Date(a.createdAt).getTime() - new Date(b.createdAt).getTime();
                }));
            };

            request.onerror = () => reject(request.error);
            transaction.oncomplete = () => db.close();
        });
    });
}

function putQueuedPayload(payload) {
    return openDb().then((db) => {
        return new Promise((resolve, reject) => {
            const transaction = db.transaction(STORE_NAME, 'readwrite');
            transaction.objectStore(STORE_NAME).put(payload);
            transaction.oncomplete = () => {
                db.close();
                resolve();
            };
            transaction.onerror = () => {
                db.close();
                reject(transaction.error);
            };
        });
    });
}

function deleteQueuedPayload(id) {
    return openDb().then((db) => {
        return new Promise((resolve, reject) => {
            const transaction = db.transaction(STORE_NAME, 'readwrite');
            transaction.objectStore(STORE_NAME).delete(id);
            transaction.oncomplete = () => {
                db.close();
                resolve();
            };
            transaction.onerror = () => {
                db.close();
                reject(transaction.error);
            };
        });
    });
}

async function queueRequest(request) {
    const payload = await serializeRequest(request);
    await putQueuedPayload(payload);
    await notifyQueueUpdated();
}

async function serializeRequest(request) {
    const headers = {};
    request.headers.forEach((value, key) => {
        headers[key] = value;
    });

    const payload = {
        id: 'req-' + Date.now() + '-' + Math.random().toString(16).slice(2),
        url: request.url,
        method: request.method,
        headers: headers,
        createdAt: new Date().toISOString()
    };

    if (request.method === 'GET' || request.method === 'HEAD') {
        return payload;
    }

    const contentType = request.headers.get('content-type') || '';

    if (contentType.indexOf('multipart/form-data') !== -1) {
        const formData = await request.formData();
        payload.bodyType = 'formData';
        payload.entries = [];
        formData.forEach((value, key) => {
            if (value instanceof File) {
                payload.entries.push({
                    name: key,
                    kind: 'file',
                    value: value,
                    filename: value.name || 'upload.bin'
                });
                return;
            }

            payload.entries.push({
                name: key,
                kind: 'text',
                value: value == null ? '' : String(value)
            });
        });

        return payload;
    }

    if (contentType.indexOf('application/json') !== -1 || contentType.indexOf('application/x-www-form-urlencoded') !== -1 || contentType.indexOf('text/plain') !== -1) {
        payload.bodyType = 'text';
        payload.bodyText = await request.text();
        return payload;
    }

    payload.bodyType = 'blob';
    payload.bodyBlob = await request.blob();
    return payload;
}

function rebuildRequestInit(payload) {
    const headers = new Headers(payload.headers || {});
    const init = {
        method: payload.method,
        headers: headers,
        credentials: 'same-origin'
    };

    if (payload.bodyType === 'formData') {
        const formData = new FormData();
        (payload.entries || []).forEach((entry) => {
            if (entry.kind === 'file') {
                formData.append(entry.name, entry.value, entry.filename || 'upload.bin');
            } else {
                formData.append(entry.name, entry.value);
            }
        });
        headers.delete('content-type');
        init.body = formData;
    } else if (payload.bodyType === 'text') {
        init.body = payload.bodyText || '';
    } else if (payload.bodyType === 'blob') {
        init.body = payload.bodyBlob;
    }

    return init;
}

async function replayQueuedRequests() {
    const requests = await getAllQueuedRequests();
    let synced = 0;
    let failed = 0;

    for (const payload of requests) {
        try {
            const response = await fetch(payload.url, rebuildRequestInit(payload));
            if (response && response.ok) {
                await deleteQueuedPayload(payload.id);
                synced += 1;
            } else {
                failed += 1;
            }
        } catch (error) {
            failed += 1;
        }
    }

    const remaining = (await getAllQueuedRequests()).length;
    await notifyClients({
        type: 'DIAGOMA_OFFLINE_SYNC_RESULT',
        synced: synced,
        failed: failed,
        remaining: remaining
    });
    await notifyQueueUpdated();
}

async function notifyQueueUpdated() {
    const count = (await getAllQueuedRequests()).length;
    await notifyClients({
        type: 'DIAGOMA_OFFLINE_QUEUE_UPDATED',
        count: count
    });
}

async function notifyClients(message) {
    const clientList = await self.clients.matchAll({
        includeUncontrolled: true,
        type: 'window'
    });

    clientList.forEach((client) => {
        client.postMessage(message);
    });
}
