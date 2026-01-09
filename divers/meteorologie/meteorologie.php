<?php
require_once '../../config/connexion_bdd.php';
$db = createPdoConnection();

function getMeteorologieContent($db)
{
    // Utilisation d'une jointure explicite pour plus de performance et de clarté
    $sql = "SELECT m.*, u.name 
            FROM meteorologie m
            INNER JOIN usertable u ON m.id_users = u.id_users 
            WHERE m.verified = 'y' 
            ORDER BY m.date_meteorologie DESC";

    try {
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}
$posts = getMeteorologieContent($db);
?>
<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meteastro | Flux Météorologique</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&family=JetBrains+Mono&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        :root {
            --bg-main: #0a0f18;
            --accent: #00d2ff;
            --accent-glow: rgba(0, 210, 255, 0.4);
            --glass: rgba(255, 255, 255, 0.04);
            --glass-border: rgba(255, 255, 255, 0.1);
            --text-primary: #f1f5f9;
        }

        .weather-theme {
            background: var(--bg-main);
            color: var(--text-primary);
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
        }

        /* Background Animé */
        .weather-bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background:
                radial-gradient(circle at 20% 30%, #1e293b 0%, transparent 40%),
                radial-gradient(circle at 80% 70%, #0f172a 0%, transparent 40%);
            opacity: 0.6;
        }

        /* Header */
        .page-header {
            text-align: center;
            padding: 60px 20px;
        }

        .animate-title {
            font-size: 3.5rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: -1px;
            margin-bottom: 10px;
        }

        .animate-title span {
            background: linear-gradient(to right, #00d2ff, #3a7bd5);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 0 15px var(--accent-glow));
        }

        /* Grille */
        .weather-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 30px;
            padding: 0 40px 60px;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Carte Moderne */
        .weather-card {
            background: var(--glass);
            backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
        }

        .weather-card:hover {
            transform: translateY(-12px);
            border-color: var(--accent);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 0 0 20px var(--accent-glow);
        }

        .card-inner {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .card-visual {
            position: relative;
            height: 220px;
            overflow: hidden;
        }

        .visual-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .weather-card:hover .visual-img {
            transform: scale(1.1);
        }

        .category-tag {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            color: var(--accent);
            padding: 6px 14px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 700;
            z-index: 2;
            border: 1px solid var(--accent);
        }

        /* Body de la carte */
        .card-content {
            padding: 25px;
        }

        .content-title {
            font-size: 1.4rem;
            font-weight: 700;
            line-height: 1.3;
            margin-bottom: 12px;
            color: #fff;
        }

        .content-excerpt {
            font-size: 0.95rem;
            color: #94a3b8;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        /* Footer de la carte */
        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid var(--glass-border);
            padding-top: 15px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, var(--accent), #3a7bd5);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.8rem;
            color: #fff;
        }

        .meta {
            display: flex;
            flex-direction: column;
        }

        .author {
            font-size: 0.85rem;
            font-weight: 600;
        }

        .date {
            font-size: 0.75rem;
            color: #64748b;
        }

        .btn-arrow {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.3s;
        }

        .weather-card:hover .btn-arrow {
            background: var(--accent);
            color: #000;
            transform: rotate(-45deg);
        }

        /* Animations Responsive */
        @media (max-width: 768px) {
            .animate-title {
                font-size: 2.2rem;
            }

            .weather-grid {
                padding: 0 20px;
            }
        }
    </style>
</head>

<body class="weather-theme">

    <div class="weather-bg-overlay"></div>

    <?php include "../../__partials/menu.php"; ?>

    <main class="main-content">
        <header class="page-header">
            <h1 class="animate-title">Flux <span>Météo</span></h1>
            <p>Analyses atmosphériques et prévisions en temps réel.</p>
        </header>

        <div class="weather-grid">
            <?php foreach ($posts as $post): ?>
                <article class="weather-card">
                    <a href="contenu-meteorologie.php?id=<?= $post['id'] ?>" class="card-inner">
                        <div class="card-visual">
                            <div class="category-tag">
                                <i class="fa-solid fa-cloud-bolt"></i> <?= htmlspecialchars($post['title']) ?>
                            </div>
                            <img src="../../uploads/<?= $post['filename']; ?>" alt="" class="visual-img">
                        </div>

                        <div class="card-content">
                            <h2 class="content-title"><?= htmlspecialchars($post['title_contenu']) ?></h2>

                            <p class="content-excerpt">
                                <?php
                                $clean_text = strip_tags($post['contenu']);
                                echo mb_strimwidth($clean_text, 0, 110, "...");
                                ?>
                            </p>

                            <div class="card-footer">
                                <div class="user-info">
                                    <div class="avatar"><?= strtoupper(substr($post['name'], 0, 1)) ?></div>
                                    <div class="meta">
                                        <span class="author"><?= htmlspecialchars($post['name']) ?></span>
                                        <span
                                            class="date"><?= date("d M Y", strtotime($post['date_meteorologie'])) ?></span>
                                    </div>
                                </div>
                                <span class="btn-arrow"><i class="fa-solid fa-arrow-right"></i></span>
                            </div>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </main>

    <?php include "../../cookie/cookie.php"; ?>
    <?php include "../../__partials/footer.php"; ?>

    <script src="/js/divers.js"></script>
</body>

</html>