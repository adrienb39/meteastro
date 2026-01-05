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
<div class="cookie-wrapper">
    
    <aside id="cookie-banner" class="cookie-banner" role="status" aria-labelledby="banner-title" style="display: none;">
        <div class="cookie-container">
            <header>
                <div class="cookie-icon" aria-hidden="true">🍪</div>
                <h2 id="banner-title">Gestion des cookies</h2>
            </header>
            
            <div class="cookie-content">
                <p>
                    Meteastro utilise des cookies pour optimiser votre navigation stellaire. 
                    Certains sont nécessaires au fonctionnement du vaisseau, d'autres nous aident à cartographier votre expérience. 
                    <a href="#" id="openTerms" class="cookie-link">Politique de confidentialité</a>.
                </p>
            </div>
            
            <div class="cookie-actions">
                <button id="accept-all" class="cookie-btn btn-accept">Accepter tout</button>
                <button id="reject-all" class="cookie-btn btn-reject">Refuser</button>
                <button id="manage-btn" class="cookie-btn btn-manage">Paramétrer</button>
            </div>
        </div>
    </aside>

</div>

<div id="cookie-modal" class="cookie-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-title" style="display: none;">
    <div class="cookie-modal-content">
        <button id="close-modal" class="btn-close-modal" aria-label="Fermer la fenêtre">&times;</button>
        
        <header class="modal-header">
            <h3 id="modal-title">Préférences spatiales</h3>
            <p class="modal-subtitle">Choisissez les données que vous souhaitez partager avec la station.</p>
        </header>
        
        <div class="cookie-settings-list">
            <div class="setting-item">
                <div class="setting-info">
                    <span class="setting-name">Cookies Techniques</span>
                    <span class="setting-desc">Indispensables pour la connexion et la sécurité.</span>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="check-essential" checked disabled>
                </div>
            </div>

            <div class="setting-item">
                <div class="setting-info">
                    <span class="setting-name">Mesures d'Audience</span>
                    <span class="setting-desc">Nous permettent d'améliorer les instruments de bord.</span>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="check-analytics">
                </div>
            </div>

            <div class="setting-item">
                <div class="setting-info">
                    <span class="setting-name">Personnalisation</span>
                    <span class="setting-desc">Contenu adapté à vos précédentes explorations.</span>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="check-ads">
                </div>
            </div>
        </div>

        <footer class="modal-footer">
            <button id="save-settings" class="cookie-btn btn-save">Enregistrer la configuration</button>
        </footer>
    </div>
</div>
</body>
</html>