<?php
require_once '../../config/connexion_bdd.php';
$db = createPdoConnection();

function getArticle($db, $id)
{
    // Utilisation d'un alias et d'une jointure propre
    $sql = 'SELECT m.*, u.name FROM meteorologie m 
            JOIN usertable u ON m.id_users = u.id_users 
            WHERE m.id = :id';

    $stmt = $db->prepare($sql);
    $stmt->execute([':id' => $id]);

    if ($article = $stmt->fetch(PDO::FETCH_ASSOC)) {
        return $article;
    } else {
        header("Location: index.php"); // Redirige si l'article n'existe pas
        exit;
    }
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $article = getArticle($db, intval($_GET['id']));
} else {
    die('Erreur : Coordonnées manquantes.');
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
            --glass: rgba(255, 255, 255, 0.03);
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
        }

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
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        /* Banner & Visuals */
        .article-hero {
            position: relative;
            height: 400px;
            width: 100%;
        }

        .hero-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.8);
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
            letter-spacing: 1px;
            box-shadow: 0 10px 20px rgba(14, 165, 233, 0.3);
        }

        /* Content Styling */
        .article-header {
            padding: 40px 50px 20px;
        }

        .article-title {
            font-size: 2.8rem;
            font-weight: 800;
            margin-bottom: 25px;
            background: linear-gradient(to right, #fff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
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

        .article-body p {
            margin-bottom: 1.5rem;
        }

        /* Back Button */
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
            filter: brightness(1.2);
        }

        @media (max-width: 768px) {
            .article-title {
                font-size: 1.8rem;
            }

            .article-header,
            .article-body {
                padding: 30px 20px;
            }

            .article-hero {
                height: 250px;
            }
        }
    </style>

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
                <img class="hero-img" src="../../uploads/<?= htmlspecialchars($article['filename']); ?>" alt="">
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
        </article>
    </div>

    <?php include "../../cookie/cookie.php"; ?>
    <?php include "../../__partials/footer.php"; ?>

    <script src="/js/meteorologie.js"></script>
    <script src="/js/divers.js"></script>
</body>

</html>