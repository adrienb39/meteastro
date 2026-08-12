<div id="main-content-bg-logo">
    <div id="snow-container"
        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1; overflow-y: hidden;">
    </div>
    <div class="py-5 mt-5 mt-md-0">

        <div class="container">

            <div class="p-md-4 rounded">

                <h1 class="text-center avva-title mb-4" style="font-family: var(); margin-top: -45px;">
                    <a href="/page-presentation/<?= $pagePresentation->getId() ?>"
                        class="avva-title-double-underline text-decoration-none"
                        style="color: yellow; font-weight: bold; font-size: 50px;">
                        Bienvenue à l'Amicale Vélo du Val d'Amour
                    </a>
                </h1>
                <div class="marquee-container">
                    <p class="marquee-text">
                        <span class="bike-icon">🚴</span> <?= $defilementTexte->getDefilementTexte() ?>
                    </p>
                </div>

                <style>
                    <?php
                    $tailleTexte = $defilementTexte->getTailleDefilementTexte();
                    // On utilise exactement la taille du texte pour la hauteur
                    $hauteurDynamique = $tailleTexte;
                    ?>
                    .marquee-container {
                        position: relative;
                        width: 100%;
                        /* On force la hauteur sur la taille du texte */
                        height:
                            <?= $hauteurDynamique + 6 . 'px' ?>
                        ;
                        background:
                            <?= $defilementTexte->getFondDefilementTexte(); ?>
                        ;
                        margin-top: -26px;
                        /* Centre le texte verticalement si besoin */
                        display: flex;
                        align-items: center;
                        overflow: hidden;
                        /* Empêche le texte de dépasser du bandeau */
                        border-radius: 5px;
                        top: <?= $defilementTexte->getPositionDefilementTexte() . 'px' ?>;
                    }

                    <?php
                    $texte = $defilementTexte->getDefilementTexte();
                    $nbCaracteres = strlen($texte);

                    // On définit une base : par exemple, 1 seconde pour 10 caractères.
                    // On ajoute un minimum de 15s pour que même un texte court ne soit pas trop rapide.
                    $vitesseBase = 0.35; // Ajuste ce chiffre : plus il est petit, plus ça va vite
                    $dureeAnimation = max(30, $nbCaracteres * $vitesseBase + 5);

                    $tailleTexte = $defilementTexte->getTailleDefilementTexte();
                    $hauteurDynamique = $tailleTexte * 1.5; // Largeur de sécurité pour les emojis
                    ?>

                    .marquee-text {
                        position: absolute;
                        white-space: nowrap;
                        margin: 0;
                        padding-left: 100%;
                        animation: marquee
                            <?= $dureeAnimation . 's' ?>
                            linear infinite;
                        font-weight: bold;
                        color:
                            <?= $defilementTexte->getCouleurDefilementTexte() ?>
                        ;
                        font-size:
                            <?= $defilementTexte->getTailleDefilementTexte() . 'px' ?>
                        ;
                        line-height: 1;
                    }

                    @keyframes marquee {
                        0% {
                            transform: translateX(0);
                        }

                        100% {
                            transform: translateX(-100%);
                        }
                    }

                    .bike-icon {
                        position: relative;
                        display: inline-block;
                        margin-right: 0;
                        /* Espace pour laisser passer le texte */
                        z-index: 2;
                    }

                    /* La traînée principale (centrée) */
                    .bike-icon::before {
                        content: "";
                        position: absolute;
                        left: 80%;
                        top: 55%;
                        width: 45px;
                        height: 2px;
                        background: linear-gradient(to left, rgba(255, 255, 255, 0.8), transparent);
                        transform: translateY(-50%);
                        border-radius: 2px;
                        z-index: -1;
                        animation: contrail 0.2s infinite alternate;
                    }

                    /* Deuxième et troisième lignes */
                    .bike-icon::after {
                        content: "";
                        position: absolute;
                        left: 85%;
                        top: 40%;
                        /* Position de la ligne supérieure */
                        width: 40px;
                        height: 1px;
                        background: linear-gradient(to left, rgba(255, 255, 255, 0.5), transparent);
                        z-index: -1;
                        /* Le box-shadow crée la 3ème ligne en copiant la 2ème 
                            Syntaxe : x y flou couleur 
                        */
                        box-shadow: 0 8px 0 0 rgba(255, 255, 255, 0.5);
                        animation: contrail 0.3s infinite reverse;
                    }

                    /* Animation de vibration légère pour simuler la vitesse */
                    @keyframes contrail {
                        from {
                            transform: scaleX(1) translateY(-50%);
                            opacity: 0.7;
                        }

                        to {
                            transform: scaleX(1.2) translateY(-50%);
                            opacity: 1;
                        }
                    }
                </style>
                <div class="text-center py-3"
                    style="position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); width: 90%; max-width: 600px; z-index: 9999; pointer-events: none; display: none;">

                    <div id="container-countdown"
                        style="background: rgba(0, 0, 0, 0.8); backdrop-filter: blur(10px); border-radius: 20px; padding: 15px; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 10px 30px rgba(0,0,0,0.5); pointer-events: all; display: none;">

                        <div id="countdown-container" class="mb-0" style="display: none;">
                            <div class="mb-2 text-uppercase"
                                style="letter-spacing: 2px; color: white; font-size: 14px; font-weight: bold;">
                                🚀 Top départ <?= date('Y') + 1 ?> dans :
                            </div>
                            <div id="timer" class="d-flex justify-content-center align-items-center gap-3">
                                <div class="timer-unit text-white"><span id="days"
                                        style="font-size: 20px; font-weight: bold;">00</span><br><small
                                        style="font-size: 9px; opacity: 0.7; display: block;">JOURS</small></div>
                                <div class="timer-unit text-white"><span id="hours"
                                        style="font-size: 20px; font-weight: bold;">00</span><br><small
                                        style="font-size: 9px; opacity: 0.7; display: block;">HEURES</small></div>
                                <div class="timer-unit text-white"><span id="mins"
                                        style="font-size: 20px; font-weight: bold;">00</span><br><small
                                        style="font-size: 9px; opacity: 0.7; display: block;">MINS</small></div>
                                <div class="timer-unit" style="color: #ffeb3b;"><span id="secs"
                                        style="font-size: 20px; font-weight: bold;">00</span><br><small
                                        style="font-size: 9px; font-weight: bold;">SECS</small></div>
                            </div>
                        </div>

                        <div id="final-greeting" class="reveal-msg" style="display: none;">
                            <div class="bonne-annee"
                                style="font-size: 28px; font-weight: 900; text-transform: uppercase; color: #ffeb3b; text-shadow: 0 0 10px rgba(255,235,59,0.5);">
                                ✨ Bonne Année <?= date('Y') ?> ! ✨
                            </div>
                            <div style="color: white; font-size: 14px; font-weight: bold;">
                                🚲 À fond vers de nouveaux sommets 🚲
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="d-flex justify-content-md-around align-items-md-start flex-column flex-md-row">
        <nav class="navbar avva-accueil mx-auto mx-md-0 mb-5 mb-md-0" style="margin-right: 20px">
            <ul class="navbar-nav">
                <li class="nav-link d-flex flex-column align-items-center">
                    <?php foreach ($pages as $page): ?>
                        <?php if ($page->getDispositionPageAccueil()->getNom() == 'Gauche'): ?>
                            <a class="btn btn-light btn-lg border-3 border-primary text-uppercase mb-5 rounded-5"
                                href="page/<?= $page->getUrl() ?>"><?= $page->getNom() ?></a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </li>
            </ul>
        </nav>
        <div class="position-relative d-flex flex-column flex-md-row align-items-center">
            <nav class="navbar mx-auto mx-md-0 mt-5 mt-md-0">
                <ul class="navbar-nav">
                    <li class="nav-link d-flex flex-column align-items-center">
                        <?php foreach ($pages as $page): ?>
                            <?php if ($page->getDispositionPageAccueil()->getNom() == 'Droite'): ?>
                                <?php if ($page->getId() != 5 && $page->getId() != 12 && $page->getId() != 13 && $page->getId() != 14): ?>
                                    <?php if ($page->getUrl() == 'cartoguide'): ?>
                                        <a class="btn btn-light btn-lg border-3 border-primary text-uppercase mb-5 rounded-5"
                                            href="/<?= $page->getUrl() ?>"><?= $page->getNom() ?></a>
                                    <?php else: ?>
                                        <a class="btn btn-light btn-lg border-3 border-primary text-uppercase mb-5 rounded-5"
                                            href="page/<?= $page->getUrl() ?>"><?= $page->getNom() ?></a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </li>
                </ul>
            </nav>
            <div class="bouton-absolu-droite">
                <nav class="navbar mx-auto mx-md-0 mt-0">
                    <ul class="navbar-nav">
                        <li class="nav-link d-flex flex-column align-items-center" style="margin-bottom: -16px;">
                            <a class="btn btn-light btn-lg border-3 border-primary text-uppercase mb-5 rounded-5"
                                href="/page/photos-videos">Photos / Vidéos</a>
                        </li>
                        <li class="nav-link d-flex flex-column align-items-center" style="margin-bottom: -16px;">
                            <a class="btn btn-light btn-lg border-3 border-primary text-uppercase mb-5 rounded-5"
                                href="/page/extra">Extra</a>
                        </li>
                        <li class="nav-link d-flex flex-column align-items-center" style="margin-bottom: -16px;">
                            <a class="btn btn-light btn-lg border-3 border-primary text-uppercase mb-5 rounded-5"
                                href="/page/contacts">Contacts</a>
                        </li>
                        <li class="nav-link d-flex flex-column align-items-center">
                            <a class="btn btn-light btn-lg border-3 border-primary text-uppercase mb-5 rounded-5"
                                href="/page/boutique">Boutique</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <div id="door-on-footer-area" class="d-flex justify-content-between justify-content-md-center align-items-center">
        <span id="door-icon"></span>
        <button class="btn btn-sm me-md-5" id="fullscreen-button" title="Plein Écran"
            style="font-size: 25px; position: relative; z-index: 999999" type="button">
            <img src="/assets/images/fullscreen-maximize.png" alt="Plein Écran" style="width: 25px; height: 25px;">
        </button>
    </div>
</div>
<style>
    .snowflake {
        position: absolute;
        top: -10px;
        color: white;
        user-select: none;
        pointer-events: none;
        z-index: 1;
        will-change: transform;
    }

    @keyframes fall {
        0% {
            transform: translateY(0) rotate(0deg);
        }

        100% {
            transform: translateY(100vh) rotate(360deg);
        }
    }

    #countdown-container {
        font-family: 'Arial Black', sans-serif;
        color: white;
        text-shadow: 0 0 20px rgba(255, 255, 0, 0.7);
        transition: all 0.5s ease;
    }

    .timer-unit {
        display: inline-block;
        background: rgba(0, 0, 0, 0.3);
        padding: 10px 15px;
        border-radius: 10px;
        margin: 0 5px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .reveal-msg {
        animation: festivePop 1s cubic-bezier(0.17, 0.89, 0.32, 1.49) forwards;
        display: none;
        /* Caché par défaut */
    }

    @keyframes festivePop {
        0% {
            transform: scale(0.5);
            opacity: 0;
        }

        100% {
            transform: scale(1.1);
            opacity: 1;
        }
    }

    /* Animation de pulsation douce pour le conteneur */
    @keyframes pulseContainer {
        0% {
            transform: scale(1);
            opacity: 0.9;
        }

        50% {
            transform: scale(1.02);
            opacity: 1;
        }

        100% {
            transform: scale(1);
            opacity: 0.9;
        }
    }

    /* Animation de battement pour les secondes */
    @keyframes heartBeat {
        0% {
            transform: scale(1);
            color: white;
        }

        14% {
            transform: scale(1.3);
            color: #ffeb3b;
        }

        28% {
            transform: scale(1);
        }

        42% {
            transform: scale(1.3);
            color: #ffeb3b;
        }

        70% {
            transform: scale(1);
        }
    }

    /* Effet de balayage brillant sur "Bonne Année" */
    @keyframes shimmer {
        0% {
            background-position: -200% center;
        }

        100% {
            background-position: 200% center;
        }
    }

    .timer-unit {
        display: inline-block;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(5px);
        /* Effet de verre dépoli */
        padding: 12px 18px;
        border-radius: 15px;
        margin: 0 8px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        min-width: 80px;
        color: black;
    }

    #secs {
        display: inline-block;
        animation: heartBeat 1s infinite;
        font-weight: 900;
    }

    .bonne-annee {
        background: linear-gradient(90deg, #ffd700, #fff, #ffd700);
        background-size: 200% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: shimmer 3s linear infinite, festivePop 0.8s cubic-bezier(0.17, 0.89, 0.32, 1.49);
        display: inline-block;
    }

    #countdown-container {
        animation: pulseContainer 3s ease-in-out infinite;
    }
</style>

<script>
    function createSnowflake() {
        const container = document.getElementById('snow-container');
        if (!container) return;

        const flake = document.createElement('div');
        const size = Math.random() * 10 + 5 + 'px'; // Taille entre 5 et 15px

        flake.className = 'snowflake';
        flake.innerText = '❄'; // Caractère flocon
        flake.style.left = Math.random() * 100 + '%';
        flake.style.fontSize = size;
        flake.style.opacity = Math.random();
        flake.style.animation = `fall ${Math.random() * 5 + 5}s linear infinite`; // Vitesse variable

        container.appendChild(flake);

        // Supprime le flocon après l'animation pour ne pas saturer la page
        setTimeout(() => {
            flake.remove();
        }, 10000);
    }

    function manageNewYearDisplay() {
        const now = new Date();
        const currentMonth = now.getMonth(); // 0 = Janvier, 11 = Décembre
        const currentDate = now.getDate();
        const currentYear = now.getFullYear();

        // --- CONFIGURATION DES DATES ---
        // Afficher si on est en Décembre (11) OU en Janvier (0)
        const isDecember = (currentMonth === 11);
        const isJanuary = (currentMonth === 0);

        // Le script s'arrête le 1er Février (donc affiche si mois < 1)
        const shouldShow = isDecember || isJanuary;

        const container = document.querySelector('.text-center.py-3[style*="position: fixed"]');
        const containerCountdown = document.querySelector('#container-countdown')

        if (!shouldShow) {
            if (container) container.style.display = 'none';
            if (containerCountdown) containerCountdown.style.display = 'none';
            return; // On arrête tout si on n'est pas dans la période
        } else {
            // SI C'EST LA BONNE PÉRIODE : On l'affiche !
            if (container) container.style.display = 'block';
            if (containerCountdown) containerCountdown.style.display = 'block';
            // Génère un flocon toutes les 200ms
            setInterval(createSnowflake, 200);
        }

        // --- CALCUL DE LA CIBLE ---
        // Si on est en Janvier, l'année cible est l'année en cours. 
        // Si on est en Décembre, l'année cible est l'année suivante.
        const targetYear = isJanuary ? currentYear : currentYear + 1;
        const targetDate = new Date(`Jan 1, ${targetYear} 00:00:00`).getTime();

        const updateTimer = setInterval(() => {
            const currentTime = new Date().getTime();
            const distance = targetDate - currentTime;

            const countdownBox = document.getElementById("countdown-container");
            const greetingBox = document.getElementById("final-greeting");

            // SI ON EST APRES MINUIT (Jour de l'an)
            if (distance < 0) {
                if (countdownBox) countdownBox.style.display = "none";
                if (greetingBox) {
                    greetingBox.style.display = "block";
                    greetingBox.querySelector('.bonne-annee').innerHTML = `✨ BONNE ANNÉE ${targetYear} ! ✨`;
                }
                // Neige plus intense pour la fête
                if (typeof createSnow === "function") setInterval(createSnow, 100);
                clearInterval(updateTimer);
                return;
            }

            countdownBox.style.display = "block";

            // CALCUL DU COMPTE À REBOURS
            const d = Math.floor(distance / (1000 * 60 * 60 * 24));
            const h = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const s = Math.floor((distance % (1000 * 60)) / 1000);

            if (document.getElementById("days")) {
                document.getElementById("days").innerText = d.toString().padStart(2, '0');
                document.getElementById("hours").innerText = h.toString().padStart(2, '0');
                document.getElementById("mins").innerText = m.toString().padStart(2, '0');
                document.getElementById("secs").innerText = s.toString().padStart(2, '0');
            }
        }, 1000);
    }

    // Lancement au chargement
    document.addEventListener('DOMContentLoaded', manageNewYearDisplay);
</script>
<?php
/**
 * RENDER MEDIA ITEMS - Version Moderne & Corrigée
 * Affiche les éléments médias (images ou vidéos) dans la structure nécessaire
 * pour le défilement infini, incluant les attributs pour la modale.
 * @param array $medias Tableau d'objets App\Entity\PhotoVideo.
 */
function render_media_items(array $medias): void
{
    $output = '';

    foreach ($medias as $media) {
        if (!($media instanceof App\Entity\PhotoVideo)) {
            continue;
        }

        $type = $media->getType();
        $fichier = htmlspecialchars($media->getFichier() ?? '');
        $embedUrl = htmlspecialchars($media->getEmbedUrl() ?? '');
        $title = htmlspecialchars($media->getTitre() ?? 'Média sans titre');

        $data_attributes = '';
        if ($type === 'image' && !empty($fichier)) {
            $data_attributes = ' data-full-src="/' . $fichier . '" data-title="' . $title . '"';

        // Début de l'item avec des classes de type
        $output .= '<div class="media-item media-item--' . $type . '"' . $data_attributes . '>';
        // 🔥 NOUVEAU CONTENEUR DE TRANSFORMATION (isole l'animation de survol)
        $output .= '<div class="hover-target">';

        if ($type === 'image' && !empty($fichier)) {
            // Image
            $output .= <<<HTML
            <div class="media-wrapper">
                <img src="/{$fichier}" alt="{$title}"
                    class="media-content"
                    loading="lazy">
            </div>
            HTML;
        } elseif ($type === 'video' && !empty($embedUrl)) {
            // Vidéo
            $output .= <<<HTML
            <div class="media-wrapper media-wrapper--16-9">
                <iframe src="{$embedUrl}"
                    class="media-content"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen loading="lazy" frameborder="0" title="{$title}"></iframe>
            </div>
            HTML;
        }

        // FIN du conteneur de transformation et de l'item
        $output .= '</div>';
        $output .= '</div>';
        }
    }

    echo $output;
}
?>
<div class="scrolling-container" id="media-scroller">
    <div class="scrolling-track">
        <div class="scrolling-content-initial">
            <?php
            if (isset($medias) && is_array($medias) && !empty($medias)) {
                render_media_items($medias);
            } else {
                echo '<p class="no-media-message">Aucun média à afficher.</p>';
            }
            ?>
        </div>
    </div>
</div>

<div id="media-modal" class="modal">
    <span class="close-button" title="Fermer (Échap)">&times;</span>
    <img class="modal-content" id="modal-image" alt="Image agrandie">
    <div id="modal-caption"></div>
</div>
<style>
    /* ************************************** */
    /* 2. STYLES CSS (MODERNE & PERFORMANT)   */
    /* ************************************** */

    :root {
        /* Dimensions et Espacement (Conservé) */
        --item-width: 30px;
        --item-height: 30px;
        --item-gap: 24px;

        /* Animations & Transitions (Conservé) */
        --base-duration: 999s;
        --fast-duration: 999s;
        --transition-easing: cubic-bezier(0.4, 0, 0.2, 1);
        --speed-transition-duration: 0.8s;
        --hover-transition: 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);

        /* Variables JS (Conservé) */
        --scroll-distance: 0px;
        --is-paused: running;
    }

    /* 🖼️ Conteneur principal */
    .scrolling-container {
        width: 100%;
        position: absolute;
        bottom: 0;
        left: 0;
        overflow: visible;
        background: linear-gradient(to right, transparent 0%, rgba(0, 0, 0, 0.05) 10%, rgba(0, 0, 0, 0.05) 90%, transparent 100%);
        backdrop-filter: blur(5px);
    }

    /* 🛣️ La piste de défilement (Inchangée) */
    .scrolling-track {
        display: flex;
        width: fit-content;
        animation: marquee-scroll var(--base-duration) linear var(--is-paused) infinite;
        transition: animation-duration var(--speed-transition-duration) var(--transition-easing);
        overflow: visible;
    }

    /* Conteneurs de contenu (Inchangés) */
    .scrolling-content-initial,
    .scrolling-content-clone {
        display: flex;
        flex-shrink: 0;
    }

    /* 📦 Vignettes (media-item) */
    .media-item {
        flex-shrink: 0;
        width: var(--item-width);
        height: var(--item-height);
        contain: layout;
        /* Utiliser layout au lieu de strict pour permettre le débordement des transformations */
        overflow: visible;
        cursor: pointer;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);

        /* 🔥 AJOUTÉ : Définit un contexte de positionnement stable pour l'enfant */
        position: relative;
        /* Retire les transitions et will-change du parent pour éviter les conflits */
        transition: none;
        will-change: auto;
        z-index: 1;
    }

    /* 🎯 NOUVEAU : Conteneur de transformation */
    .hover-target {
        width: 100%;
        height: 100%;
        position: absolute;
        /* Permet au hover-target de se positionner par rapport au media-item */
        top: 0;
        left: 0;
        transition: transform var(--hover-transition), box-shadow var(--transition-easing);
        will-change: transform, box-shadow;

        /* Réapplique le box-shadow de base sur la cible pour qu'il soit transformé avec elle */
        box-shadow: inherit;
    }

    /* 🖱️ Effet de Survol : Déplacement + Agrandissement (Appliqué au hover-target) */
    .media-item:hover .hover-target {
        /*
         * scale(3) : Rend la vignette 3 fois plus grande (90px).
         * translateY(-300%) : Déplace de 3x sa hauteur.
         */
        transform: translateY(-200%) scale(3);
        z-index: 200;
        /* Priorité maximale pour être au-dessus du reste */
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6), 0 30px 80px rgba(0, 0, 0, 0.4);
        transition: transform 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    /* 🖼️ Wrapper Média & Contenu (Inchangé) */
    .media-wrapper {
        width: 100%;
        height: 100%;
        overflow: hidden;
        position: relative;
        padding-bottom: 56.25%;
    }

    .media-item--image .media-wrapper {
        padding-bottom: 0;
    }

    .media-content {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        position: absolute;
        top: 0;
        left: 0;

        transition: transform 1s var(--transition-easing);
        will-change: transform;
    }

    /* 🔎 Annulation de zoom interne (SUPPRIMÉ pour que l'image grandisse avec le conteneur) */
    /* Le bloc .media-item:hover .media-content a été retiré */

    /* ⏱️ Animation CSS de défilement (Inchangée) */
    @keyframes marquee-scroll {
        from {
            transform: translateX(0);
        }

        to {
            transform: translateX(var(--scroll-distance));
        }
    }

    /* --- Styles de la Modale (Inchangés) --- */
    .modal {
        display: none;
        position: fixed;
        z-index: 2000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        padding-top: 50px;
        animation: fadeIn 0.3s forwards;
    }

    #media-modal {
        background-color: rgba(0, 0, 0, 0.9);
    }

    .modal-content {
        margin: auto;
        display: block;
        width: 90%;
        max-width: 900px;
        max-height: 85vh;
        object-fit: contain;
        animation: zoomIn 0.3s;
    }

    #modal-caption {
        margin: 10px auto;
        display: block;
        width: 90%;
        max-width: 900px;
        text-align: center;
        color: #fff;
        font-size: 16px;
    }

    .close-button {
        position: fixed;
        top: 15px;
        right: 35px;
        color: #fff;
        font-size: 40px;
        font-weight: bold;
        transition: 0.3s;
        cursor: pointer;
        z-index: 2001;
    }

    .close-button:hover,
    .close-button:focus {
        color: #aaa;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes zoomIn {
        from {
            transform: scale(0.8);
        }

        to {
            transform: scale(1);
        }
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const track = document.querySelector('.scrolling-track');
        const initialContent = document.querySelector('.scrolling-content-initial');
        const root = document.documentElement;

        if (!track || !initialContent) return;

        // --- CONSTANTES ---
        const SPEED_FACTOR = 30;
        const CLONE_COUNT = 3;
        let isCloned = false;

        // --- GESTION DE LA MODALE ---
        const modal = document.getElementById('media-modal');
        const modalImage = document.getElementById('modal-image');
        const modalCaption = document.getElementById('modal-caption');
        const closeButton = document.querySelector('.close-button');

        const closeModal = () => {
            modal.style.display = 'none';
            document.body.style.overflow = '';
            root.style.setProperty('--is-paused', 'running');
        };

        const openModal = (fullSrc, title) => {
            modalImage.src = fullSrc;
            modalCaption.textContent = title || 'Image sans titre';
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
            root.style.setProperty('--is-paused', 'paused');
        };

        // Fonction pour attacher les écouteurs de Clic (Modale)
        const attachModalListeners = () => {
            // La délégation d'événement fonctionne toujours sur .media-item--image
            document.getElementById('media-scroller').addEventListener('click', (event) => {
                let item = event.target.closest('.media-item--image');

                if (item) {
                    const fullSrc = item.getAttribute('data-full-src');
                    const title = item.getAttribute('data-title');

                    if (fullSrc) {
                        openModal(fullSrc, title);
                    }
                }
            });
        };

        // Fonction pour gérer le Survol (Pause/Reprise du Marquee)
        const attachHoverListeners = () => {
            const scroller = document.getElementById('media-scroller');

            scroller.addEventListener('mouseenter', () => {
                if (modal.style.display !== 'block') {
                    root.style.setProperty('--is-paused', 'paused');
                }
            });

            scroller.addEventListener('mouseleave', () => {
                if (modal.style.display !== 'block') {
                    root.style.setProperty('--is-paused', 'running');
                }
            });
        };


        // --- LOGIQUE DE DÉFILEMENT (MARQUEE) ---
        const updateScroller = () => {
            const initialWidth = initialContent.getBoundingClientRect().width;

            if (initialWidth <= 0) return;

            const scrollDistance = `-${initialWidth}px`;
            const newDuration = initialWidth / SPEED_FACTOR;

            root.style.setProperty('--scroll-distance', scrollDistance);
            root.style.setProperty('--base-duration', `${newDuration}s`);

            if (!isCloned) {
                track.querySelectorAll('.scrolling-content-clone').forEach(clone => clone.remove());

                for (let i = 0; i < CLONE_COUNT; i++) {
                    const clone = initialContent.cloneNode(true);
                    clone.classList.remove('scrolling-content-initial');
                    clone.classList.add('scrolling-content-clone');
                    track.appendChild(clone);
                }
                isCloned = true;

                attachModalListeners();
                attachHoverListeners();
            }
        };

        // Utiliser ResizeObserver pour détecter les changements de taille
        const resizeObserver = new ResizeObserver(entries => {
            if (entries[0] && entries[0].contentRect) {
                updateScroller();
            }
        });

        resizeObserver.observe(initialContent);

        // --- GESTION DES ÉVÉNEMENTS DE LA MODALE ---
        closeButton.addEventListener('click', closeModal);

        window.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && modal.style.display === 'block') {
                closeModal();
            }
        });
    });
</script>