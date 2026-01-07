<?php
require_once '../../config/connexion_bdd.php';
$db = createPdoConnection();

/**
 * Récupère un article spécifique avec les informations de l'auteur
 */
function getArticle($db, $id)
{
    $sql = 'SELECT a.*, u.name 
            FROM astronomie a 
            JOIN usertable u ON a.id_users = u.id_users 
            WHERE a.id = :id';

    try {
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$article) {
            header('Location: index.php'); // Redirection si l'article n'existe pas
            exit();
        }
        return $article;
    } catch (PDOException $e) {
        die("Erreur de connexion spatiale.");
    }
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $article = getArticle($db, (int) $_GET['id']);
} else {
    die('Erreur : Coordonnées de l\'article manquantes.');
}
?>
<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($article['title_contenu']) ?> | Meteastro</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Inter:wght@300;400;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="/divers/divers.css">
    <link rel="stylesheet" href="../../CSS/style.css">
    <style>
        :root {
            --space-dark: #05070a;
            --nebula-purple: #6d28d9;
            --star-cyan: #00d4ff;
            --glass: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body {
            background: var(--space-dark);
            color: #e0e0e0;
            font-family: 'Inter', sans-serif;
            background-image: radial-gradient(circle at 50% 50%, #161b22 0%, #05070a 100%);
            overflow-x: hidden;
        }

        /* Animation de fond d'étoiles */
        .stars-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://www.transparenttextures.com/patterns/stardust.png');
            z-index: -1;
            opacity: 0.3;
        }

        .container-article {
            max-width: 900px;
            margin: 120px auto 50px;
            padding: 0 20px;
            animation: slideIn 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .article-card {
            background: var(--glass);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        }

        .hero-banner {
            position: relative;
            height: 450px;
            width: 100%;
            overflow: hidden;
        }

        .hero-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.7);
            transition: transform 10s linear;
        }

        .article-card:hover .hero-img {
            transform: scale(1.1);
        }

        .category-badge {
            position: absolute;
            top: 20px;
            left: 20px;
            background: var(--star-cyan);
            color: #000;
            padding: 8px 20px;
            border-radius: 50px;
            font-family: 'Orbitron', sans-serif;
            font-weight: bold;
            font-size: 0.8rem;
            box-shadow: 0 0 20px rgba(0, 212, 255, 0.5);
        }

        .article-header {
            padding: 40px;
            text-align: center;
            border-bottom: 1px solid var(--glass-border);
        }

        .article-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            color: #fff;
            margin-bottom: 20px;
            text-shadow: 0 0 15px rgba(255, 255, 255, 0.2);
        }

        .article-meta {
            display: flex;
            justify-content: center;
            gap: 30px;
            color: var(--star-cyan);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .article-meta i {
            margin-right: 8px;
        }

        .article-content {
            padding: 40px;
            line-height: 1.8;
            font-size: 1.1rem;
            color: #ccd6f6;
        }

        .article-content p {
            margin-bottom: 20px;
        }

        /* Bouton retour animé */
        .btn-back {
            display: inline-block;
            margin-bottom: 20px;
            color: #fff;
            text-decoration: none;
            font-family: 'Orbitron', sans-serif;
            font-size: 0.8rem;
            transition: 0.3s;
        }

        .btn-back:hover {
            color: var(--star-cyan);
            transform: translateX(-5px);
        }
    </style>
</head>

<body>
    <div class="stars-bg"></div>

    <?php include "../../import_dans_le_php/menu.php"; ?>

    <main class="container-article">
        <a href="astronomie.php" class="btn-back">
            <i class="fas fa-chevron-left"></i> RETOUR AU COSMOS
        </a>

        <article class="article-card">
            <div class="hero-banner">
                <span class="category-badge"><?= htmlspecialchars($article['title']) ?></span>
                <img class="hero-img" src="../../uploads/<?= htmlspecialchars($article['filename']); ?>" alt="">
            </div>

            <header class="article-header">
                <h1 class="article-title"><?= htmlspecialchars($article['title_contenu']) ?></h1>

                <div class="article-meta">
                    <div class="meta-item">
                        <i class="fa-regular fa-calendar-check"></i>
                        <?= date("d M Y", strtotime($article['date_astronomie'])) ?>
                    </div>
                    <div class="meta-item">
                        <i class="fa-solid fa-user-astronaut"></i>
                        AUTEUR : <?= htmlspecialchars($article['name']) ?>
                    </div>
                </div>
            </header>

            <section class="article-content">
                <?= nl2br($article['contenu']) ?>
            </section>
        </article>
    </main>

    <?php include "../../cookie/cookie.php"; ?>
    <?php include "../../import_dans_le_php/footer.php"; ?>

    <script src="script.js"></script>
    <script src="../app.js"></script>
</body>

</html>