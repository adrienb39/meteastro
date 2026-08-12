<div class="container-fluid content-section-page py-5" id="contenu-page">
    <div class="container content">
        <div id="img-status">
            <button id="explicitButton" style="cursor: pointer; background: none; border: none; padding: 0;">
                <?= $pageStatus->getContenu(); ?>
            </button>
        </div>
    </div>
</div>
<div id="imageModal">
    <div id="modalContent">
        <div id="modalHeader">
            <div id="imageStatusText"></div>

            <span id="closeModal">&times;</span>
        </div>

        <div id="imageContainer">
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Sélectionne toutes les images à l'intérieur du contenu initial
        const originalImages = document.querySelectorAll('#img-status img');

        const imageModal = document.getElementById('imageModal');
        const imageContainer = document.getElementById('imageContainer');
        const explicitButton = document.getElementById('explicitButton');
        const closeModal = document.getElementById('closeModal');
        const imageStatusText = document.getElementById('imageStatusText');

        const totalImages = originalImages.length;

        // Fonction optimisée pour mettre à jour le statut lors du défilement
        const updateStatus = () => {
            if (totalImages === 0) return;

            // Récupère les images actuellement dans la modale
            const modalImages = imageContainer.querySelectorAll('.modal-image');
            if (modalImages.length === 0) return;

            let currentImageNumber = 1; // Par défaut, la première image
            const containerScrollTop = imageContainer.scrollTop; // Position de défilement

            // On cherche l'image dont le haut est le plus proche de la position de défilement
            modalImages.forEach((img) => {
                // img.offsetTop est la position du haut de l'image par rapport au conteneur scrollable.
                // On ajoute une petite marge (e.g., 50px) pour considérer l'image comme "vue" dès qu'elle apparaît
                if (img.offsetTop <= containerScrollTop + 50) {
                    // Si le haut de l'image est passé la ligne du scroll (ou juste au-dessus), on met à jour.
                    currentImageNumber = parseInt(img.dataset.imageNumber);
                }
            });

            imageStatusText.textContent = `${currentImageNumber} sur ${totalImages}`;
        };

        // Fonction pour nettoyer la modale et retirer l'écouteur de scroll
        const closeModaleHandler = () => {
            imageModal.style.display = "none";
            imageContainer.innerHTML = ''; // Nettoie le conteneur
            imageContainer.removeEventListener('scroll', updateStatus); // Retire l'écouteur
        };

        // --- LOGIQUE D'OUVERTURE ---
        if (explicitButton) {
            explicitButton.addEventListener('click', function () {
                if (totalImages === 0) {
                    alert("Aucune image à afficher trouvée.");
                    return;
                }

                // 1. Préparation et insertion des images
                originalImages.forEach((img, index) => {
                    const newImg = document.createElement('img');
                    newImg.src = img.src;
                    newImg.alt = img.alt;

                    // Ajout des données nécessaires pour la numérotation
                    newImg.classList.add('modal-image');
                    newImg.dataset.imageNumber = index + 1; // Stocke le numéro

                    imageContainer.appendChild(newImg);
                });

                // 2. AFFICHAGE DANS LA MODALE
                imageModal.style.display = "flex";

                // 3. Initialisation et écoute du défilement
                updateStatus(); // Affiche "Image 1 sur N" immédiatement

                // AJOUT de l'écouteur de défilement
                // Utilisation d'un "throttle" pour éviter de surcharger le navigateur à chaque pixel de défilement
                let scrollTimeout;
                imageContainer.addEventListener('scroll', () => {
                    clearTimeout(scrollTimeout);
                    scrollTimeout = setTimeout(updateStatus, 50); // Met à jour max 20 fois par seconde
                });
            });
        }

        // --- Gestion de la Fermeture ---
        closeModal.onclick = closeModaleHandler;

        window.onclick = (event) => {
            if (event.target == imageModal) {
                closeModaleHandler();
            }
        }
    });
</script>