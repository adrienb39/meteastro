<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Meteastro : Astronomie / Météorologie</title>
  <meta name="description" content="Meteastro - Site dédié à l'astronomie et à la météorologie.">
  <meta name="author" content="Meteastro">
  <link rel="stylesheet" href="/cookie/cookie.css">
  <script src="/cookie/cookie.js" defer></script>
</head>

<body>
  <!-- Bandeau de consentement cookies -->
  <div class="cookie-wrapper">
    <div class="cookie-banner">
      <header>
        <i class="bx bx-cookie"></i>
        <h2>Gestion des cookies</h2>
      </header>
      <div class="cookie-content">
        <p>Ce site utilise des cookies pour améliorer votre expérience de navigation. Vous pouvez accepter ou gérer vos préférences. <a href="../connexion/terms.php">En savoir plus...</a></p>
      </div>
      <div class="cookie-actions">
        <button id="accept-all" class="cookie-btn accept">Accepter tous les cookies</button>
        <button id="reject-all" class="cookie-btn reject">Refuser tous les cookies</button>
        <button id="manage-preferences" class="cookie-btn manage">Gérer les préférences</button>
      </div>
    </div>
  </div>

  <!-- Popup politique des cookies -->
  <div id="popup-policy" class="modal">
    <div class="modal-content">
      <h3>Politique de confidentialité et cookies</h3>
      <p>Nous utilisons des cookies pour analyser la performance du site, personnaliser le contenu et vous proposer des publicités ciblées. Vous pouvez personnaliser vos choix ci-dessous.</p>
      <div class="cookie-settings">
        <label>
          <input type="checkbox" id="functional-cookies" checked disabled>
          Cookies fonctionnels
        </label>
        <label>
          <input type="checkbox" id="analytics-cookies">
          Cookies analytiques
        </label>
        <label>
          <input type="checkbox" id="advertising-cookies">
          Cookies publicitaires
        </label>
      </div>
      <button id="save-preferences" class="cookie-btn save">Enregistrer mes préférences</button>
      <a href="#" class="close-modal">×</a>
    </div>
  </div>
</body>

</html>