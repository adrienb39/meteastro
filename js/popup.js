$(function() {
    const $overlay = $('#popup_overlay');

    // OUVERTURE
    $("#popup_open").on('click', function(e) {
        e.preventDefault();
        // 1. On affiche le conteneur en flex
        $overlay.css('display', 'flex').hide().fadeIn(200, function() {
            // 2. On ajoute la classe pour déclencher l'animation CSS (le slide-up)
            $overlay.addClass('active');
        });
    });

    // FERMETURE (Bouton X ou clic sur l'ombre)
    $("#popup_close, #popup_overlay").on('click', function(e) {
        // Empêche la fermeture si on clique sur la carte elle-même
        if (e.target !== this && e.currentTarget === $overlay[0]) return;
        
        $overlay.removeClass('active'); // Retire l'animation
        setTimeout(function() {
            $overlay.fadeOut(300); // Cache le modal après l'animation
        }, 200);
    });

    // Sécurité pour ne pas fermer en cliquant dans le formulaire
    $(".modal-content-wrapper").on('click', function(e) {
        e.stopPropagation();
    });
});