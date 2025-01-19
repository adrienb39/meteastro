<?php
require_once __DIR__ . "/User.php";
session_start();
require __DIR__ . "/../../config/connexion_bdd.php";

// Créer la connexion PDO
$pdo = createPdoConnection();

$errors = [];
$successMessage = "";

// Processus d'inscription

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $user = new User(
        $_POST['name'],
        $_POST['email'],
        $_POST['password'],
        $_POST['cpassword'],
        isset($_POST['consent']) ? $_POST['consent'] : null,
        $_POST['software_id'],
        $pdo,
        $_POST['license_key']
    );

    $errors = $user->validate();
    if (empty($errors)) {
        $result = $user->save();
        if (isset($result['success'])) {
            $successMessage = $result['success'];
        } else {
            $errors[] = $result['db'] ?? 'Une erreur s\'est produite.';
        }
    }
}


// Processus de connexion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    // Récupération des données du formulaire de connexion
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Création d'une instance de la classe User pour la connexion
    $user = new User(null, $email, $password, null, null, null, $pdo);
    $errors = $user->login(); // Connexion

    if (isset($errors['success'])) {
        // Connexion réussie, redirection ou message
        header("Location: ../index.php"); // Rediriger vers une page après la connexion
        exit;
    }
}

// Récupérer toutes les clés de licence qui ne sont pas encore associées
$stmt = $pdo->prepare("SELECT license_key FROM licenses WHERE used_license IS NULL");
$stmt->execute();
$licenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr-FR">
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="icon" type="image/png" sizes="16x16" href="../ressources/logo-gestimag2.png">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="theme-color" content="#ffffff">
<script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="connexion.css" />
<link rel="stylesheet" href="information/information.css" />
<title>S'inscrire / Se connecter | Gestimag</title>
<style>
    /* Styles des cartes */
    .software-cards {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        margin-top: 20px;
    }

    .card {
        width: 50%;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .card.selected {
        border-color: #4CAF50;
        box-shadow: 0 8px 16px rgba(76, 175, 80, 0.2);
    }

    .card-content h3 {
        font-size: 1.2em;
        margin-bottom: 10px;
    }

    .card-content p {
        font-size: 1em;
        margin-bottom: 10px;
    }

    .price {
        font-weight: bold;
        font-size: 1.1em;
    }

    /* Style pour l'affichage du logiciel sélectionné */
    .selected-software {
        margin-top: 20px;
        font-size: 1.2em;
    }



    .carousel-container {
        width: 40%;
        /* Vous pouvez ajuster cette largeur selon vos besoins */
        position: absolute;
        /* Pour positionner le carrousel à gauche de la page */
        top: 50%;
        left: 4%;
        /* Aligné à gauche */
        transform: translateY(-50%);
        /* Centrer verticalement */
        height: 70%;
        overflow: hidden;
        z-index: 10;
    }

    .carousel {
        display: flex;
        flex-direction: row;
        height: 100%;
        justify-content: space-between;
        transition: transform 0.3s ease;
        /* Pour ajouter une transition fluide lors du défilement */
    }

    .carousel-item {
        width: 100%;
        height: 100%;
        display: none;
        /* Par défaut, caché */
    }

    .carousel-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 8px;
    }

    .prev-btn,
    .next-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background-color: rgba(0, 0, 0, 0.5);
        color: white;
        border: none;
        font-size: 2em;
        cursor: pointer;
        z-index: 10;
        padding: 10px;
        border-radius: 50%;
    }

    .prev-btn {
        left: 10px;
        /* Positionner la flèche de gauche */
    }

    .next-btn {
        right: 10px;
        /* Positionner la flèche de droite */
    }

    .prev-btn:hover,
    .next-btn:hover {
        background-color: rgba(0, 0, 0, 0.7);
    }

    @media (max-width: 480px) {
        .carousel-container {
            width: 100%;
            left: 0;
        }

        .carousel-item img {
            object-fit: cover;
            /* Adaptation des images sur les petits écrans */
        }

        .carousel-container,
        .carousel {
            height: 250px;
            /* Réduire la hauteur du carrousel sur petit écran */
        }
    }
</style>
</head>

<body>
    <?php if (isset($_SESSION['user_email']) && isset($_SESSION['user_password'])) { ?>
        <div class="container">
            <div class="forms-container">
                <div class="signin-signup">
                    <form class="box sign-in-form" action="" method="post" name="login" novalidate>
                        <h2 class="box-title title">CONNEXION</h2>
                        <div class="input-field-session">Vous êtes déjà connecté ! Vous voulez vous déconnecter ?
                        </div>
                        <a class="box-button btn-session solid" href="logout.php">Déconnexion</a>
                        <p class="social-text">Revenir sur la page d'accueil du Site Web : <a
                                style="text-decoration: none; color: #046280;" href="../index.php">Accueil du
                                Site Web</a></p>
                    </form>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="container">
            <!-- <div class="carousel-container">
                <button class="prev-btn">&#10094;</button>
                <div class="carousel">
                    <div class="carousel-item">
                        <img src=""
                            alt="Image 1">
                    </div>
                    <div class="carousel-item">
                        <img src="" alt="Image 2">
                    </div>
                    <div class="carousel-item">
                        <img src="" alt="Image 3">
                    </div>
                </div>
                <button class="next-btn">&#10095;</button>
            </div> -->

            <div class="forms-container">
                <div class="signin-signup">

                    <!-- Formulaire d'inscription -->
                    <form class="box sign-in-form" id="signup-form" method="POST" novalidate
                        style="<?php echo isset($_POST['signup']) || !isset($_POST['login']) ? 'display: block;' : 'display: none;'; ?>">
                        <h2 class="box-title title">INSCRIPTION</h2>

                        <!-- Barre de progression -->
                        <div class="progress-bar-container">
                            <div class="progress-bar" id="progress-bar"></div>
                        </div>

                        <!-- Affichage des messages de succès ou d'erreur -->
                        <?php if ($successMessage): ?>
                            <div class="success-message">
                                <p><?php echo $successMessage; ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($errors)): ?>
                            <div class="error-message">
                                <p><?php echo implode('<br>', $errors); ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- Etapes du formulaire d'inscription -->
                        <div class="step active" id="step-1">
                            <div class="input-field">
                                <i class="fas fa-user"></i>
                                <input type="text" class="box-input" id="name" name="name" placeholder="Nom de l'entreprise"
                                    required value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>">
                            </div>
                            <button type="button" class="btn-nav" id="next-btn-1">Suivant</button>
                        </div>

                        <div class="step" id="step-2">
                            <div class="input-field">
                                <i class="fas fa-envelope"></i>
                                <input type="email" class="box-input" id="email" name="email" placeholder="Email" required
                                    value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                            </div>
                            <button type="button" class="btn-nav" id="prev-btn-2">Précédent</button>
                            <button type="button" class="btn-nav" id="next-btn-2">Suivant</button>
                        </div>

                        <div class="step" id="step-3">
                            <div class="input-field">
                                <i class="fas fa-lock"></i>
                                <input type="password" class="box-input" id="password" name="password"
                                    placeholder="Mot de passe" required>
                            </div>
                            <button type="button" class="btn-nav" id="prev-btn-3">Précédent</button>
                            <button type="button" class="btn-nav" id="next-btn-3">Suivant</button>
                        </div>

                        <div class="step" id="step-4">
                            <div class="input-field">
                                <i class="fas fa-lock"></i>
                                <input type="password" class="box-input" id="cpassword" name="cpassword"
                                    placeholder="Confirmation du mot de passe" required>
                            </div>
                            <button type="button" class="btn-nav" id="prev-btn-4">Précédent</button>
                            <button type="button" class="btn-nav" id="next-btn-4">Suivant</button>
                        </div>

                        <div class="step" id="step-5">
                            <h2>Sélectionnez un logiciel</h2>
                            <p>Vous pourrez modifié dans votre espace connecté</p>
                            <div class="software-cards">
                                <?php
                                $softwareQuery = "SELECT * FROM logiciels";
                                $softwareResult = $pdo->prepare($softwareQuery);
                                $softwareResult->execute();
                                $softwares = $softwareResult->fetchAll();
                                if ($softwares && count($softwares) > 0) {
                                    foreach ($softwares as $software) {
                                        echo '<div class="card" data-software-id="' . htmlspecialchars($software['id_logiciel']) . '" data-price="' . htmlspecialchars($software['prix']) . '">
                                    <div class="card-content">
                                        <h3>' . htmlspecialchars($software['nom']) . '</h3>
                                        <p>' . htmlspecialchars($software['description']) . '</p>
                                        <p class="price">Prix : ' . htmlspecialchars($software['prix']) . ' €</p>
                                    </div>
                                  </div>';
                                    }
                                } else {
                                    echo "<p>Aucun logiciel trouvé.</p>";
                                }
                                ?>
                                <input type="hidden" name="software_id" id="software_id">
                            </div>
                            <div class="selected-software">
                                <p><strong>Logiciel sélectionné :</strong> <span id="selected-software-name">Aucun</span>
                                </p>
                                <p><strong>Prix :</strong> <span id="selected-price">0.00 €</span></p>
                            </div>
                            <button type="button" class="btn-nav" id="prev-btn-5">Précédent</button>
                            <button type="button" class="btn-nav" id="next-btn-5">Suivant</button>
                        </div>

                        <div class="step" id="step-6">
                            <div class="input-field">
                                <i class="fas fa-id-card"></i>
                                <!-- Sélection de la clé de licence -->
                                <select class="box-input" name="license_key" id="license_key" required>
                                    <option value="">Sélectionnez une clé de licence</option>
                                    <?php if (!empty($licenses)): ?>
                                        <?php foreach ($licenses as $license): ?>
                                            <option value="<?php echo htmlspecialchars($license['license_key']); ?>">
                                                <?php echo htmlspecialchars($license['license_key']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="">Aucune licence disponible</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <button type="button" class="btn-nav" id="prev-btn-6">Précédent</button>
                            <button type="button" class="btn-nav" id="next-btn-6">Suivant</button>
                        </div>


                        <div class="step" id="step-7">
                            <div class="input-field">
                                <input type="checkbox" name="consent" id="consent" required <?php echo isset($consent) && $consent == 1 ? 'checked' : ''; ?>>
                                <label for="consent">J'accepte les <a href="terms.php" target="_blank">termes et
                                        conditions</a>.</label>
                            </div>
                            <button type="button" class="btn-nav" id="prev-btn-7">Précédent</button>
                            <button type="submit" name="signup" class="btn-nav" id="submit-btn">S'inscrire</button>
                        </div>

                        <!-- Basculement vers le formulaire de connexion -->
                        <div class="switch-form">
                            <p>Déjà un compte ? <a href="javascript:void(0);" id="switch-to-login"
                                    style="text-decoration: none; color: #046280;">Se connecter</a></p>
                            <p class="social-text">Revenir sur la page d'accueil du Site Web : <a
                                    style="text-decoration: none; color: #046280;" href="../index.php">Accueil du
                                    Site Web</a></p>
                        </div>
                    </form>

                    <!-- Formulaire de connexion -->
                    <form class="box login-form" id="login-form" method="POST" novalidate
                        style="<?php echo isset($_POST['login']) ? 'display: block;' : 'display: none;'; ?>">
                        <h2 class="box-title title">CONNEXION</h2>

                        <!-- Affichage des messages de succès ou d'erreur -->
                        <?php if (!empty($errors)): ?>
                            <div class="error-message">
                                <p><?php echo implode('<br>', $errors); ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- Formulaire de connexion -->
                        <div class="input-field">
                            <i class="fas fa-envelope"></i>
                            <input type="email" class="box-input" name="email" placeholder="Email" required>
                        </div>
                        <div class="input-field">
                            <i class="fas fa-lock"></i>
                            <input type="password" class="box-input" name="password" placeholder="Mot de passe" required>
                        </div>

                        <button type="submit" name="login" class="btn-nav">Se connecter</button>

                        <!-- Basculement vers le formulaire d'inscription -->
                        <div class="switch-form">
                            <p>Pas encore de compte ? <a href="javascript:void(0);" id="switch-to-signup"
                                    style="text-decoration: none; color: #046280;">S'inscrire</a></p>
                            <p class="social-text">Revenir sur la page d'accueil du Site Web : <a
                                    style="text-decoration: none; color: #046280;" href="../index.php">Accueil du
                                    Site Web</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php } ?>

    <!-- Ajoutez du JavaScript pour gérer le carrousel -->
    <script>
        let currentIndex = 0;
        const items = document.querySelectorAll('.carousel-item');
        const totalItems = items.length;

        // Afficher l'élément actuel
        function showItem(index) {
            items.forEach((item, i) => {
                item.style.display = (i === index) ? 'block' : 'none';
            });
        }

        // Fonction pour passer à l'image suivante
        function nextItem() {
            currentIndex = (currentIndex === totalItems - 1) ? 0 : currentIndex + 1;
            showItem(currentIndex);
        }

        // Fonction pour passer à l'image précédente
        function prevItem() {
            currentIndex = (currentIndex === 0) ? totalItems - 1 : currentIndex - 1;
            showItem(currentIndex);
        }

        // Gérer le bouton précédent
        document.querySelector('.prev-btn').addEventListener('click', () => {
            prevItem(); // Passer à l'image précédente
            resetTimer(); // Réinitialiser le temporisateur
        });

        // Gérer le bouton suivant
        document.querySelector('.next-btn').addEventListener('click', () => {
            nextItem(); // Passer à l'image suivante
            resetTimer(); // Réinitialiser le temporisateur
        });

        // Initialiser le carrousel
        showItem(currentIndex);

        // Fonction de temporisation : changer l'image après 30 secondes
        let timer;
        function startAutoSlide() {
            timer = setInterval(nextItem, 30000); // Changer d'image toutes les 30 secondes
        }

        // Fonction pour réinitialiser le timer lorsque l'utilisateur interagit avec les flèches
        function resetTimer() {
            clearInterval(timer); // Annuler l'ancien timer
            startAutoSlide(); // Démarrer un nouveau timer
        }

        // Lancer le carrousel automatique
        startAutoSlide();
    </script>

    <script>
        let currentStep = 1;
        const totalSteps = 7;
        const progressBar = document.getElementById("progress-bar");

        function showStep(step) {
            const steps = document.querySelectorAll(".step");
            steps.forEach(s => s.classList.remove("active"));
            document.getElementById("step-" + step).classList.add("active");

            const progress = (step - 1) / (totalSteps - 1) * 100;
            progressBar.style.width = progress + "%";
        }

        // Fonction pour naviguer entre les étapes
        document.querySelectorAll(".btn-nav").forEach(button => {
            button.addEventListener("click", function () {
                if (button.id === "next-btn-1" && currentStep === 1 || button.id === "next-btn-2" && currentStep === 2 || button.id === "next-btn-3" && currentStep === 3 || button.id === "next-btn-4" && currentStep === 4 || button.id === "next-btn-5" && currentStep === 5 || button.id === "next-btn-6" && currentStep === 6 || button.id === "next-btn-7" && currentStep === 7) {
                    currentStep++;
                } else if (button.id === "prev-btn-2" && currentStep === 2 || button.id === "prev-btn-3" && currentStep === 3 || button.id === "prev-btn-4" && currentStep === 4 || button.id === "prev-btn-5" && currentStep === 5 || button.id === "prev-btn-6" && currentStep === 6 || button.id === "prev-btn-7" && currentStep === 7) {
                    currentStep--;
                }
                showStep(currentStep);
            });
        });
        // Afficher la première étape du formulaire d'inscription
        showStep(currentStep);

        // Fonction de basculement entre l'inscription et la connexion
        document.getElementById("switch-to-signup").addEventListener("click", function () {
            document.getElementById("signup-form").style.display = "block";
            document.getElementById("login-form").style.display = "none";
        });

        document.getElementById("switch-to-login").addEventListener("click", function () {
            document.getElementById("signup-form").style.display = "none";
            document.getElementById("login-form").style.display = "block";
        });

        // Gérer la sélection du logiciel via JavaScript
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('click', function () {
                // Désélectionner toutes les cartes
                document.querySelectorAll('.card').forEach(c => c.classList.remove('selected'));

                // Sélectionner la carte cliquée
                card.classList.add('selected');

                // Mettre à jour le logiciel sélectionné et son prix
                const softwareName = card.querySelector('h3').textContent;
                const softwarePrice = card.getAttribute('data-price');
                const softwareId = card.getAttribute('data-software-id');

                // Afficher les informations du logiciel sélectionné
                document.getElementById('selected-software-name').textContent = softwareName;
                document.getElementById('selected-price').textContent = softwarePrice + ' €';

                // Ajouter l'ID du logiciel sélectionné dans un champ caché
                document.getElementById('software_id').value = softwareId;
            });
        });
    </script>
</body>

</html>