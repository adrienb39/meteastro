<?php
session_start();
require_once '../../config/connexion_bdd.php';
$db = createPdoConnection();

function getArticle($db, $id)
{
    // Sélection incluant gallery_images pour la météo
    $sql = 'SELECT m.*, u.name FROM meteorologie m 
            JOIN users u ON m.id_users = u.id_users 
            WHERE m.id = :id';

    try {
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$article) {
            header("Location: index.php");
            exit;
        }
        return $article;
    } catch (PDOException $e) {
        die("Erreur de connexion aux capteurs météo.");
    }
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $article = getArticle($db, intval($_GET['id']));
} else {
    die('Erreur : Coordonnées manquantes.');
}

// Préparation du background dynamique
$bgDynamicStyle = "";
if (!empty($article['background_img'])) {
    $bgUrl = "../../uploads/" . htmlspecialchars($article['background_img']);
    $bgDynamicStyle = "
        body {
            background-image: url('$bgUrl') !important;
            background-size: cover !important;
            background-attachment: fixed !important;
            background-position: center !important;
        }
        body::after {
            content: '';
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.75); 
            z-index: -2;
        }
    ";
}
?>
<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($article['title_contenu']) ?> | Meteastro</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&family=JetBrains+Mono&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="/css/divers.css">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        :root {
            --weather-blue: #0ea5e9;
            --weather-dark: #0f172a;
            --glass: rgba(30, 41, 59, 0.7);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body {
            background: var(--weather-dark);
            background-image:
                radial-gradient(circle at 10% 20%, rgba(14, 165, 233, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(59, 130, 246, 0.1) 0%, transparent 40%);
            color: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            line-height: 1.6;
            margin: 0;
        }

        <?= $bgDynamicStyle ?>

        .article-container {
            max-width: 1000px;
            margin: 100px auto;
            padding: 0 20px;
            animation: fadeInSlide 0.8s ease-out;
        }

        @keyframes fadeInSlide {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .glass-article {
            background: var(--glass);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
        }

        .article-hero {
            position: relative;
            height: 400px;
            width: 100%;
        }

        .hero-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: brightness(0.8);
            transition: transform 8s ease;
        }

        .glass-article:hover .hero-img {
            transform: scale(1.05);
        }

        .category-floating {
            position: absolute;
            top: 30px;
            left: 30px;
            background: var(--weather-blue);
            color: white;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 0.75rem;
            z-index: 2;
            box-shadow: 0 10px 20px rgba(14, 165, 233, 0.3);
        }

        .article-header {
            padding: 40px 50px 20px;
        }

        .article-title {
            font-weight: 800;
            margin-bottom: 25px;
            color: #fff;
        }

        .article-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px 0;
            border-top: 1px solid var(--glass-border);
            border-bottom: 1px solid var(--glass-border);
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .meta-item i {
            color: var(--weather-blue);
        }

        .article-body {
            padding: 40px 50px;
            font-size: 1.15rem;
            color: #cbd5e1;
        }

        /* --- SECTION GALERIE PHOTO --- */
        .article-gallery {
            padding: 20px 50px 50px;
            border-top: 1px solid var(--glass-border);
        }

        .gallery-label {
            display: flex;
            align-items: center;
            gap: 15px;
            color: var(--weather-blue);
            font-weight: 800;
            text-transform: uppercase;
            font-size: 0.85rem;
            margin-bottom: 25px;
            letter-spacing: 1px;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }

        .gallery-card {
            border-radius: 16px;
            overflow: hidden;
            aspect-ratio: 16 / 10;
            border: 1px solid var(--glass-border);
            background: rgba(0, 0, 0, 0.2);
            transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .gallery-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: 0.6s;
        }

        .gallery-card:hover {
            transform: scale(1.03);
            border-color: var(--weather-blue);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4);
        }

        .gallery-card:hover img {
            filter: brightness(1.1);
        }

        .btn-return {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
            color: var(--weather-blue);
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-return:hover {
            transform: translateX(-5px);
        }

        @media (max-width: 768px) {
            .article-header,
            .article-body,
            .article-gallery {
                padding: 30px 20px;
            }

            .gallery-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>

<body>
    <?php include "../../__partials/menu.php"; ?>

    <div class="article-container">
        <a href="javascript:history.back()" class="btn-return">
            <i class="fa-solid fa-wind"></i> Retour au flux météo
        </a>

        <article class="glass-article">
            <div class="article-hero">
                <div class="category-floating">
                    <i class="fa-solid fa-cloud-bolt"></i> <?= htmlspecialchars($article['title']) ?>
                </div>
                <?php if ($article['show_images']): ?>
                    <img class="hero-img" src="../../uploads/<?= htmlspecialchars($article['filename']); ?>"
                        alt="Image de couverture">
                <?php endif; ?>
            </div>

            <header class="article-header">
                <h1 class="article-title"><?= htmlspecialchars($article['title_contenu']) ?></h1>

                <div class="article-meta">
                    <div class="meta-item">
                        <i class="fa-regular fa-calendar"></i>
                        <span>Publié le <?= date("d/m/Y", strtotime($article['date_meteorologie'])) ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fa-regular fa-user"></i>
                        <span>Par <strong><?= htmlspecialchars($article['name']) ?></strong></span>
                    </div>
                    <div class="meta-item">
                        <i class="fa-solid fa-clock"></i>
                        <span><?= date("H:i", strtotime($article['date_meteorologie'])) ?></span>
                    </div>
                </div>
            </header>

            <div class="article-body">
                <?= nl2br($article['contenu']) ?>
            </div>

            <?php if (!empty($article['gallery_images'])): ?>
                <section class="article-gallery">
                    <div class="gallery-label">
                        <i class="fa-solid fa-camera-retro"></i> Captures du phénomène
                    </div>
                    <div class="gallery-grid">
                        <?php
                        $imgs = explode(',', $article['gallery_images']);
                        foreach ($imgs as $img):
                            $img = trim($img);
                            if (empty($img))
                                continue;
                            ?>
                            <div class="gallery-card">
                                <a href="../../uploads/<?= htmlspecialchars($img) ?>" target="_blank">
                                    <img src="../../uploads/<?= htmlspecialchars($img) ?>" alt="Observation météo"
                                        loading="lazy">
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        </article>
    </div>

    <?php include "../../cookie/cookie.php"; ?>
    <?php include "../../__partials/footer.php"; ?>

    <script src="/js/meteorologie.js"></script>
    <script src="/js/divers.js"></script>
</body>

</html>