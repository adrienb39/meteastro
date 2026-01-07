<?php
require_once '../../config/connexion_bdd.php';
$db = createPdoConnection();

/**
 * Récupère les articles d'astronomie avec les infos auteurs
 */
function getAstroContent($db)
{
    $sql = "SELECT a.*, u.name 
            FROM astronomie a
            JOIN usertable u ON a.id_users = u.id_users 
            WHERE a.verified = 'y' 
            ORDER BY a.date_astronomie DESC";
    try {
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

$posts = getAstroContent($db);
?>
<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meteastro | Exploration Spatiale</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Inter:wght@300;400;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../CSS/style.css">
    <style>
        :root {
            --deep-space: #05070a;
            --star-gold: #ffcc00;
            --nebula-blue: #00d4ff;
            --glass: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        .astro-body {
            background: var(--deep-space);
            color: #fff;
            font-family: 'Inter', sans-serif;
            margin: 0;
            overflow-x: hidden;
        }

        /* --- ANIMATION ETOILES (Background) --- */
        .stars-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: radial-gradient(ellipse at bottom, #1B2735 0%, #090A0F 100%);
        }

        #stars,
        #stars2,
        #stars3 {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: transparent url('https://www.transparenttextures.com/patterns/stardust.png') repeat;
            animation: move-stars 100s linear infinite;
        }

        @keyframes move-stars {
            from {
                background-position: 0 0;
            }

            to {
                background-position: -10000px 5000px;
            }
        }

        /* --- HERO SECTION --- */
        .astro-hero {
            text-align: center;
            padding: 80px 20px;
        }

        .glow-text {
            font-family: 'Orbitron', sans-serif;
            font-size: 3.5rem;
            letter-spacing: 5px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .glow-text span {
            color: var(--nebula-blue);
            text-shadow: 0 0 20px rgba(0, 212, 255, 0.6);
        }

        /* --- GRID & CARDS --- */
        .astro-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .glass-card {
            background: var(--glass);
            backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .glass-card:hover {
            transform: translateY(-10px) scale(1.02);
            border-color: var(--nebula-blue);
            box-shadow: 0 15px 40px rgba(0, 212, 255, 0.2);
        }

        .card-anchor {
            text-decoration: none;
            color: inherit;
        }

        .card-header {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .glass-card:hover .card-img {
            transform: scale(1.1);
        }

        .category-pill {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--nebula-blue);
            color: #000;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: bold;
            z-index: 2;
            font-family: 'Orbitron', sans-serif;
        }

        .card-content {
            padding: 25px;
        }

        .post-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.2rem;
            margin-bottom: 15px;
            line-height: 1.4;
        }

        .post-excerpt {
            font-size: 0.9rem;
            color: #ccd6f6;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .read-more {
            display: block;
            margin-top: 10px;
            color: var(--nebula-blue);
            font-weight: 600;
        }

        .post-footer {
            display: flex;
            justify-content: space-between;
            border-top: 1px solid var(--glass-border);
            padding-top: 15px;
            font-size: 0.8rem;
            color: #8892b0;
        }

        .meta i {
            color: var(--nebula-blue);
            margin-right: 5px;
        }

        /* --- RESPONSIVE --- */
        @media (max-width: 768px) {
            .glow-text {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body class="astro-body">

    <div class="stars-container">
        <div id="stars"></div>
        <div id="stars2"></div>
        <div id="stars3"></div>
    </div>

    <?php include "../../import_dans_le_php/menu.php"; ?>

    <header class="astro-hero">
        <h1 class="glow-text">Exploration <span>Astronomie</span></h1>
        <p>Voyagez à travers les données du cosmos</p>
    </header>

    <main class="container">
        <div class="astro-grid">
            <?php foreach ($posts as $post): ?>
                <article class="glass-card">
                    <a href="contenu-astronomie.php?id=<?= $post['id'] ?>" class="card-anchor">
                        <div class="card-header">
                            <span class="category-pill"><?= htmlspecialchars($post['title']) ?></span>
                            <img src="../../uploads/<?= $post['filename']; ?>" alt="" class="card-img">
                        </div>

                        <div class="card-content">
                            <h2 class="post-title"><?= htmlspecialchars($post['title_contenu']) ?></h2>

                            <div class="post-excerpt">
                                <?php
                                $text = strip_tags($post['contenu']);
                                echo (strlen($text) > 100) ? substr($text, 0, 100) . '...' : $text;
                                ?>
                                <span class="read-more">Lire la suite <i class="fa-solid fa-arrow-right"></i></span>
                            </div>

                            <div class="post-footer">
                                <div class="meta">
                                    <i class="fa-regular fa-user"></i> <span><?= $post['name'] ?></span>
                                </div>
                                <div class="meta">
                                    <i class="fa-regular fa-calendar-days"></i>
                                    <span><?= date("d.m.y", strtotime($post['date_astronomie'])) ?></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </main>

    <?php include "../../cookie/cookie.php"; ?>
    <?php include "../../import_dans_le_php/footer.php"; ?>

    <script src="../app.js"></script>
</body>

</html>