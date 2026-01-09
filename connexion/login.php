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
    <title>Connexion | Meteastro Station</title>
    
    <meta name="description" content="Accédez à votre cockpit Meteastro pour suivre les étoiles et la météo.">
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
                        <h2 class="title">COCKPIT ACTIF</h2>
                        <p class="status-text">
                            Transmission stable. Vos identifiants sont déjà synchronisés avec la station.
                        </p>
                        <div class="action-group">
                            <a href="/index.php" class="box-button secondary">RETOUR AU SITE</a>
                            <a href="logout.php" class="box-button logout-btn">DÉCONNEXION</a>
                        </div>
                    </section>

                <?php else: ?>
                    <form action="login.php" method="POST" class="box glass-card animate-in" autocomplete="on" novalidate>
                        <header>
                            <h2 class="title">CONNEXION</h2>
                            <p class="subtitle">Prêt pour le décollage ?</p>
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
                                <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                                <input type="email" name="email" placeholder="Email" 
                                       value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                            </div>

                            <div class="input-field">
                                <i class="fa-solid fa-lock" aria-hidden="true"></i>
                                <input type="password" name="password" placeholder="Mot de passe" required>
                            </div>
                        </div>

                        <div class="forgot-link">
                            <a href="forgot-password.php">Trajectoire perdue ? (Mdp oublié)</a>
                        </div>

                        <button type="submit" name="login" class="box-button btn-glow">
                            INITIALISER LA CONNEXION <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
                        </button>

                        <footer class="form-footer">
                            <p>Pas encore de badge ?</p>
                            <a href="signup.php" class="signup-link">REJOINDRE L'EXPÉDITION</a>
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

    <script src="/js/login.js"></script>
</body>
</html>