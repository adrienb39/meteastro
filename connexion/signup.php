<?php
require_once "../config/controllerUserData.php";

/**
 * Initialisation du thème : 
 * Le site est sombre par défaut. On n'ajoute 'lightmode' que si 
 * le cookie est explicitement défini sur 'light'.
 */
$themeChoice = $_COOKIE['meteastro_theme'] ?? 'dark';
$bodyClass = ($themeChoice === 'light') ? 'lightmode' : '';
?>
<!DOCTYPE html>
<html lang="fr-FR" data-bs-theme="<?php echo ($themeChoice === 'light') ? 'light' : 'dark'; ?>">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inscription | Meteastro Expedition</title>
    
    <meta name="description" content="Rejoignez la station Meteastro. Créez votre compte pour accéder aux données astronomiques et météorologiques.">
    <link rel="icon" type="image/png" href="/ressources/logo.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="/css/connexion.css" />
</head>

<body class="<?php echo $bodyClass; ?>">
    <div class="star-field" aria-hidden="true"></div>
    <div class="glowing-stars" aria-hidden="true"></div>
    <div class="planet" aria-hidden="true"></div>
    <div class="asteroid" aria-hidden="true"></div>

    <main class="container">
        <div class="forms-container">
            <div class="signin-signup">

                <?php if (isset($_SESSION['email'], $_SESSION['password'])): ?>
                    <section class="box glass-card animate-in">
                        <div class="icon-header">
                            <i class="fa-solid fa-user-astronaut"></i>
                        </div>
                        <h2 class="title">ACCÈS REFUSÉ</h2>
                        <p class="status-text">
                            Votre cockpit est déjà actif. Vous ne pouvez pas créer de nouveau compte en étant connecté.
                        </p>
                        <div class="action-group">
                            <a href="/index.php" class="box-button secondary">RETOUR AU BORD</a>
                            <a href="logout.php" class="box-button logout-btn">DÉCONNEXION</a>
                        </div>
                    </section>

                <?php else: ?>
                    <form action="signup.php" method="POST" class="box glass-card animate-in" autocomplete="off" novalidate>
                        <header>
                            <h2 class="title">REJOINDRE</h2>
                            <p class="subtitle">Créez votre badge d'explorateur</p>
                        </header>

                        <?php if (!empty($errors)): ?>
                            <div class="error-alert" role="alert">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <div>
                                    <?php foreach ($errors as $error): ?>
                                        <p><?php echo htmlspecialchars($error); ?></p>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="input-group">
                            <div class="input-field">
                                <i class="fa-solid fa-user" aria-hidden="true"></i>
                                <input type="text" name="name" placeholder="Nom complet" 
                                       value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                            </div>

                            <div class="input-field">
                                <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                                <input type="email" name="email" placeholder="Email" 
                                       value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                            </div>

                            <div class="input-field">
                                <i class="fa-solid fa-lock" aria-hidden="true"></i>
                                <input type="password" name="password" placeholder="Mot de passe" required>
                            </div>

                            <div class="input-field">
                                <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
                                <input type="password" name="cpassword" placeholder="Confirmer mot de passe" required>
                            </div>
                        </div>

                        <div class="consent-container">
                            <label class="checkbox-wrapper">
                                <input type="checkbox" name="consent" id="consent" required>
                                <span class="checkmark"></span>
                                <span class="label-text">
    J'accepte les <a href="#" id="openTerms">Termes et Conditions</a>
</span>
                            </label>
                        </div>

                        <button type="submit" name="signup" class="box-button btn-glow">
                            CRÉER MON COMPTE <i class="fa-solid fa-rocket" aria-hidden="true"></i>
                        </button>

                        <footer class="form-footer">
                            <p>Déjà membre de l'équipage ?</p>
                            <a href="login.php" class="signup-link">CONNEXION AU COCKPIT</a>
                        </footer>

                        <div class="home-return">
                            <a href="/index.php">
                                <i class="fa-solid fa-house"></i> Retour Terre (Accueil)
                            </a>
                        </div>
                    </form>
                <?php endif; ?>

            </div>
        </div>
    </main>

    <div id="termsModal" class="modal-overlay">
    <div class="modal-content glass-card animate-in">
        <div class="modal-header">
            <h2><i class="fa-solid fa-file-contract"></i> Termes et Conditions</h2>
            <button id="closeTerms" class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
            <h3>1. Acceptation des termes</h3>
            <p>En accédant à ce site, vous acceptez d'être lié par ces termes et conditions et toutes les lois et réglementations applicables.</p>
            
            <h3>2. Utilisation du site</h3>
            <p>Vous pouvez utiliser notre site uniquement à des fins légales et d'une manière qui ne porte pas atteinte aux droits des autres utilisateurs.</p>
            
            <h3>3. Propriété intellectuelle</h3>
            <p>Tous les contenus présents sur ce site (textes, graphiques, logos) sont la propriété de Meteastro.</p>
            
            <h3>4. Limitation de responsabilité</h3>
            <p>Meteastro ne peut être tenu responsable des dommages résultant de l'utilisation de ce site.</p>
            
            <h3>5. Modifications</h3>
            <p>Meteastro se réserve le droit de modifier ces termes à tout moment.</p>
        </div>
        <div class="modal-footer">
            <button id="acceptTermsBtn" class="box-button btn-glow">J'AI COMPRIS</button>
        </div>
    </div>
</div>

    <script src="/js/login.js"></script>
</body>
</html>