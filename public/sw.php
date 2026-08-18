<?php
header('Content-Type: application/javascript');
 
// Lecture de la version centralisée (siteVersion + appBuild)
$version = require __DIR__ . '/version.php';
$cacheName = $version['siteVersion'] . '.' . $version['appBuild'];
?>
const CACHE_NAME = '<?= $cacheName ?>';

// Liste complète des ressources locales à mettre en cache initialement
const ASSETS_TO_CACHE = [
  '/',
  '/index.php',
  '/__partials/footer.php',
  '/__partials/menu.php',
  '/divers/astronomie/astronomie.php',
  '/divers/astronomie/contenu-astronomie.php',
  '/divers/meteorologie/meteorologie.php',
  '/divers/meteorologie/contenu-meteorologie.php',
  '/connexion/login.php',
  '/connexion/signup.php'
];

/* ==========================================================================
   1. INSTALLATION : MISE EN CACHE INITIALE
   ========================================================================== */
self.addEventListener('install', (event) => {
  // skipWaiting() est volontairement omis pour laisser le contrôle au Toast utilisateur
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      console.log('PWA SW : Mise en cache des ressources initiales.');
      return cache.addAll(ASSETS_TO_CACHE);
    })
  );
});

/* ==========================================================================
   2. ACTIVATION : NETTOYAGE ET PRISE DE CONTRÔLE
   ========================================================================== */
self.addEventListener('activate', (event) => {
  // Permet au SW de contrôler immédiatement toutes les pages ouvertes dès son activation
  event.waitUntil(self.clients.claim());

  // Nettoyage des anciens caches obsolètes
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cache) => {
          if (cache !== CACHE_NAME) {
            console.log('PWA SW : Suppression de l\'ancien cache obsolète : ', cache);
            return caches.delete(cache);
          }
        })
      );
    })
  );
});

/* ==========================================================================
   3. INTERCOMMUNICATION & SCRIPT MESSAGES
   ========================================================================== */
self.addEventListener('message', (event) => {
  if (event.data && event.data.action === 'skipWaiting') {
    console.log('PWA SW : Signal skipWaiting reçu. Activation forcée.');
    self.skipWaiting();
  }
});

/* ==========================================================================
   4. GESTION DES NOTIFICATIONS (AVANT-PLAN ET ARRIÈRE-PLAN)
   ========================================================================== */

// Écouteur PUSH : Intercepte les notifications du serveur, même si l'application est fermée
self.addEventListener('push', (event) => {
  let data = { title: "Alerte !", body: "Nouvelle notification reçue." };

  if (event.data) {
    try {
      data = event.data.json();
    } catch (e) {
      data = { title: "Notification", body: event.data.text() };
    }
  }

  const options = {
    body: data.body,
    icon: data.icon || '/assets/images/logo.png',
    badge: data.badge || '/assets/images/logo.png',
    vibrate: [200, 100, 200],
    requireInteraction: false,
    data: {
      url: data.url || '/'
    }
  };

  // Force l'OS à maintenir le Service Worker éveillé le temps d'afficher l'alerte
  event.waitUntil(
    self.registration.showNotification(data.title, options)
  );
});

// Écouteur CLICK : Gère le comportement de l'application au clic sur une notification
self.addEventListener('notificationclick', (event) => {
  event.notification.close(); // Ferme le bandeau de notification sur le système

  const targetUrl = event.notification.data.url || '/';

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
      // Si l'application est déjà ouverte en arrière-plan, on la remonte au premier plan (focus)
      for (const client of clientList) {
        if (client.url.includes(targetUrl) && 'focus' in client) {
          return client.focus();
        }
      }
      // Si l'application était fermée, on l'ouvre directement sur la ressource demandée
      if (clients.openWindow) {
        return clients.openWindow(targetUrl);
      }
    })
  );
});

/* ==========================================================================
   5. STRATÉGIE DE CACHE HYBRIDE (DYNAMIQUE ET SÉCURISÉE)
   ========================================================================== */
self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') return;

  const url = new URL(event.request.url);
  if (!url.protocol.startsWith('http')) return;

  // STRATÉGIE A : Network First pour les pages HTML (Navigation)
  if (event.request.mode === 'navigate' || (event.request.headers.get('accept') && event.request.headers.get('accept').includes('text/html'))) {
    event.respondWith(
      fetch(event.request)
        .then((response) => {
          if (response.status === 200) {
            const responseToCache = response.clone();
            caches.open(CACHE_NAME).then((cache) => {
              cache.put(event.request, responseToCache);
            });
          }
          return response;
        })
        .catch(() => {
          return caches.match(event.request);
        })
    );
    return;
  }

  // STRATÉGIE B : Cache First pour les images et médias
  if (event.request.headers.get('accept') && event.request.headers.get('accept').includes('image/')) {
    event.respondWith(
      caches.match(event.request).then((cachedResponse) => {
        if (cachedResponse) {
          return cachedResponse;
        }
        return fetch(event.request).then((response) => {
          if (response.status === 200) {
            const responseToCache = response.clone();
            caches.open(CACHE_NAME).then((cache) => {
              cache.put(event.request, responseToCache);
            });
          }
          return response;
        });
      })
    );
    return;
  }

  // STRATÉGIE C : Stale-While-Revalidate pour les assets de structure (CSS, JS, Fonts)
  event.respondWith(
    caches.match(event.request).then((cachedResponse) => {
      const fetchPromise = fetch(event.request).then((networkResponse) => {
        if (networkResponse.status === 200) {
          const responseToCache = networkResponse.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, responseToCache);
          });
        }
        return networkResponse;
      }).catch(() => null); // Évite les erreurs réseau silencieuses en tâche de fond

      return cachedResponse || fetchPromise;
    })
  );
});