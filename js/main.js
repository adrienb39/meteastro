/* ==========================================================================
   1. VARIABLES GLOBALES ET CONFIGURATION
   ========================================================================== */
let deferredPrompt;
const CONFIG = {
  icon: '/ressources/logo.png',            // Icône principale (Pleine couleur)
  badge: '/ressources/logo.png',           // Badge monochrome pour la barre d'état Android
  storageKey: 'notif_onboarding_dismissed'
};

// Éléments DOM (sécurisés et récupérés une seule fois)
const elements = {
  installBtn: document.getElementById('install-btn'),
  onboarding: document.getElementById('notification-onboarding'),
  btnAcceptNotif: document.getElementById('btn-accept-notif'),
  btnRefuseNotif: document.getElementById('btn-refuse-notif'),
  contactForm: document.getElementById('contact-form'),
  menuToggleBtn: document.getElementById('menu-toggle-btn'),
  appDrawer: document.getElementById('app-drawer'),
  drawerOverlay: document.getElementById('app-drawer-overlay'),
  // Éléments du sous-menu de compte utilisateur
  menuTrigger: document.getElementById('account-menu-trigger'),
  submenu: document.getElementById('account-submenu')
};

/* ==========================================================================
   2. GESTION DES MISES À JOUR (NOTIF PARTOUT + TOAST EN STANDALONE UNIQUEMENT)
   ========================================================================== */
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js', { updateViaCache: 'none' })
      .then((registration) => {
        console.log('PWA : Service Worker enregistré avec succès.');

        // 1. Détection au chargement : une mise à jour attend déjà l'activation
        if (registration.waiting) {
          handleUpdateProcess(registration.waiting);
        }

        // 2. Détection en cours de route : un nouveau SW s'installe
        registration.addEventListener('updatefound', () => {
          const newWorker = registration.installing;
          if (!newWorker) return;

          newWorker.addEventListener('statechange', () => {
            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
              handleUpdateProcess(newWorker);
            }
          });
        });
      })
      .catch((error) => {
        console.error('PWA : Échec de l\'enregistrement :', error);
      });
  });

  // Rechargement propre dès que le nouveau SW prend le contrôle
  let isRefreshing = false;
  navigator.serviceWorker.addEventListener('controllerchange', () => {
    if (!isRefreshing) {
      isRefreshing = true;
      window.location.reload();
    }
  });
}

/**
 * Centralise le traitement de la mise à jour (Notification + aiguillage du Toast)
 */
function handleUpdateProcess(waitingWorker) {
  // 1. Envoi systématique de la Notification en tâche de fond / premier plan via SW
  sendPwaNotification(
    "Mise à jour disponible ✨", 
    "Une nouvelle version est prête. Cliquez pour relancer l'application."
  );

  // 2. Vérification du mode d'affichage
  const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
  
  // On génère le Toast visuel UNIQUEMENT si on est en mode Standalone (App installée)
  if (isStandalone) {
    showUpdateToast(waitingWorker);
  }
}

/**
 * Crée et affiche le composant Toast (Exclusif au mode standalone)
 */
function showUpdateToast(waitingWorker) {
  if (document.getElementById('pwa-update-toast')) return;

  const toast = document.createElement('div');
  toast.id = 'pwa-update-toast';
  toast.innerHTML = `
    <div class="update-toast-content">
      <span class="update-toast-icon">✨</span>
      <div class="update-toast-text">
        <h4>Mise à jour disponible</h4>
        <p>Une nouvelle version est prête.</p>
      </div>
      <button id="pwa-update-btn">Relancer</button>
    </div>
  `;

  // Injection des styles (Opacités de surface adaptatives)
  const style = document.createElement('style');
  style.innerHTML = `
    #pwa-update-toast {
      position: fixed; bottom: 80px; left: 16px; right: 16px; /* Ajusté pour flotter au-dessus de la bottom-nav */
      max-width: 400px; margin: 0 auto;
      background: rgb(243, 243, 244);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      padding: 14px 18px; border-radius: 20px; z-index: 10001; opacity: 0;
      transform: translateY(100px);
      transition: transform 0.3s cubic-bezier(0.2, 0, 0, 1), opacity 0.25s ease;
      font-family: system-ui, -apple-system, sans-serif;
      border: 1px solid rgba(0, 0, 0, 0.06);
    }
    @media (prefers-color-scheme: dark) {
      #pwa-update-toast {
        background: rgb(31, 31, 31);
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.4);
      }
    }
    #pwa-update-toast.show { transform: translateY(0); opacity: 1; }
    .update-toast-content { display: flex; align-items: center; justify-content: space-between; }
    .update-toast-icon { font-size: 20px; margin-right: 12px; }
    .update-toast-text { flex: 1; text-align: left; }
    .update-toast-text h4 { margin: 0; color: #1f1f1f; font-size: 14px; font-weight: 600; }
    .update-toast-text p { margin: 2px 0 0 0; color: #444746; font-size: 12px; }
    @media (prefers-color-scheme: dark) {
      .update-toast-text h4 { color: #e3e3e3; }
      .update-toast-text p { color: #c4c7c5; }
    }
    #pwa-update-btn {
      background: #0b57d0; color: #ffffff; border: none; padding: 8px 16px;
      border-radius: 100px; font-size: 13px; font-weight: 600; cursor: pointer;
      transition: background 0.2s;
    }
    @media (prefers-color-scheme: dark) {
      #pwa-update-btn { background: #a8c7fa; color: #1f1f1f; }
    }
  `;

  document.head.appendChild(style);
  document.body.appendChild(toast);

  setTimeout(() => toast.classList.add('show'), 100);

  document.getElementById('pwa-update-btn').addEventListener('click', () => {
    if (waitingWorker) {
      waitingWorker.postMessage({ action: 'skipWaiting' });
    }
  });
}

/* ==========================================================================
   3. GESTION DU BOUTON D'INSTALLATION PERSONNALISÉ (PWA)
   ========================================================================== */
window.addEventListener('beforeinstallprompt', (e) => {
  e.preventDefault();
  deferredPrompt = e;
  
  if (elements.installBtn && !window.matchMedia('(display-mode: standalone)').matches) {
    elements.installBtn.style.display = 'flex';
  }
});

if (elements.installBtn) {
  elements.installBtn.addEventListener('click', async (e) => {
    e.preventDefault();
    if (!deferredPrompt) return;
    
    elements.installBtn.style.display = 'none';
    deferredPrompt.prompt();
    
    const { outcome } = await deferredPrompt.userChoice;
    console.log(`PWA : Choix d'installation utilisateur : ${outcome}`);
    
    if (outcome === 'accepted') {
      sendPwaNotification("Merci ! 🎉", "L'application s'installe sur votre appareil.");
    }
    deferredPrompt = null;
  });
}

window.addEventListener('appinstalled', () => {
  console.log('PWA : Application installée avec succès !');
  if (elements.installBtn) {
    elements.installBtn.style.display = 'none';
  }
});

/* ==========================================================================
   4. SYSTÈME DE NOTIFICATIONS ET CENTRALISATION DES FLUX
   ========================================================================== */
function handleNotificationPermissionFlow() {
  if (!('Notification' in window) || !('serviceWorker' in navigator)) return;

  const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

  if (!isStandalone) {
    console.log("PWA : Mode navigateur classique. Demande de notification masquée.");
    return;
  }

  if (Notification.permission === 'default' && !localStorage.getItem(CONFIG.storageKey)) {
    if (elements.onboarding) {
      setTimeout(() => {
        elements.onboarding.style.display = 'block';
      }, 1500);
    }
  }
}

if (elements.btnAcceptNotif) {
  elements.btnAcceptNotif.addEventListener('click', () => {
    if (elements.onboarding) elements.onboarding.style.display = 'none';

    Notification.requestPermission().then((permission) => {
      if (permission === 'granted') {
        sendPwaNotification("Notifications activées ! 🚀", "Vous recevrez désormais nos alertes directement.");
      }
    });
  });
}

if (elements.btnRefuseNotif) {
  elements.btnRefuseNotif.addEventListener('click', () => {
    if (elements.onboarding) elements.onboarding.style.display = 'none';
    localStorage.setItem(CONFIG.storageKey, 'true');
  });
}

/**
 * Envoie de la notification PWA via Service Worker (Arrière-plan / Avant-plan sécurisé)
 */
function sendPwaNotification(title, body, url = '/') {
  if (!('Notification' in window) || Notification.permission !== 'granted') return;

  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.ready.then((registration) => {
      registration.showNotification(title, {
        body: body,
        icon: CONFIG.icon,
        badge: CONFIG.badge,
        vibrate: [200, 100, 200],
        requireInteraction: false,
        data: { url: url }
      });
    }).catch(err => console.error("Erreur d'envoi de la notification via Service Worker:", err));
  }
}

/* ==========================================================================
   5. GESTION DU MODE EN LIGNE / HORS-LIGNE EN TEMPS RÉEL
   ========================================================================== */
function initOnlineStatusManager() {
  let offlineBanner = document.getElementById('offline-banner');
  
  if (!offlineBanner) {
    offlineBanner = document.createElement('div');
    offlineBanner.id = 'offline-banner';
    offlineBanner.innerHTML = '⚠️ Mode hors-ligne actif (Navigation limitée)';
    offlineBanner.style = `
      position: fixed; top: 0; left: 0; right: 0;
      background-color: #f29900; color: white; text-align: center;
      padding: 8px; font-weight: bold; font-size: 14px;
      z-index: 9999; display: none; box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    `;
    document.body.appendChild(offlineBanner);
  }

  function updateStatus(e) {
    if (navigator.onLine) {
      offlineBanner.style.display = 'none';
      if (e && e.type === 'online') {
        sendPwaNotification("Connexion rétablie 🌐", "Vous êtes de nouveau en ligne.");
      }
    } else {
      offlineBanner.style.display = 'block';
      sendPwaNotification("Mode hors-ligne ⚠️", "Connexion perdue. La navigation s'appuie sur le cache.");
    }
  }

  window.addEventListener('online', updateStatus);
  window.addEventListener('offline', updateStatus);
  updateStatus();
}

/* ==========================================================================
   6. ACTIONS INITIALISÉES AU CHARGEMENT DU DOM
   ========================================================================== */
document.addEventListener('DOMContentLoaded', () => {
  handleNotificationPermissionFlow();
  initOnlineStatusManager();

  const isFirefox = navigator.userAgent.toLowerCase().includes('firefox');
  const isMobile = /Mobi|Android|iPhone/i.test(navigator.userAgent);
  if (isFirefox && !isMobile) {
    console.log("PWA Info : Firefox Desktop ne gère pas l'installation directe standard.");
  }

  // 1. Synchronisation dynamique des classes de navigation actives
  const currentPath = window.location.pathname;
  document.querySelectorAll('nav a').forEach(link => {
    if (link.getAttribute('href') === currentPath) {
      link.classList.add('active');
    }
  });

  // Gestion spécifique du hash d'ancrage #contacts
  if (window.location.hash === '#contacts') {
    document.querySelectorAll('.nav-item').forEach(link => link.classList.remove('active'));
    const contactLink = document.querySelector('.nav-item[data-anchor="contacts"]');
    if (contactLink) contactLink.classList.add('active');
  }

  // 2. Interactivité du sous-menu de gestion du compte utilisateur (Profil/Paramètres)
  if (elements.menuTrigger && elements.submenu) {
    elements.menuTrigger.addEventListener('click', (e) => {
      e.stopPropagation(); // Évite la fermeture immédiate sur le clic parent
      elements.submenu.classList.toggle('open');
    });

    // Fermeture automatique en cas de clic en dehors de la zone du sous-menu
    document.addEventListener('click', (e) => {
      if (!elements.menuTrigger.contains(e.target)) {
        elements.submenu.classList.remove('open');
      }
    });
  }

  // 3. Soumission sécurisée des formulaires en ligne / hors-ligne
  if (elements.contactForm) {
    elements.contactForm.addEventListener('submit', (e) => {
      if (!navigator.onLine) {
        e.preventDefault();
        sendPwaNotification("Échec de l'envoi ❌", "Impossible d'envoyer le formulaire en mode hors-ligne.");
      }
    });
  }

  // 4. Gestion des tiroirs de navigation latéraux (App Drawers) si présents
  if (elements.menuToggleBtn && elements.appDrawer && elements.drawerOverlay) {
    elements.menuToggleBtn.addEventListener('click', () => {
      elements.appDrawer.classList.add('open');
      elements.drawerOverlay.classList.add('open');
    });

    elements.drawerOverlay.addEventListener('click', () => {
      elements.appDrawer.classList.remove('open');
      elements.drawerOverlay.classList.remove('open');
    });
  }
});