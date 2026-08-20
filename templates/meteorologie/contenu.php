<?php
$article = $article ?? [];
$escape = static fn($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$date = $article['date_meteorologie'] ?? null;
$dateObject = $date instanceof DateTimeInterface ? $date : ($date ? new DateTimeImmutable((string) $date) : null);
$background = !empty($article['background_img']) ? '/uploads/' . rawurlencode($article['background_img']) : '';
?>
<link
    href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&family=JetBrains+Mono&display=swap"
    rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    :root {
        --weather-blue: #0ea5e9;
        --weather-dark: #0f172a;
        --glass: rgba(30, 41, 59, .7);
        --glass-border: rgba(255, 255, 255, .1)
    }

    body {
        background: var(--weather-dark);
        background-image: radial-gradient(circle at 10% 20%, rgba(14, 165, 233, .15), transparent 40%), radial-gradient(circle at 90% 80%, rgba(59, 130, 246, .1), transparent 40%);
        color: #f8fafc;
        font-family: 'Plus Jakarta Sans', sans-serif;
        line-height: 1.6;
        margin: 0
    }

    .article-container {
        max-width: 1000px;
        margin: 100px auto;
        padding: 0 20px;
        animation: fadeInSlide .8s ease-out
    }

    .glass-article {
        background: var(--glass);
        backdrop-filter: blur(16px);
        border: 1px solid var(--glass-border);
        border-radius: 32px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, .6)
    }

    .article-hero {
        position: relative;
        height: 400px;
        width: 100%
    }

    .hero-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        filter: brightness(.8);
        transition: transform 8s ease
    }

    .glass-article:hover .hero-img {
        transform: scale(1.05)
    }

    .category-floating {
        position: absolute;
        top: 30px;
        left: 30px;
        background: var(--weather-blue);
        color: #fff;
        padding: 8px 20px;
        border-radius: 50px;
        font-weight: 800;
        text-transform: uppercase;
        font-size: .75rem;
        z-index: 2
    }

    .article-header {
        padding: 40px 50px 20px
    }

    .article-title {
        font-weight: 800;
        margin-bottom: 25px;
        color: #fff
    }

    .article-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        padding: 20px 0;
        border-top: 1px solid var(--glass-border);
        border-bottom: 1px solid var(--glass-border)
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #94a3b8;
        font-size: .9rem
    }

    .meta-item i {
        color: var(--weather-blue)
    }

    .article-body {
        padding: 40px 50px;
        font-size: 1.15rem;
        color: #cbd5e1;
        white-space: pre-line
    }

    .article-gallery {
        padding: 20px 50px 50px;
        border-top: 1px solid var(--glass-border)
    }

    .gallery-label {
        display: flex;
        align-items: center;
        gap: 15px;
        color: var(--weather-blue);
        font-weight: 800;
        text-transform: uppercase;
        font-size: .85rem;
        margin-bottom: 25px
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px
    }

    .gallery-card {
        border-radius: 16px;
        overflow: hidden;
        aspect-ratio: 16/10;
        border: 1px solid var(--glass-border);
        background: rgba(0, 0, 0, .2);
        transition: .3s
    }

    .gallery-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: .6s
    }

    .gallery-card:hover {
        transform: scale(1.03);
        border-color: var(--weather-blue)
    }

    .btn-return {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 30px;
        color: var(--weather-blue);
        text-decoration: none;
        font-weight: 600;
        transition: .3s
    }

    .btn-return:hover {
        transform: translateX(-5px)
    }

    @keyframes fadeInSlide {
        from {
            opacity: 0;
            transform: translateY(30px)
        }

        to {
            opacity: 1;
            transform: translateY(0)
        }
    }

    @media(max-width:768px) {

        .article-header,
        .article-body,
        .article-gallery {
            padding: 30px 20px
        }

        .gallery-grid {
            grid-template-columns: repeat(2, 1fr)
        }

        .article-hero {
            height: 280px
        }
    }
</style>
<div class="article-container" <?= $background ? ' style="background-image:url(' . $escape($background) . ');background-size:cover;background-attachment:fixed;background-position:center"' : '' ?>><a href="/meteorologie"
        class="btn-return"><i class="fa-solid fa-wind"></i> Retour au flux météo</a>
    <article class="glass-article">
        <div class="article-hero">
            <div class="category-floating"><i class="fa-solid fa-cloud-bolt"></i> <?= $escape($article['title']) ?>
            </div><?php if (!empty($article['show_images'])): ?><img class="hero-img"
                    src="/uploads/<?= $escape($article['filename']) ?>" alt="Image de couverture"><?php endif; ?>
        </div>
        <header class="article-header">
            <h1 class="article-title"><?= $escape($article['title_contenu']) ?></h1>
            <div class="article-meta">
                <div class="meta-item"><i class="fa-regular fa-calendar"></i><span>Publié le
                        <?= $dateObject?->format('d/m/Y') ?? '-' ?></span></div>
                <div class="meta-item"><i class="fa-regular fa-user"></i><span>Par
                        <strong><?= $escape($article['name']) ?></strong></span></div>
                <div class="meta-item"><i
                        class="fa-solid fa-clock"></i><span><?= $dateObject?->format('H:i') ?? '-' ?></span></div>
            </div>
        </header>
        <div class="article-body"><?= nl2br($article['contenu']) ?></div>
        <?php if (!empty($article['gallery_images'])): ?>
            <section class="article-gallery">
                <div class="gallery-label"><i class="fa-solid fa-camera-retro"></i> Captures du phénomène</div>
                <div class="gallery-grid">
                    <?php foreach (explode(',', $article['gallery_images']) as $image):
                        $image = trim($image);
                        if ($image === '')
                            continue; ?>
                        <div class="gallery-card"><a href="/uploads/<?= $escape($image) ?>" target="_blank"><img
                                    src="/uploads/<?= $escape($image) ?>" alt="Observation météo" loading="lazy"></a></div>
                    <?php endforeach; ?>
                </div>
            </section><?php endif; ?>
    </article>
</div>