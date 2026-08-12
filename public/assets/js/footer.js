/**
 * Meteastro - Système de Thème V3 (Sombre par défaut)
 * Gère le basculement vers le mode clair via la classe .lightmode
 */
(function () {
    document.addEventListener("DOMContentLoaded", () => {
        const themeSwitch = document.getElementById("switch");
        const body = document.body;
        const STORAGE_KEY = "meteastro_theme";

        /**
         * Applique le thème aux éléments HTML
         * @param {string} theme - 'dark' ou 'light'
         */
        const applyTheme = (theme) => {
            if (theme === "light") {
                body.classList.add("lightmode");
                // Support Bootstrap 5.3+
                document.documentElement.setAttribute("data-bs-theme", "light");
                localStorage.setItem(STORAGE_KEY, "light");
            } else {
                // Par défaut ou si 'dark', on retire la classe lightmode
                body.classList.remove("lightmode");
                document.documentElement.setAttribute("data-bs-theme", "dark");
                localStorage.setItem(STORAGE_KEY, "dark");
            }
        };

        /**
         * Initialisation : Priorité au stockage, sinon Sombre par défaut
         */
        const init = () => {
            const savedTheme = localStorage.getItem(STORAGE_KEY);
            
            // Si l'utilisateur a déjà choisi un thème, on l'applique
            if (savedTheme) {
                applyTheme(savedTheme);
            } 
            // Sinon, on ne fait rien (le CSS sombre par défaut prend le relais)
            // On force juste l'attribut data-bs-theme pour la cohérence
            else {
                document.documentElement.setAttribute("data-bs-theme", "dark");
            }
        };

        // Lancement immédiat
        init();

        // Gestion du clic sur le switch
        if (themeSwitch) {
            themeSwitch.addEventListener("click", () => {
                // Active les transitions CSS douces
                body.classList.add("is-transitioning");
                
                // Si le body a 'lightmode', on passe au sombre, sinon au clair
                const currentIsLight = body.classList.contains("lightmode");
                applyTheme(currentIsLight ? "dark" : "light");

                // Nettoyage de la classe de transition après l'animation
                setTimeout(() => {
                    body.classList.remove("is-transitioning");
                }, 300);
            });
        }
    });
})();