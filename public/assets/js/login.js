document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('termsModal');
    const openBtn = document.getElementById('openTerms');
    const closeBtn = document.getElementById('closeTerms');
    const acceptBtn = document.getElementById('acceptTermsBtn');

    // Ouvrir le modal
    openBtn.addEventListener('click', (e) => {
        e.preventDefault();
        modal.classList.add('active');
        document.body.style.overflow = 'hidden'; // Empêche le défilement du fond
    });

    // Fermer le modal (Croix ou Bouton Accepter)
    [closeBtn, acceptBtn].forEach(btn => {
        btn.addEventListener('click', () => {
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        });
    });

    // Fermer si clic à l'extérieur
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    });
});