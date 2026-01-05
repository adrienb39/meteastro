<?php
session_start();
require_once "config/connexion_bdd.php";

// Sécurité : Redirection immédiate si non connecté
if (!isset($_SESSION['email']) || !isset($_SESSION['password'])) {
    header('Location: connexion/login.php');
    exit();
}

// Initialisation Connexion (Force PDO pour la sécurité)
$db = createPdoConnection();

// Détection du thème
$themeChoice = $_COOKIE['meteastro_theme'] ?? 'dark';
?>
<!DOCTYPE html>
<html lang="fr-FR" data-bs-theme="<?= ($themeChoice === 'light') ? 'light' : 'dark'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Espace de création Meteastro - Partagez vos observations astronomiques et météo.">
    <title>Création de Contenu | Meteastro</title>

    <link rel="icon" type="image/png" href="/ressources/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../CSS/style.css" />
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

    <style>
        :root {
            --glass-card: rgba(15, 23, 42, 0.7);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body {
            background-color: #020617;
            /* Fond sombre spatial */
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .glass-container {
            background: var(--glass-card);
            backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            margin-bottom: 2rem;
        }

        /* Personnalisation de l'éditeur pour l'intégrer au design */
        .note-editor.note-frame {
            border: 1px solid var(--glass-border) !important;
            border-radius: 12px !important;
            overflow: hidden;
            background: white !important;
            /* L'éditeur reste blanc pour la lisibilité */
        }

        .note-toolbar {
            background-color: #f8fafc !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }

        .btn-astro {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border: none;
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-astro:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.4);
            color: white;
        }

        .rocket-icon {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }
    </style>
</head>

<body class="<?= ($themeChoice === 'light') ? 'lightmode' : ''; ?>">

    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-7 order-2 order-lg-1">
                <div class="glass-container">
                    <header class="mb-4">
                        <h1 class="display-5 fw-bold text-white mb-2">
                            Nouvelle Publication <i class="fa-solid fa-rocket rocket-icon text-primary"></i>
                        </h1>
                        <p class="text-secondary">Remplissez les formulaires ci-dessous pour faire décoller vos idées.
                        </p>
                    </header>

                    <div class="content-form">
                        <button class="btn-combined" id="popup_open">
                            <div class="btn-content">
                                <i class="fas fa-plus-circle"></i>
                                <span>ACCÈS À L'AJOUT DU CONTENU</span>
                            </div>
                        </button>

                        <style>
                            .btn-combined {
                                position: relative;
                                padding: 3px;
                                /* Épaisseur de la bordure animée */
                                background: transparent;
                                border: none;
                                border-radius: 50px;
                                cursor: pointer;
                                overflow: hidden;
                                display: inline-flex;
                                align-items: center;
                                justify-content: center;
                                transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                                z-index: 1;
                                margin-bottom: 2rem;
                            }

                            /* Bordure animée (Effet Nébuleuse) */
                            .btn-combined::before {
                                content: '';
                                position: absolute;
                                top: -50%;
                                left: -50%;
                                width: 200%;
                                height: 200%;
                                background: conic-gradient(from 0deg,
                                        transparent,
                                        #3b82f6,
                                        /* Bleu */
                                        #ef4444,
                                        /* Rouge */
                                        transparent 60%);
                                animation: rotateGradient 4s linear infinite;
                                z-index: -2;
                            }

                            /* Flou lumineux derrière (Glow) */
                            .btn-combined::after {
                                content: '';
                                position: absolute;
                                inset: 0;
                                background: linear-gradient(45deg, #3b82f6, #ef4444);
                                filter: blur(15px);
                                opacity: 0;
                                transition: opacity 0.4s ease;
                                z-index: -1;
                            }

                            /* Corps du bouton (Effet Glassmorphism) */
                            .btn-content {
                                background: #020617;
                                /* Fond sombre de la nébuleuse */
                                backdrop-filter: blur(10px);
                                padding: 15px 35px;
                                border-radius: 50px;
                                display: flex;
                                align-items: center;
                                gap: 12px;
                                color: #ffffff;
                                font-size: 1.1rem;
                                font-weight: 700;
                                letter-spacing: 1px;
                                width: 100%;
                                height: 100%;
                            }

                            /* Icône interactive */
                            .btn-content i {
                                font-size: 1.3rem;
                                transition: transform 0.5s ease, color 0.4s ease;
                            }

                            /* ÉTATS HOVER (SURVOL) */
                            .btn-combined:hover {
                                transform: scale(1.05) translateY(-3px);
                            }

                            .btn-combined:hover::after {
                                opacity: 0.6;
                                /* Affiche le glow au survol */
                            }

                            .btn-combined:hover .btn-content i {
                                transform: rotate(90deg);
                                color: #3b82f6;
                            }

                            .btn-combined:active {
                                transform: scale(0.98);
                            }

                            /* ANIMATION DE ROTATION */
                            @keyframes rotateGradient {
                                0% {
                                    transform: rotate(0deg);
                                }

                                100% {
                                    transform: rotate(360deg);
                                }
                            }
                        </style>
                    </div>

                    <div class="mt-4 pt-3 border-top border-secondary border-opacity-25">
                        <a href="index.php" class="text-decoration-none text-info">
                            <i class="fa-solid fa-arrow-left me-2"></i> Retour au tableau de bord
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 order-1 order-lg-2 d-none d-lg-block text-center mb-4 mb-lg-0">
                <img src="/ressources/contenu.png" alt="Illustration astronomie"
                    class="img-fluid rounded-4 shadow-lg animate-in" style="max-height: 500px;">
                <div class="mt-4 text-white-50 fst-italic">
                    "L'astronomie est l'école de la patience et de l'humilité."
                </div>
            </div>
        </div>
    </div>
    <?php include "divers/contenu.php"; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="divers/popup.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    <script>
        $(document).ready(function () {
            // Configuration commune Summernote
            const summernoteConfig = {
                height: 250,
                lang: 'fr-FR',
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            };

            $('#contenu-astronomie').summernote({
                summernoteConfig,
                placeholder: '<b>🔭 Partagez vos découvertes stellaires...</b><br>Détaillez vos observations, matériel utilisé, etc.'
            });

            $('#contenu-meteorologie').summernote({
                summernoteConfig,
                placeholder: '<b>☁️ Rapport météorologique...</b><br>Décrivez les phénomènes observés, températures, pressions...'
            });
        });
    </script>
</body>

</html>