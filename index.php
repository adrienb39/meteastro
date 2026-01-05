<?php
session_start();
require_once "config/controllerUserData.php";
$db = createPdoConnection();

function getLatestNews($db, $table) {
    try {
        $sql = "SELECT * FROM $table WHERE verified='y' ORDER BY id DESC LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return null;
    }
}
?>
<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meteastro | Dashboard Spatial</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Space+Mono&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --glass: rgba(15, 23, 42, 0.7);
            --border: rgba(255, 255, 255, 0.1);
            --accent: #3b82f6;
        }

        body {
            background-color: #05070a;
            color: #e2e8f0;
            font-family: 'Outfit', sans-serif;
            overflow-x: hidden;
        }

        /* Background étoilé */
        #star-field {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: -1;
            background: radial-gradient(circle at center, #0a0f1a 0%, #05070a 100%);
        }

        /* Style Glassmorphism Bootstrap */
        .glass-card {
            background: var(--glass) !important;
            backdrop-filter: blur(12px);
            border: 1px solid var(--border) !important;
            border-radius: 1.5rem !important;
            transition: all 0.4s ease;
        }

        .glass-card:hover {
            border-color: var(--accent) !important;
            transform: translateY(-5px);
        }

        /* Boites de catégories */
        .category-box {
            position: relative;
            height: 350px;
            overflow: hidden;
            border-radius: 1.5rem;
            display: block;
            text-decoration: none;
        }

        .category-box img {
            width: 100%; height: 100%;
            object-fit: cover;
            transition: transform 0.7s ease;
            filter: brightness(0.5);
        }

        .category-box:hover img {
            transform: scale(1.1);
            filter: brightness(0.7);
        }

        /* Onglets personnalisés */
        .nav-tabs { border: none; }
        .nav-link {
            color: #94a3b8 !important;
            border: none !important;
            font-family: 'Space Mono', monospace;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.8rem;
            padding: 1rem 1.5rem;
        }
        .nav-link.active {
            background: none !important;
            color: var(--accent) !important;
            border-bottom: 2px solid var(--accent) !important;
        }

        /* Inputs de formulaire */
        .form-control {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid var(--border) !important;
            color: white !important;
            border-radius: 0.75rem;
            padding: 0.8rem 1.2rem;
        }
        .form-control:focus {
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 0.25 margin-top: 10px;rem rgba(59, 130, 246, 0.25);
        }

        .btn-astro {
            background: var(--accent);
            border: none;
            border-radius: 0.75rem;
            padding: 1rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
        }
        .btn-astro:hover {
            background: #2563eb;
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.4);
        }

        @keyframes twinkle {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 1; }
        }

        p {
            color: white;
        }
    </style>
</head>
<body>

    <div id="star-field"></div>

    <?php include "import_dans_le_php/menu.php"; ?>

    <div class="container py-5">
        
        <div class="text-center mb-5">
            <span class="badge rounded-pill px-4 py-2" style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #60a5fa;">
                ⚡ Gestimag v1.0-rc1 disponible (2024)
            </span>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-lg-6">
                <a href="/divers/astronomie/astronomie.php" class="category-box glass-card shadow">
                    <img src="ressources/IMG_0191.JPG" alt="Astronomie">
                    <div class="position-absolute bottom-0 start-0 p-4 w-100 bg-gradient">
                        <h2 class="h1 fw-bold text-white mb-1">Astronomie</h2>
                        <p class="text-light-50 mb-0 opacity-75">Explorez les étoiles et le cosmos.</p>
                    </div>
                </a>
            </div>
            <div class="col-lg-6">
                <a href="/divers/meteorologie/meteorologie.php" class="category-box glass-card shadow">
                    <img src="ressources/IMG_0933.jpg" alt="Météorologie">
                    <div class="position-absolute bottom-0 start-0 p-4 w-100 bg-gradient">
                        <h2 class="h1 fw-bold text-white mb-1">Météorologie</h2>
                        <p class="text-light-50 mb-0 opacity-75">Données climatiques en temps réel.</p>
                    </div>
                </a>
            </div>
        </div>

        <div class="row justify-content-center mb-5">
            <div class="col-md-10 col-xl-8">
                <div class="glass-card shadow-lg">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="fw-bold mb-4 d-flex align-items-center">
                            <span class="bg-primary rounded-pill me-3" style="width: 5px; height: 30px;"></span>
                            Flux de données
                        </h3>

                        <ul class="nav nav-tabs mb-4" id="newsTabs" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" id="astro-tab" data-bs-toggle="tab" data-bs-target="#astro-news" type="button">Astronomie</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="meteo-tab" data-bs-toggle="tab" data-bs-target="#meteo-news" type="button">Météorologie</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="newsTabsContent">
                            <div class="tab-pane fade show active" id="astro-news">
                                <?php $astro = getLatestNews($db, 'astronomie'); ?>
                                <?php if($astro): ?>
                                    <h4 class="h5 fw-bold text-white"><?= $astro['title_contenu'] ?></h4>
                                    <p class="text-light"><?= substr($astro['contenu'], 0, 160) ?>...</p>
                                    <a href="/divers/astronomie/astronomie.php" class="btn btn-link p-0 text-primary text-decoration-none fw-bold">DÉCODER LA SUITE →</a>
                                <?php else: ?>
                                    <p class="text-muted italic">Signal non détecté.</p>
                                <?php endif; ?>
                            </div>
                            <div class="tab-pane fade" id="meteo-news">
                                <?php $meteo = getLatestNews($db, 'meteorologie'); ?>
                                <?php if($meteo): ?>
                                    <h4 class="h5 fw-bold text-white"><?= $meteo['title_contenu'] ?></h4>
                                    <p class="text-light"><?= substr($meteo['contenu'], 0, 160) ?>...</p>
                                    <a href="/divers/meteorologie/meteorologie.php" class="btn btn-link p-0 text-primary text-decoration-none fw-bold">DÉCODER LA SUITE →</a>
                                <?php else: ?>
                                    <p class="text-muted italic">Signal non détecté.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center pt-5" id="contacts">
            <div class="col-md-10 col-xl-8">
                <div class="text-center mb-5">
                    <h2 class="display-5 fw-bold text-light">🪐 CONTACT 🪐</h2>
                    <p class="text-secondary">Envoyez une transmission à l'administrateur</p>
                </div>

                <form action="contacts.php" method="post" class="glass-card p-4 p-md-5">
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">IDENTIFIANT</label>
                            <input type="text" name="pseudo" class="form-control" placeholder="Pseudo" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">COORDONNÉES EMAIL</label>
                            <input type="email" name="email" class="form-control" placeholder="email@cosmos.com" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary">MESSAGE</label>
                        <textarea name="message" class="form-control" rows="5" placeholder="Votre transmission..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-astro w-100">Envoyer le signal</button>
                </form>
            </div>
        </div>
    </div>

    <?php include "cookie/cookie.php"; ?>
    <?php include "import_dans_le_php/footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Générateur d'étoiles
        const field = document.getElementById('star-field');
        for (let i = 0; i < 150; i++) {
            const star = document.createElement('div');
            const size = Math.random() * 2 + 'px';
            star.style.position = 'absolute';
            star.style.width = size;
            star.style.height = size;
            star.style.backgroundColor = 'white';
            star.style.left = Math.random() * 100 + '%';
            star.style.top = Math.random() * 100 + '%';
            star.style.borderRadius = '50%';
            star.style.opacity = Math.random();
            star.style.animation = `twinkle ${Math.random() * 3 + 2}s infinite ease-in-out`;
            field.appendChild(star);
        }
    </script>
</body>
</html>