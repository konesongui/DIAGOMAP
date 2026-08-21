// sw.js - Service Worker pour Diagoma
// Version: 1.0.0

const CACHE_NAME = 'diagoma-v1';
const OFFLINE_URL = '/diagoma/offline.html';

// Liste des fichiers à mettre en cache
const urlsToCache = [
    '/diagoma/',
    '/diagoma/index.php',
    '/diagoma/offline.html',
    
    // CSS
    '/diagoma/backend/bootstrap/css/bootstrap.min.css',
    '/diagoma/backend/dist/css/font-awesome.min.css',
    '/diagoma/backend/dist/css/ionicons.min.css',
    '/diagoma/backend/dist/css/custom_style.css',
    '/diagoma/backend/dist/css/jquery.mCustomScrollbar.min.css',
    '/diagoma/backend/plugins/iCheck/flat/blue.css',
    '/diagoma/backend/plugins/datepicker/datepicker3.css',
    '/diagoma/backend/plugins/colorpicker/bootstrap-colorpicker.css',
    '/diagoma/backend/plugins/daterangepicker/daterangepicker-bs3.css',
    '/diagoma/backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css',
    '/diagoma/backend/datepicker/css/bootstrap-datetimepicker.css',
    '/diagoma/backend/dist/css/dropify.min.css',
    '/diagoma/backend/dist/css/nprogress.css',
    '/diagoma/backend/dist/datatables/css/jquery.dataTables.min.css',
    '/diagoma/backend/dist/datatables/css/buttons.dataTables.min.css',
    '/diagoma/backend/dist/datatables/css/dataTables.bootstrap.min.css',
    '/diagoma/backend/dist/datatables/css/responsive.dataTables.min.css',
    '/diagoma/backend/dist/datatables/css/rowReorder.dataTables.min.css',
    '/diagoma/backend/fullcalendar/dist/fullcalendar.min.css',
    '/diagoma/backend/fullcalendar/dist/fullcalendar.print.min.css',
    
    // JavaScript
    '/diagoma/backend/custom/jquery.min.js',
    '/diagoma/backend/dist/js/moment.min.js',
    '/diagoma/backend/datepicker/js/bootstrap-datetimepicker.js',
    '/diagoma/backend/plugins/colorpicker/bootstrap-colorpicker.js',
    '/diagoma/backend/datepicker/date.js',
    '/diagoma/backend/dist/js/jquery-ui.min.js',
    '/diagoma/backend/js/school-custom.js',
    '/diagoma/backend/js/school-admin-custom.js',
    '/diagoma/backend/js/sstoast.js',
    '/diagoma/backend/bootstrap/js/bootstrap.min.js',
    '/diagoma/backend/plugins/iCheck/icheck.min.js',
    '/diagoma/backend/plugins/slimScroll/jquery.slimscroll.min.js',
    '/diagoma/backend/plugins/fastclick/fastclick.min.js',
    '/diagoma/backend/dist/js/app.min.js',
    '/diagoma/backend/plugins/datatables/jquery.dataTables.min.js',
    '/diagoma/backend/plugins/datatables/dataTables.bootstrap.min.js',
    '/diagoma/backend/fullcalendar/dist/fullcalendar.min.js',
    '/diagoma/backend/fullcalendar/dist/locale/fr.js'
];

// ========================================== //
// INSTALLATION DU SERVICE WORKER             //
// ========================================== //
self.addEventListener('install', function(event) {
    console.log('[ServiceWorker] Installation en cours...');
    
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function(cache) {
                console.log('[ServiceWorker] Mise en cache des ressources...');
                return cache.addAll(urlsToCache);
            })
            .then(function() {
                console.log('[ServiceWorker] Installation terminée');
                return self.skipWaiting();
            })
            .catch(function(error) {
                console.error('[ServiceWorker] Erreur lors de l\'installation:', error);
            })
    );
});

// ========================================== //
// ACTIVATION DU SERVICE WORKER               //
// ========================================== //
self.addEventListener('activate', function(event) {
    console.log('[ServiceWorker] Activation en cours...');
    
    const cacheWhitelist = [CACHE_NAME];
    
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.map(function(cacheName) {
                    if (cacheWhitelist.indexOf(cacheName) === -1) {
                        console.log('[ServiceWorker] Suppression de l\'ancien cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(function() {
            console.log('[ServiceWorker] Activation terminée');
            return self.clients.claim();
        })
    );
});

// ========================================== //
// INTERCEPTION DES REQUÊTES (CORRIGÉ)        //
// ========================================== //
self.addEventListener('fetch', function(event) {
    // IGNORER les requêtes POST, PUT, DELETE (API)
    if (event.request.method !== 'GET') {
        console.log('[ServiceWorker] Ignoré (méthode non-GET):', event.request.method);
        return;
    }

    // IGNORER les requêtes vers l'API et login
    if (event.request.url.indexOf('/site/login') !== -1 ||
        event.request.url.indexOf('/admin/') !== -1 ||
        event.request.url.indexOf('/api/') !== -1 ||
        event.request.url.indexOf('/ajax/') !== -1) {
        console.log('[ServiceWorker] Ignoré (API/Login):', event.request.url);
        return;
    }

    // IGNORER les requêtes vers des ressources externes
    if (event.request.url.indexOf('https://cdnjs.cloudflare.com') !== -1 ||
        event.request.url.indexOf('https://fonts.googleapis.com') !== -1 ||
        event.request.url.indexOf('https://maps.googleapis.com') !== -1) {
        return;
    }

    event.respondWith(
        caches.match(event.request)
            .then(function(response) {
                if (response) {
                    console.log('[ServiceWorker] Ressource trouvée dans le cache:', event.request.url);
                    return response;
                }
                return fetch(event.request)
                    .then(function(response) {
                        if (response && response.status === 200) {
                            var responseToCache = response.clone();
                            caches.open(CACHE_NAME)
                                .then(function(cache) {
                                    cache.put(event.request, responseToCache);
                                });
                        }
                        return response;
                    })
                    .catch(function(error) {
                        console.warn('[ServiceWorker] Erreur réseau:', error);
                        if (event.request.mode === 'navigate') {
                            return caches.match(OFFLINE_URL);
                        }
                        return new Response('Erreur réseau', { status: 503 });
                    });
            })
    );
});

// ========================================== //
// GESTION DES NOTIFICATIONS PUSH             //
// ========================================== //
self.addEventListener('push', function(event) {
    let title = 'Diagoma';
    let body = 'Vous avez une nouvelle notification';
    let icon = '/diagoma/uploads/school_content/admin_logo/icon-192x192.png';
    
    if (event.data) {
        try {
            const payload = event.data.json();
            title = payload.title || title;
            body = payload.body || body;
            icon = payload.icon || icon;
        } catch (e) {
            body = event.data.text() || body;
        }
    }
    
    const options = {
        body: body,
        icon: icon,
        vibrate: [200, 100, 200]
    };
    
    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// ========================================== //
// CLIC SUR LES NOTIFICATIONS                 //
// ========================================== //
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(function(clientList) {
                for (var i = 0; i < clientList.length; i++) {
                    var client = clientList[i];
                    if (client.url === '/' && 'focus' in client) {
                        return client.focus();
                    }
                }
                if (clients.openWindow) {
                    return clients.openWindow('/diagoma/');
                }
            })
    );
});

console.log('[ServiceWorker] Service Worker chargé avec succès');