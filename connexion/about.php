<!-- Bouton flottant et fenêtre d'information -->
<div class="floating-button" id="floatingButton">
    </div>
    <div class="about-container" id="aboutContainer">
        <div class="about-text">
            <h3>À propos de la nouvelle version 4 de la page de connexion / inscription</h3>
            <p>Bienvenue sur la version 4 de notre page de connexion / inscription. Cette version intègre des
                améliorations pour une meilleure expérience utilisateur. Elle permet une gestion plus fluide des
                sessions, une interface plus moderne et des fonctionnalités optimisées pour la connexion et
                l'inscription des utilisateurs.</p>
        </div>
    </div>

    <script>
        // Script pour gérer l'ouverture et la fermeture du texte "À propos"
        document.getElementById('floatingButton').addEventListener('click', function () {
            const aboutContainer = document.getElementById('aboutContainer');
            aboutContainer.classList.toggle('open');
        });
    </script>