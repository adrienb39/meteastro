<?php session_start(); ?>
<?php
require __DIR__ . "/../config/connexion_bdd.php"; // Votre fichier de connexion à la base de données

// Créer la connexion PDO
$pdo = createPdoConnection();

// Fonction pour générer une clé de licence unique
function generateLicenseKey()
{
    return '1' . strtoupper(bin2hex(random_bytes(32))); // Génère une clé de licence unique
}

// Vérifier si le formulaire a été soumis
$licenseKey = '';

// Ajouter la clé dans la base de données si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Générez la clé de licence
    $licenseKey = generateLicenseKey();

    // Insérer la licence dans la table `licenses`
    $stmt = $pdo->prepare("INSERT INTO licenses (license_key, created_at) VALUES (?, NOW())");
    if ($stmt->execute([$licenseKey])) {
        echo "<p>La clé de licence a été enregistrée dans la base de données.</p>";
    } else {
        echo "<p>Erreur lors de l'enregistrement de la clé de licence.</p>";
    }
}

// Générer un token unique pour chaque session
if (!isset($_SESSION['download_token'])) {
    $_SESSION['download_token'] = bin2hex(random_bytes(32));  // Un token de 64 caractères hexadécimaux
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logiciel pour entreprises, boutiques et ateliers</title>
    <link rel="icon" type="image/png" sizes="16x16" href="ressources/logo-gestimag2.png">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">
    <!--===== Font Awesome =====-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!--===== Main CSS =====-->
    <link rel="stylesheet" href="css/style.css">
    <style>
        .disabled {
            pointer-events: none;
            /* Désactive les clics */
            color: gray;
            /* Change la couleur pour indiquer qu'il est désactivé */
            text-decoration: none;
            /* Supprime le soulignement */
        }

        .description-rapide {
            display: flex;
            justify-content: space-between;
        }
    </style>
    <script>
        // Fonction pour afficher la modale
        function showForm() {
            document.getElementById("overlay").style.display = "block";
        }

        // Fonction pour fermer la modale
        function closeForm() {
            document.getElementById("overlay").style.display = "none";
        }
    </script>
</head>

<body>
    <!--===== Navbar Start =====-->
    <nav class="navbar">
        <div class="container-menu">
            <div class="row justify-content-between">
                <div class="logo"><a href="index.php">
                        <div class="img-logo"></div><span>GESTIMAG</span>
                    </a></div>
                <div class="links">
                    <ul class="menu">
                        <li class="nav-item"><a href="#home" class="nav-link active">Accueil <span></span></a></li>
                        <li class="nav-item"><a href="#fonctionnalites" class="nav-link">Fonctionnalités
                                <span></span></a></li>
                        <li class="nav-item"><a href="#tarifs" class="nav-link">Tarifs <span></span></a></li>
                        <?php if (isset($_SESSION['user_email']) && isset($_SESSION['user_password'])) { ?>
                            <li class="nav-item"><a href="connexion/logout.php" class="nav-link">Déconnexion
                                    <span></span></a></li>
                        <?php } else { ?>
                            <li class="nav-item"><a href="connexion/" class="nav-link">Connexion <span></span></a></li>
                        <?php } ?>
                        <li class="nav-item"><a href="/" class="nav-link">Retour sur Meteastro <span></span></a></li>
                    </ul>
                </div>
                <div class="menu-btn">
                    <i class="fas fa-bars"></i>
                </div>
            </div>
            <?php echo $_SESSION['user_email']; ?>
        </div>
    </nav>
    <!--===== Navbar End =====-->
    <!--===== Home Section Start =====-->
    <section class="home-section" id="home">
        <div class="container">
            <div class="row align-items-center">
                <div class="home-text">
                    <div>
                    <h1 style="color: #046280;">Logiciel de gestion pour<h1 style="color: orange;">boutiques, ateliers
                            et entreprises</h1>
                    </h1>
                    <span>Gagnez du temps au quotidien
                        et connectez-vous à vos fournisseurs !</span>
                    <a href="#fonctionnalites" class="btn btn-1">En savoir +</a>
                    <a href="#contact" class="btn btn-1">Testez Gestimag</a>
                    </div>
                    <div class="description-rapide">
                        <p><i class="fa-solid fa-gauge"></i> Rapide et expert</p>
                        <p><i class="fa-solid fa-maximize"></i> Intuitif et évolutif</p>
                        <p><i class="fa-solid fa-crop-simple"></i> Gestion simplifiée</p>
                    </div>
                </div>
                <div class="home-image">
                    <div class="img-box">
                        <img src="images/thumb.png" alt="">
                    </div>
                </div>
            </div>
        </div>
        <div class="header-hero-shape"></div> <!--===== Hero Shape =====-->
    </section>
    <!--===== Services Section Start =====-->
    <section class="service-section section-padding" id="fonctionnalites">
        <div class="container">
            <div class="row">
                <div class="section-title text-align">
                    <h1 class="main-title" style="color: #046280;">Fonctionnalités adaptées<h1 class="main-title"
                            style="color: orange;">pour boutiques, ateliers et entreprises</h1>
                    </h1>
                    <p class="sub-title">J'ai développé le logiciel Gestimag dont nous rêvions pour vous gagnez du
                        temps.. et J'espère qu'il vous plaira !</p>
                    <ul class="line">
                        <li></li>
                    </ul>
                </div> <!--===== Section Title =====-->
            </div>
            <div class="row">
                <!--===== Service Item Start =====-->
                <div class="service-item">
                    <div class="service-item-inner">
                        <h3>Gestion de stock</h3>
                        <p>Réalisez un inventaire n'a jamais été aussi facile !</p>
                    </div>
                </div>
                <!--===== Service Item End =====-->
                <!--===== Service Item Start =====-->
                <div class="service-item">
                    <div class="service-item-inner">
                        <h3>Statistiques</h3>
                        <p>Pilotez avec des chiffres votre boutique, atelier et entreprise</p>
                    </div>
                </div>
                <!--===== Service Item End =====-->
                <!--===== Service Item Start =====-->
                <div class="service-item">
                    <div class="service-item-inner">
                        <h3>CRM : fichier client</h3>
                        <p>Connaitre et fidélisez vos clients va devenir un jeu d'enfant !</p>
                    </div>
                </div>
                <!--===== Service Item End =====-->
                <!--===== Service Item Start =====-->
                <div class="service-item">
                    <div class="service-item-inner">
                        <h3>Catalogue fournisseurs</h3>
                        <p>Ayez accès à +de 30 fournisseurs et optimisez vos commandes</p>
                    </div>
                </div>
                <!--===== Service Item End =====-->
                <!--===== Service Item Start =====-->
                <div class="service-item">
                    <div class="service-item-inner">
                        <h3>RDV en ligne</h3>
                        <p>Proposez un outil de prise de rdv en ligne intégré à votre site web</p>
                    </div>
                </div>
                <!--===== Service Item End =====-->
                <!--===== Service Item Start =====-->
                <div class="service-item">
                    <div class="service-item-inner">
                        <h3>Gestion trésorie</h3>
                        <p>Gérez votre trésorerie simplement via la visualisation de vos revenus</p>
                    </div>
                </div>
                <!--===== Service Item End =====-->
                <!--===== Service Item Start =====-->
                <div class="service-item">
                    <div class="service-item-inner">
                        <h3>Commandes & inventaire</h3>
                        <p>Accélérez et simplifiez vos commandes et réassorts en 1 clic</p>
                    </div>
                </div>
                <!--===== Service Item End =====-->
                <!--===== Service Item Start =====-->
                <div class="service-item">
                    <div class="service-item-inner">
                        <h3>Vente en ligne - site web </h3>
                        <p>Synchronisez vos stocks et vos commandes de votre site web</p>
                    </div>
                </div>
                <!--===== Service Item End =====-->
            </div>
        </div>
    </section>
    <!--===== Services Section End =====-->
    <!--===== Services Section Start =====-->
    <section class="pricing section-padding" id="tarifs">
        <div class="container">
            <div class="row">
                <div class="section-title text-align">
                    <h5 class="sub-title">Tarifs</h5>
                    <h3 class="main-title">Plans tarifaires</h3>
                    <ul class="line">
                        <li></li>
                    </ul>
                </div> <!--===== Section Title =====-->
            </div>
            
            <div id="overlay">
                <div class="form-container">
                    <button class="close-btn" onclick="closeForm()">X</button>
                    <?php if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_password'])) { ?>
                        <p>Inscrivez-vous pour pouvoir télécharger le logiciel</p>
                    <?php } ?>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="pricing-item">
                    <div class="pricing-plan">
                        <div class="pricing-header">
                            <h3>Tous inclus</h3>
                            <h4>v1.0.0-alpha1</h4>
                            <p>Le Jeudi 14 Novembre 2024</p>
                        </div>
                        <div class="pricing-price">
                            <span class="currency">€</span>
                            <span class="price">0</span>
                            <span class="period">/mois (ht)</span>
                        </div>
                        <div class="pricing-body">
                            <ul>
                                <li><i class="fa fa-check"></i> Accès à toutes les fonctionnalités</li>
                            </ul>
                        </div>
                        <div class="pricing-footer">
                            <?php if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_password'])) { ?>
                                <a href="#tarifs" class="btn-2 disabled" onclick="showForm()">Télécharger</a>
                            <?php } else { ?>
                                <!-- URL avec token -->
                                <a href="download.php?file=Gestimag-1.0.0-alpha1.exe&token=<?php echo $_SESSION['download_token']; ?>"
                                    class="btn-2 disabled">Télécharger</a>
                            <?php } ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--===== Pricing Section End =====-->
    <?php if (isset($_SESSION['user_email']) && isset($_SESSION['user_password'])) { ?>
        <!--===== Contact Section Start =====-->
        <section class="contact-section section-padding" id="contact">
            <div class="container">
                <div class="row">
                    <div class="section-title text-align">
                        <h5 class="sub-title">contact</h5>
                        <h3 class="main-title"></h3>
                        <ul class="line">
                            <li></li>
                        </ul>
                    </div> <!--===== Section Title =====-->
                </div>
                <div class="row">
                    <!--===== contact item start =====-->
                    <!-- <div class="contact-item">
                    <div class="contact-item-inner">
                        <i class="fas fa-phone"></i>
                        <span>Téléphone</span>
                        <p></p>
                    </div>
                </div> -->
                    <!--===== contact item end =====-->
                    <!--===== contact item start =====-->
                    <!-- <div class="contact-item">
                    <div class="contact-item-inner">
                        <i class="fas fa-envelope"></i>
                        <span>Email</span>
                        <p></p>
                    </div>
                </div> -->
                    <!--===== contact item end =====-->
                    <!--===== contact item start =====-->
                    <!-- <div class="contact-item">
                    <div class="contact-item-inner">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Adresse</span>
                        <p></p>
                    </div>
                </div> -->
                    <!--===== contact item end =====-->
                </div>
                <!--===== Contact Form =====-->
                <div class="row">
                    <div class="contact-form">
                        <form action="contact.php" method="post">
                            <div class="row">
                                <div class="w-50">
                                    <div class="input-group">
                                        <input type="text" placeholder="Nom" name="name" class="input-control">
                                    </div>
                                    <div class="input-group">
                                        <input type="email" placeholder="Email" name="email" class="input-control">
                                    </div>
                                    <div class="input-group">
                                        <input type="text" placeholder="Sujet" name="subject" class="input-control">
                                    </div>
                                </div>
                                <div class="w-50">
                                    <div class="input-group">
                                        <textarea class="input-control" placeholder="Message" name="message" id="" cols="30"
                                            rows="10"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="submit-btn">
                                    <button type="submit" class="btn-1">Envoyer</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <?php if ($_SESSION['user_admin'] == 1) { ?>
            <div class="container">
                <h2>Formulaire de génération de clé de licence</h2>

                <form method="POST" action="">
                    <div class="input-field">
                        <label for="license_key">Clé de licence</label>
                        <input type="hidden" id="license_key" name="license_key" placeholder="Clé de licence générée" readonly>
                    </div>

                    <button type="submit" class="btn-nav">Générer la clé</button>
                </form>

                <?php if ($licenseKey): ?>
                    <p>La clé de licence générée est : <strong><?php echo htmlspecialchars($licenseKey); ?></strong></p>
                <?php endif; ?>
            </div>
        <?php } ?>
        <!--===== Contact Section End =====-->
    <?php } ?>
    <!--===== Footer Section Start =====-->
    <footer class="footer">
        <div class="container">
            <div class="row justify-content-center">
                <p class="copyright-text">&copy; <?php echo date("Y") ?> - Gestimag / <span style="color: red;">Propulsé par
                        Meteastro</span></p>
            </div>
        </div>
    </footer>
    <!--===== Footer Section End =====-->
    <!--===== Lightbox Start =====-->
    <div class="lightbox">
        <div class="lightbox-content">
            <div class="lightbox-close">&times;</div>
            <img src="images/portfolio/portfolio-2.jpg" onclick="nextItem()" class="lightbox-img" alt="">
            <div class="lightbox-caption">
                <div class="caption-text"></div>
                <div class="caption-counter"></div>
            </div>
        </div>
        <div class="lightbox-controls">
            <div class="prev-item" onclick="prevItem()"><i class="fa fa-angle-left"></i></div>
            <div class="next-item" onclick="nextItem()"><i class="fa fa-angle-right"></i></div>
        </div>
    </div>
    <!--===== Lightbox End =====-->
    <!--===== Main JS =====-->
    <script src="js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const img = document.querySelector('img');
            if (img) {
                img.addEventListener('contextmenu', function (event) {
                    event.preventDefault();
                });
                img.addEventListener('dragstart', function (event) {
                    event.preventDefault(); // Empêche le glisser-déposer
                });
            }
        });
    </script>
</body>

</html>