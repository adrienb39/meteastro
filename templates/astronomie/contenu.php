<?php
$article = $article ?? [];
$jsonPlaylist = $jsonPlaylist ?? '[]';

$hudFeed = $article['hud_feed_id'] ?? '';
$isAurora = ($hudFeed === 'astro_nebula_animated');
$isMatrixGrid = ($hudFeed === 'astro_constellations_animated');
$isStarfield = ($hudFeed === 'astro_stars_animated');
$isDeepSpace = ($hudFeed === 'astro_deep_space_static');
$isSupernova = ($hudFeed === 'astro_supernova_static');
$isBlueprint = ($hudFeed === 'astro_blueprint_static');

// Préparation du style CSS dynamique pour le background
$bgDynamicStyle = "";
if (!empty($article['background_img'])) {
    $bgUrl = "/uploads/" . htmlspecialchars($article['background_img']);
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
            background: rgba(5, 7, 10, 0.7); 
            z-index: -2;
        }
    ";
}
?>

    <link rel="stylesheet" href="/assets/css/divers.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Inter:wght@300;400;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --space-dark: #05070a;
            --nebula-purple: #6d28d9;
            --star-cyan: #00d4ff;
            --glass: rgba(15, 23, 42, 0.7);
            --glass-border: rgba(255, 255, 255, 0.1);
            --neon-blue: #00f2ff;
            --neon-purple: #bc13fe;
        }

        body {
            background: var(--space-dark);
            color: #e0e0e0;
            font-family: 'Inter', sans-serif;
            background-image: radial-gradient(circle at 50% 50%, #161b22 0%, #05070a 100%);
            overflow-x: hidden;
            margin: 0;
        }

        <?= $bgDynamicStyle ?>

        .stars-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.3;
            pointer-events: none;
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
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.6);
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
            object-fit: contain;
            filter: brightness(0.8);
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
            z-index: 2;
            box-shadow: 0 0 20px rgba(0, 212, 255, 0.4);
        }

        .article-header {
            padding: 40px;
            text-align: center;
            border-bottom: 1px solid var(--glass-border);
        }

        .article-title {
            font-family: 'Orbitron', sans-serif;
            color: #fff;
            margin-bottom: 20px;
        }

        .article-meta {
            display: flex;
            justify-content: center;
            gap: 30px;
            color: var(--star-cyan);
            font-size: 0.9rem;
        }

        .article-content {
            padding: 40px;
            line-height: 1.8;
            font-size: 1.1rem;
            color: #ccd6f6;
        }

        /* Styles de la Galerie */
        .article-gallery {
            padding: 0 40px 40px;
            border-top: 1px solid var(--glass-border);
        }

        .gallery-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.2rem;
            color: var(--star-cyan);
            margin: 30px 0 20px;
            letter-spacing: 2px;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
        }

        .gallery-item {
            border-radius: 12px;
            overflow: hidden;
            aspect-ratio: 1 / 1;
            border: 1px solid var(--glass-border);
            transition: 0.3s ease;
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: 0.5s;
        }

        .gallery-item:hover {
            transform: translateY(-5px);
            border-color: var(--star-cyan);
            box-shadow: 0 5px 15px rgba(0, 212, 255, 0.3);
        }

        .gallery-item:hover img {
            transform: scale(1.1);
        }

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

        .feed-animated-simulation {
            width: 100vw;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: -2;
            pointer-events: none;
        }

        .feed-animated-simulation.aurora {
            opacity: 0.35;
            background: radial-gradient(circle at 30% 30%, var(--neon-purple), transparent 65%),
                        radial-gradient(circle at 70% 70%, var(--neon-blue), transparent 65%);
            background-size: 200% 200%;
            animation: hudAurora 10s ease infinite;
        }

        @keyframes hudAurora {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .feed-animated-simulation.matrix-grid {
            opacity: 0.25; /* Légèrement réduit pour ne pas agresser les yeux derrière le texte */
            background-image: linear-gradient(rgba(0, 242, 255, 0.1) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(0, 242, 255, 0.1) 1px, transparent 1px);
            background-size: 20px 20px; /* Ajusté à 20px pour un rendu plus aéré sur grand écran */
            animation: hudMatrixGrid 8s linear infinite;
        }

        @keyframes hudMatrixGrid {
            from { background-position: 0 0; }
            to { background-position: 0 100px; }
        }

        .feed-animated-simulation.starfield {
            opacity: 0.4; /* Ajustement de l'opacité globale pour le fond */
            background: radial-gradient(white, rgba(255, 255, 255, .2) 2px, transparent 40px),
                        radial-gradient(white, rgba(255, 255, 255, .15) 1px, transparent 30px);
            background-size: 80px 80px;
            background-position: 0 0, 40px 40px;
            animation: hudStarfield 12s linear infinite;
        }

        @keyframes hudStarfield {
            from { background-position: 0 0, 40px 40px; }
            to { background-position: 80px 80px, 120px 120px; }
        }

        .feed-static-simulation.deep-space {
            opacity: 0.8;
            background: radial-gradient(circle at 80% 20%, rgba(0, 242, 255, 0.15), transparent 50%),
                        radial-gradient(circle at 20% 80%, rgba(188, 19, 254, 0.15), transparent 50%),
                        #070a13;
        }

        .feed-static-simulation.supernova-static {
            opacity: 0.6; /* Évite que l'éclat ambré central ne fatigue la lecture */
            background: radial-gradient(circle at 50% 50%, rgba(255, 204, 0, 0.2), transparent 70%),
                        radial-gradient(rgba(255, 255, 255, 0.4) 1px, transparent 1px),
                        #050811;
            background-size: 100% 100%, 15px 15px;
        }

        .feed-static-simulation.hud-blueprint {
            opacity: 0.4; /* Ajusté pour garder le texte du blog lisible au premier plan */
            background-image: linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 15px 15px;
            background-position: center;
            border: 1px solid rgba(0, 242, 255, 0.1);
        }

        .article-card.aurora-active {
            border: 1px solid rgba(109, 40, 217, 0.4);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.6), 0 0 30px rgba(109, 40, 217, 0.15);
        }

        .article-card.matrix-active {
            border: 1px solid rgba(0, 212, 255, 0.3);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.6), 0 0 30px rgba(0, 212, 255, 0.15);
        }

        .article-card.starfield-active {
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.6), 0 0 30px rgba(255, 255, 255, 0.08);
        }

        .article-card.deep-space-active {
            border: 1px solid rgba(0, 242, 255, 0.2);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.6), 0 0 35px rgba(7, 10, 19, 0.5);
        }

        .article-card.supernova-active {
            border: 1px solid rgba(255, 204, 0, 0.3);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.6), 0 0 30px rgba(255, 204, 0, 0.15);
        }

        .article-card.blueprint-active {
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.6), inset 0 0 20px rgba(0, 242, 255, 0.05);
        }
    </style>
    <?php if ($isAurora): ?>
        <div class="feed-animated-simulation aurora"></div>
    <?php elseif ($isMatrixGrid): ?>
        <div class="feed-animated-simulation matrix-grid"></div>
    <?php elseif ($isStarfield): ?>
        <div class="feed-animated-simulation starfield"></div>
    <?php elseif ($isDeepSpace): ?>
        <div class="feed-static-simulation deep-space"></div>
    <?php elseif ($isSupernova): ?>
        <div class="feed-static-simulation supernova-static"></div>
    <?php elseif ($isBlueprint): ?>
        <div class="feed-static-simulation hud-blueprint"></div>
    <?php endif; ?>
    <div class="stars-bg"></div>

    <main class="container-article">
        <a href="/astronomie" class="btn-back">
            <i class="fas fa-chevron-left"></i> RETOUR AU COSMOS
        </a>

        <article class="article-card <?= $isAurora ? 'aurora-active' : ($isMatrixGrid ? 'matrix-active' : ($isStarfield ? 'starfield-active' : ($isDeepSpace ? 'deep-space-active' : ($isSupernova ? 'supernova-active' : ($isBlueprint ? 'blueprint-active' : ''))))) ?>">
            <div class="hero-banner">
                <span class="category-badge"><?= htmlspecialchars($article['title']) ?></span>
                <?php if ($article['show_images']): ?>
                    <img class="hero-img" src="/uploads/<?= htmlspecialchars($article['filename']); ?>"
                        alt="Image de l'article">
                <?php endif; ?>
            </div>

            <header class="article-header">
                <h1 class="article-title"><?= $article['title_contenu'] ?></h1>

                <div class="article-meta">
                    <div class="meta-item">
                        <i class="fa-regular fa-calendar-check"></i>
                        <?= $article['date_astronomie']->format('d M Y') ?>
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

            <?php if (!empty($article['gallery_images'])): ?>
                <section class="article-gallery">
                    <h2 class="gallery-title"><i class="fa-solid fa-images"></i> GALERIE SPATIALE</h2>
                    <div class="gallery-grid">
                        <?php
                        $images = explode(',', $article['gallery_images']);
                        foreach ($images as $img):
                            $img = trim($img);
                            if (empty($img))
                                continue;
                            ?>
                            <div class="gallery-item">
                                <a href="/uploads/<?= htmlspecialchars($img) ?>" target="_blank">
                                    <img src="/uploads/<?= htmlspecialchars($img) ?>" alt="Vue de la galerie"
                                        loading="lazy">
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        </article>
    </main>

    <script src="/assets/js/astronomie.js"></script>
    <script src="/assets/js/divers.js"></script>