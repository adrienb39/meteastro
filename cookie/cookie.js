/**
 * Meteastro - Gestionnaire de Consentement Cookies
 */
document.addEventListener("DOMContentLoaded", function () {
    // --- Configuration ---
    const COOKIE_NAME = "cookieByMeteastro";
    const EXPIRATION_DAYS = 30;

    // --- Éléments UI ---
    const banner = document.getElementById("cookie-banner");
    const modal = document.getElementById("cookie-modal");
    
    // Boutons de la bannière
    const btnAcceptAll = document.getElementById("accept-all");
    const btnRejectAll = document.getElementById("reject-all");
    const btnManage = document.getElementById("manage-btn");
    
    // Boutons du modal
    const btnSaveSettings = document.getElementById("save-settings");
    const btnCloseModal = document.getElementById("close-modal");

    /**
     * Lit la valeur d'un cookie par son nom
     */
    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i].trim();
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    /**
     * Enregistre le consentement et ferme l'interface proprement
     * @param {Object|String} consentData - Les préférences de l'utilisateur
     */
    function setConsent(consentData) {
        const date = new Date();
        date.setTime(date.getTime() + (EXPIRATION_DAYS * 24 * 60 * 60 * 1000));
        const expires = "; expires=" + date.toUTCString();
        
        // Stockage sous forme de chaîne JSON
        const cookieValue = typeof consentData === 'object' ? JSON.stringify(consentData) : consentData;
        
        // Écriture du cookie
        document.cookie = `${COOKIE_NAME}=${cookieValue}${expires}; path=/; SameSite=Lax`;

        // Animation de sortie
        hideInterface();
    }

    /**
     * Gère la fermeture visuelle de la bannière et du modal
     */
    function hideInterface() {
        if (banner) {
            banner.style.transition = "all 0.4s ease";
            banner.style.opacity = "0";
            banner.style.transform = "translateY(20px)";
            setTimeout(() => { banner.style.display = "none"; }, 400);
        }
        if (modal) {
            modal.style.display = "none";
        }
    }

    // --- Initialisation ---
    function init() {
        // Si le cookie n'existe pas, on affiche la bannière
        if (!getCookie(COOKIE_NAME)) {
            // Petit délai pour laisser le site charger et l'animation se lancer
            setTimeout(() => {
                if (banner) banner.style.display = "block";
            }, 600);
        }
    }

    // --- Événements ---

    // 1. Accepter tout (Bouton principal)
    if (btnAcceptAll) {
        btnAcceptAll.onclick = function() {
            setConsent({ 
                functional: true, 
                analytics: true, 
                ads: true, 
                date: new Date().toISOString() 
            });
        };
    }

    // 2. Tout refuser (Bouton secondaire)
    if (btnRejectAll) {
        btnRejectAll.onclick = function() {
            setConsent({ 
                functional: true, 
                analytics: false, 
                ads: false 
            });
        };
    }

    // 3. Ouvrir les réglages
    if (btnManage) {
        btnManage.onclick = () => {
            if (modal) modal.style.display = "flex";
        };
    }

    // 4. Fermer le modal (Croix ou clic extérieur)
    if (btnCloseModal) {
        btnCloseModal.onclick = () => { modal.style.display = "none"; };
    }
    window.onclick = (event) => {
        if (event.target === modal) modal.style.display = "none";
    };

    // 5. Enregistrer les préférences spécifiques
    if (btnSaveSettings) {
        btnSaveSettings.onclick = function() {
            const analyticsCheck = document.getElementById("check-analytics");
            const adsCheck = document.getElementById("check-ads");

            setConsent({
                functional: true,
                analytics: analyticsCheck ? analyticsCheck.checked : false,
                ads: adsCheck ? adsCheck.checked : false
            });
        };
    }

    /**
     * FONCTION DE DEBUG : Tapez resetMeteastro() dans la console pour faire réapparaître le bandeau
     */
    window.resetMeteastro = function() {
        document.cookie = COOKIE_NAME + '=; Max-Age=-99999999; path=/;';
        location.reload();
    };

    init();
});