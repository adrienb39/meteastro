<?php
if (is_array($settings)) {
    // Cas où $settings est un tableau
    $logoFilename = $settings['logo_filename'] ?? null;
} elseif ($settings instanceof \App\Entity\Reglage) {
    // Cas où $settings est l'entité Reglage
    $logoFilename = $settings->getLogoFilename();
} else {
    $logoFilename = null;
}
$logoPath = $logoFilename ? '/uploads/logo/' . $logoFilename : null;

if (is_array($settings)) {
    // Cas où $settings est un tableau
    $imageFondFilename = $settings['image_fond_filename'] ?? null;
} elseif ($settings instanceof \App\Entity\Reglage) {
    // Cas où $settings est l'entité Reglage
    $imageFondFilename = $settings->getImageFondFilename();
} else {
    $imageFondFilename = null;
}
$imageFondPath = $imageFondFilename ? '/uploads/image-fond/' . $imageFondFilename : null;
?>
<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amicale Vélo du Val d'Amour - AVVA39</title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="icon" href="/assets/images/logo-avva39.png">
    <link rel="apple-touch-icon" href="/assets/images/logo-avva39.png">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.js"></script>

    <!--  -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0">

    <!-- FullCalendar JS -->
    <script src="/assets/js/fullcalendar.global.js"></script>
    <!--  -->
    <link rel="stylesheet" href="/assets/css/leaflet.css" />
    <script src="/assets/js/leaflet.js"></script>

    <script src="/assets/js/gpx.js"></script>
    <style>
        /* #vertical-sidebar { */
        /* Largeur de la barre latérale */
        /* width: 250px; */

        /* MODIFICATION CLÉ pour le bas : définit une marge en bas */
        /* La classe 'mb-3' ne suffit pas car 'position-fixed' ignorait 'mb-3' pour la hauteur. */
        /* top: est géré par mt-3 */
        /* bottom: fixe la distance par rapport au bas de l'écran (1rem correspond à peu près à une marge Bootstrap de niveau 3) */
        /* bottom: 1rem; */

        /* MODIFICATION CLÉ pour la hauteur : Calcule la hauteur totale. */
        /* Hauteur de la fenêtre (100vh) - Marge du haut (mt-3 ≈ 1rem) - Marge du bas (bottom: 1rem) */
        /* height: calc(100vh - 2rem); */

        /* Coins arrondis (uniquement à droite) */
        /* border-radius: 0 15px 15px 0; */

        /* Effet "flottant" */
        /* box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1); */
        /* z-index: 1030; */
        /* } */
        /* Import Google font - Poppins */
        /*  */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        :root {
            --avva-bleu:
                <?= $index ? $settings->getThemeTextColor() : '' ?>
            ;
            /* Bleu foncé pour le texte et le fond */
            --avva-rouge: #ff0000;
            /* Rouge vif */
            --avva-jaune:
                <?= $index ? $settings->getThemeFondColor() : '' ?>
            ;
            /* Jaune vif pour le contraste/hover */
            --avva-noir: #000000;
            --avva-blanc: #ffffff;
        }

        #main-content-bg::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;

            /* SUPERPOSITION DES DEUX IMAGES */
            background-image:
                url('<?= htmlspecialchars($imageFondPath); ?>');

            background-repeat:
                no-repeat;

            background-attachment: fixed;
            /* L'image reste fixe lors du défilement */

            background-position:
                center;

            background-size:
                cover;
            /* L'image de Val d'Amour couvre tout */
            z-index: -990;
        }

        #main-content-bg-logo::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;

            /* SUPERPOSITION DES DEUX IMAGES */
            background-image:
                url('<?= htmlspecialchars($logoPath); ?>');

            background-repeat:
                no-repeat;

            background-position:
                center 90%;

            /* ANIMATION DE CHARGEMENT PAR DÉFAUT (Rotation TRÈS rapide et infinie) */
            background-size: 10%;
            animation: logo-tourne-petit 0.5s linear infinite;
            /* Rotation très rapide (0.5s) */

            z-index: -980;
        }

        /* Écrans <= 1500px */
        @media screen and (max-width: 2000px) and (min-width: 1750px) {
            #main-content-bg-logo::before {
                background-position: center 80% !important;
                /* Exemple: -5% par rapport à 90% */
            }
        }

        /* Écrans <= 1400px */
        @media screen and (max-width: 1750px) and (min-width: 1500px) {
            #main-content-bg-logo::before {
                background-position: center 70% !important;
                /* Exemple: -5% par rapport à 85% */
            }
        }

        /* Écrans <= 1300px */
        @media screen and (max-width: 1500px) and (min-width: 1250px) {
            #main-content-bg-logo::before {
                background-position: center 60% !important;
                /* Exemple: -5% par rapport à 80% */
            }
        }

        @media screen and (max-width: 1250px) and (min-width: 1000px) {
            #main-content-bg-logo::before {
                background-position: center 40% !important;
                /* Exemple: -5% par rapport à 80% */
            }
        }


        /* Écrans <= 1000px */
        @media screen and (max-width: 1000px) {
            #main-content-bg-logo::before {
                background-position: center 60%;
            }
        }

        #main-content-bg-logo.logo-chargement-termine::before {
            background-position: center 52%;
            animation: logo-agrandissement-final 1.5s ease-out forwards;
            /* background-size: 50%; */
            transform: rotate(0deg);
        }

        @media (display-mode: standalone) {
            #main-content-bg-logo.logo-chargement-termine::before {
                background-position: center 44%;
            }
        }

        /* 1. Définition de l'animation de zoom */
        @keyframes logo-double-battement-avva39 {
            /* Période 1 : 0% -> 33.33% (1 seconde sur 3) - Les deux battements */

            /* Battement 1 */
            0% {
                transform: scale(1);
            }

            4.16% {
                /* 0.125s (1/8 de la seconde de battement) */
                transform: scale(1.05);
                /* Zoom maximal (le "battement") */
            }

            8.33% {
                /* 0.25s (1/4 de la seconde de battement) */
                transform: scale(1);
                /* Retour à la normale */
            }

            /* Battement 2 */
            16.66% {
                /* 0.5s (2/4 de la seconde de battement) */
                transform: scale(1.05);
                /* Deuxième zoom maximal */
            }

            25% {
                /* 0.75s (3/4 de la seconde de battement) */
                transform: scale(1);
                /* Retour à la normale */
            }

            /* Fin des battements à 33.33% (1 seconde) */

            /* Période 2 : 33.33% -> 100% (2 secondes sur 3) - Le repos */
            100% {
                transform: scale(1);
                /* Reste à la taille normale pour le reste du cycle */
            }
        }

        /* 2. Application de l'animation en continu sur le logo (le pseudo-élément ::before du main) */
        #main-content-bg-logo.battement-actif::before {
            /* Définit l'animation :
            - logo-zoom-in-out-continuous : le nom de l'animation
            - 60s : La durée d'un cycle complet (zoom et dézoom). C'est votre "toutes les minutes".
            - ease-in-out : Rend l'effet plus doux.
            - infinite : Répète l'animation sans fin ! (Ceci remplace la boucle JavaScript)
            */
            animation: logo-double-battement-avva39 4.6s infinite;

            /* Assurez-vous que le transform est prêt à être animé (déjà présent dans votre code, mais important) */
            will-change: transform;
        }

        /* =========================================================================
   0. VARIABLES DE THÈME ET RÉINITIALISATION DE BASE
   ========================================================================= */

        :root {
            /* Couleurs du Thème */
            --clr-bg-dark: #151A2D;
            /* Bleu Marine Profond (Arrière-plan principal) */
            --clr-bg-dark-hover: #252D4D;
            /* Bleu Moyen (Hover/Sous-menu) */
            --clr-accent: #40A4FF;
            /* Bleu Vif (Couleur d'accent/Actif) */
            --clr-text-light: #fff;
            /* Blanc (Texte par défaut) */
            --clr-text-dark: #151A2D;
            /* Texte sombre (Sur accent) */
            --clr-toggler: #4A567D;
            /* Bleu Gris (Bouton d'ouverture/fermeture) */
            --clr-border: rgba(255, 255, 255, 0.1);
            /* Bordure subtile */

            /* Espacement & Arrondis */
            --sp-margin: 16px;
            --sp-padding-link: 12px 15px;
            --br-base: 12px;
            --br-large: 20px;

            /* Dimensions */
            --dim-sidebar-full: 270px;
            --dim-sidebar-collapsed: 85px;
            --dim-header-height-mobile: 56px;

            /* Transitions */
            --tr-base: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        /* -------------------------------------------------------------------------
   1. STRUCTURE GÉNÉRALE ET EFFETS VISUELS
   ------------------------------------------------------------------------- */

        aside {
            z-index: 1020;
        }

        .sidebar {
            position: fixed;
            width: var(--dim-sidebar-full);
            margin: var(--sp-margin);
            border-radius: var(--br-large);
            background: var(--clr-bg-dark);
            height: calc(100vh - (2 * var(--sp-margin)));
            display: flex;
            flex-direction: column;
            /* Ombre subtile, moderne et profonde */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4), inset 0 0 5px rgba(255, 255, 255, 0.05);
            transition: var(--tr-base);
        }

        /* Mode Réduit */
        .sidebar.collapsed {
            width: var(--dim-sidebar-collapsed);
        }

        /* -------------------------------------------------------------------------
   2. HEADER ET TOGGLER
   ------------------------------------------------------------------------- */

        .sidebar-header {
            display: flex;
            position: relative;
            padding: 25px 20px;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
            border-bottom: 1px solid var(--clr-border);
        }

        .sidebar-header .header-logo img {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            object-fit: contain;
        }

        .sidebar-header .toggler {
            height: 35px;
            width: 35px;
            border: none;
            cursor: pointer;
            display: flex;
            background: var(--clr-toggler);
            color: var(--clr-text-light);
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: 0.4s ease;
        }

        .sidebar-header .toggler:hover {
            background: #606b99;
        }

        /* Positionnement en mode réduit */
        .sidebar.collapsed .sidebar-header .sidebar-toggler {
            /* Déplacer l'icône chevron au bas du header quand la barre est réduite */
            transform: translate(-4px, 65px);
        }

        .sidebar.collapsed .sidebar-header .sidebar-toggler span {
            transform: rotate(180deg);
        }

        .sidebar-header .menu-toggler {
            display: none;
            /* Caché par défaut sur desktop */
        }

        /* -------------------------------------------------------------------------
   3. NAVIGATION ET DÉFILEMENT (Desktop)
   ------------------------------------------------------------------------- */

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            min-height: 0;
        }

        .sidebar-nav .nav-list {
            list-style: none;
            display: flex;
            gap: 4px;
            padding: 0 15px;
            flex-direction: column;
            transition: 0.4s ease;
        }

        /* Zone de défilement principale */
        .sidebar-nav .primary-nav {
            flex-grow: 1;
            overflow-y: auto;
            padding-top: 15px;
            position: relative;
            scrollbar-width: none;
            /* Firefox */
            -ms-overflow-style: none;
            /* IE and Edge */
        }

        .sidebar-nav .primary-nav::-webkit-scrollbar {
            width: 6px;
            background: transparent;
        }

        .sidebar-nav .primary-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
        }

        /* Décalage en mode réduit pour l'indicateur de scroll */
        .sidebar.collapsed .sidebar-nav .primary-nav {
            transform: translateY(65px);
        }

        /* -------------------------------------------------------------------------
   4. LIENS DE NAVIGATION (Vifs et Modernes)
   ------------------------------------------------------------------------- */

        .sidebar-nav .nav-link {
            color: var(--clr-text-light);
            display: flex;
            gap: 12px;
            white-space: nowrap;
            border-radius: var(--br-base);
            padding: var(--sp-padding-link);
            align-items: center;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .sidebar-nav .nav-icon {
            font-size: 1.5rem;
            /* Taille standard des icônes */
        }

        /* Masque le texte en mode réduit */
        .sidebar.collapsed .sidebar-nav .nav-link .nav-label {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        /* HOVER */
        .sidebar-nav .nav-link:hover {
            color: var(--clr-text-light);
            background: var(--clr-bg-dark-hover);
        }

        /* ACTIF (Lien Principal) */
        .sidebar-nav .nav-item.active>.nav-link:not(.toggle-submenu) {
            color: var(--clr-text-dark);
            background: var(--clr-accent);
            box-shadow: 0 4px 10px rgba(64, 164, 255, 0.3);
            font-weight: 600;
        }

        /* ACTIF (Titre Parent de Sous-menu actif) - Style différent pour le parent */
        .sidebar-nav .nav-item.active>.nav-link.toggle-submenu {
            background: var(--clr-bg-dark-hover);
            /* ou autre couleur subtile pour indiquer le parent actif */
            font-weight: 600;
        }


        /* -------------------------------------------------------------------------
   5. SOUS-MENU (Ouverture dans le Flux - Clic/Actif)
   ------------------------------------------------------------------------- */

        .nav-item {
            position: relative;
        }

        .sub-menu {
            display: none;
            /* Masqué par défaut */
            position: static;
            /* Dans le flux, pas en survol */

            width: 100%;

            background-color: var(--clr-bg-dark-hover);
            padding: 8px 0;
            margin: 4px 0;
            list-style: none;
            min-width: auto;
            box-shadow: none;
            z-index: 1;
            border-radius: var(--br-base);
        }

        /* CLASSE AJOUTÉE PAR PHP ou JS pour ouvrir le sous-menu */
        .nav-item.submenu-open .sub-menu {
            display: block;
        }

        .sub-menu li a.nav-link {
            /* Indentation visuelle des liens enfants */
            padding: 8px 12px 8px 45px;
            /* Augmenté l'indentation */
            background: transparent;
            border-radius: 8px;
            /* Léger arrondi pour les sous-liens */
        }

        .sub-menu li a.nav-link:hover {
            background-color: var(--clr-bg-dark);
            /* Hover plus sombre pour les sous-liens */
            color: var(--clr-text-light);
        }

        /* ACTIF (Lien Enfant du Sous-menu) */
        .sub-menu li a.nav-link.active {
            color: var(--clr-text-dark);
            background: var(--clr-accent);
            box-shadow: 0 2px 5px rgba(64, 164, 255, 0.2);
            /* Ombre plus douce */
            font-weight: 600;
        }

        .nav-item.submenu-open .sub-menu {
            display: block;
            /* Force l'affichage du sous-menu */
        }

        /* -------------------------------------------------------------------------
   6. INDICATION DE DÉFILEMENT (Scroller Indicator)
   ------------------------------------------------------------------------- */

        .scroll-indicator {
            position: sticky;
            height: 30px;
            pointer-events: none;
            z-index: 10;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.5s ease;
            padding: 5px 0;
        }

        .scroll-indicator.top {
            top: 0;
            background: linear-gradient(to bottom, var(--clr-bg-dark) 20%, rgba(21, 26, 45, 0) 100%);
        }

        .scroll-indicator.bottom {
            bottom: 0;
            background: linear-gradient(to top, var(--clr-bg-dark) 20%, rgba(21, 26, 45, 0) 100%);
        }

        .scroll-indicator .fa-angle-up,
        .scroll-indicator .fa-angle-down {
            color: var(--clr-accent);
            font-size: 1.5rem;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-5px);
            }

            60% {
                transform: translateY(-2px);
            }
        }

        .scroll-indicator.show {
            opacity: 1;
        }

        .sidebar.collapsed .scroll-indicator {
            display: none !important;
        }

        /* -------------------------------------------------------------------------
   7. NAVIGATION SECONDAIRE (Bas de page)
   ------------------------------------------------------------------------- */

        .sidebar-nav .secondary-nav {
            margin-top: auto;
            flex-shrink: 0;
            border-top: 1px solid var(--clr-border);
            padding: 10px 15px 20px 15px;
            /* Rendre le padding cohérent */
        }

        /* -------------------------------------------------------------------------
   8. TOOLTIP (Mode Réduit)
   ------------------------------------------------------------------------- */

        .sidebar-nav .nav-tooltip {
            position: absolute;
            top: 50%;
            /* Centrer verticalement */
            transform: translateY(-50%);
            /* Ajustement pour le centrage */
            opacity: 0;
            background: var(--clr-accent);
            color: var(--clr-text-dark);
            display: none;
            pointer-events: none;
            padding: 6px 12px;
            border-radius: var(--br-base);
            white-space: nowrap;
            left: calc(100% + 25px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            transition: 0s;
            /* Retire la transition 'top' */
        }

        .sidebar.collapsed .nav-item:hover .nav-link {
            background: var(--clr-bg-dark-hover);
            /* Conserver le hover même si on passe sur le tooltip */
        }

        .sidebar.collapsed .sidebar-nav .nav-tooltip {
            display: block;
            /* Afficher la zone du tooltip */
        }

        .sidebar-nav .nav-item:hover .nav-tooltip {
            opacity: 1;
            pointer-events: auto;
            /* Laisser le translateY(-50%) mais ajouter le décalage initial pour l'animation */
            left: calc(100% + 20px);
            /* Position finale plus proche */
            transition: all 0.4s ease;
        }

        /* -------------------------------------------------------------------------
   9. RESPONSIVE : ÉCRANS JUSQU'À 1024px (MOBILE)
   ------------------------------------------------------------------------- */

        @media (max-width: 1024px) {

            .sidebar {
                position: fixed;
                /* Reste fixe en bas de la page */
                top: var(--sp-margin);
                bottom: auto;
                height: var(--dim-header-height-mobile);
                margin: var(--sp-margin);
                width: calc(100% - (2 * var(--sp-margin)));
                overflow-y: hidden;
                max-height: calc(100vh - (2 * var(--sp-margin)));
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
                /* Retire la classe collapsed */
                width: calc(100% - (2 * var(--sp-margin))) !important;
                transform: none !important;
                margin-bottom: 2px;
            }

            .sidebar-nav {
                display: none;
            }

            /* Menu actif (Déplié) */
            .sidebar.menu-active {
                height: calc(100vh - (2 * var(--sp-margin))) !important;
                overflow-y: auto;
            }

            .sidebar.menu-active .sidebar-nav {
                display: flex;
                min-height: calc(100% - var(--dim-header-height-mobile));
            }

            .sidebar .sidebar-header {
                position: sticky;
                top: 0;
                z-index: 20;
                background: var(--clr-bg-dark);
                border-radius: var(--br-large);
                padding: 8px 10px;
                border-bottom: 1px solid var(--clr-border);
                /* Réactiver la bordure pour le sticky header */
            }

            .sidebar-nav .primary-nav {
                transform: none !important;
                overflow-y: visible;
                padding-top: 15px;
            }

            /* Masquer les indicateurs desktop et le toggler chevron */
            .scroll-indicator,
            .sidebar-header .sidebar-toggler,
            .sidebar-nav .nav-tooltip {
                display: none !important;
                opacity: 0 !important;
                pointer-events: none !important;
            }

            /* Afficher le toggler du menu sur mobile */
            .sidebar-header .menu-toggler {
                display: flex;
                height: 30px;
                width: 30px;
            }

            .sidebar-nav .nav-list {
                padding: 0 10px;
            }

            .sidebar-nav .secondary-nav {
                padding: 10px;
            }

            /* Sous-menu sur mobile : Reste dans le flux */
            .sub-menu {
                position: static;
                width: 100%;
                padding: 0 8px;
                box-shadow: none;
            }

            .sub-menu li a.nav-link {
                padding: 8px 12px 8px 30px;
            }
        }

        /* -------------------------------------------------------------------------
   10. STYLE DU CONTENU PRINCIPAL (Pour le décalage)
   ------------------------------------------------------------------------- */

        @media (min-width: 1025px) {

            /* Utiliser 1025px pour éviter le conflit avec le max-width: 1024px */
            .content-main {
                flex-grow: 1;
                padding: 24px !important;
                transition: margin-left 0.3s ease-in-out;

                /* Décalage pour la sidebar dépliée */
                margin-left: calc(var(--dim-sidebar-full) + (2 * var(--sp-margin)));
            }

            .sidebar.collapsed~.content-main {
                /* Décalage pour la sidebar réduite */
                margin-left: calc(var(--dim-sidebar-collapsed) + (2 * var(--sp-margin)));
            }
        }

        /* --- Style général du calendrier --- */
        .calendar-container {
            background: rgba(255, 255, 255, 0.05);
            /* léger fond transparent */
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
            padding: 30px;
            backdrop-filter: blur(8px);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        /* Effet au survol */
        .calendar-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.5);
        }

        /* Titre du calendrier */
        .calendrier-titre {
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #00d4ff;
            /* bleu néon */
            text-shadow: 0 0 8px #00d4ff80;
        }

        /* Style du contenu du calendrier (ex : FullCalendar, tableau, etc.) */
        #calendar table {
            width: 100%;
            border-collapse: collapse;
            color: #f8f9fa;
        }

        #calendar th {
            background-color: rgba(0, 212, 255, 0.2);
            color: #00d4ff;
            text-align: center;
            padding: 10px;
            border-radius: 8px;
        }

        #calendar td {
            text-align: center;
            padding: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: background 0.2s ease;
        }

        /* Effet hover sur les jours */
        #calendar td:hover {
            background-color: rgba(0, 212, 255, 0.1);
            cursor: pointer;
        }

        /* Exemple si tu veux colorer les jours avec événements */
        #calendar .event-day {
            background: rgba(0, 212, 255, 0.2);
            border-radius: 10px;
            color: #00d4ff;
            font-weight: bold;
        }

        .calendar-container {
            animation: fadeIn 0.8s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* --- Style global du calendrier --- */
        #calendar {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 15px;
            box-shadow: 0 0 25px rgba(0, 212, 255, 0.3);
            padding: 25px;
            backdrop-filter: blur(12px);
            color: #f5f5f5;
            font-family: 'Poppins', 'Segoe UI', sans-serif;
            border: 1px solid rgba(0, 212, 255, 0.2);
            width: 100%;
            max-width: 100%;
            margin: auto;
            overflow-x: auto;
        }

        #calendar-admin {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 15px;
            box-shadow: 0 0 25px rgba(0, 212, 255, 0.3);
            padding: 25px;
            backdrop-filter: blur(12px);
            color: #f5f5f5;
            font-family: 'Poppins', 'Segoe UI', sans-serif;
            border: 1px solid rgba(0, 212, 255, 0.2);
            width: 100%;
            max-width: 100%;
            margin: auto;
            overflow-x: auto;
        }

        #calendar-admin .fc-daygrid-dot-event.fc-event-mirror,
        .fc-daygrid-dot-event:hover {
            background: blue !important;
        }

        /* --- Titres et en-têtes du calendrier --- */
        .fc-toolbar-title {
            color: #00d4ff;
            font-size: 1.8rem;
            font-weight: 700;
            text-shadow: 0 0 8px rgba(0, 212, 255, 0.6);
            letter-spacing: 1px;
        }

        /* --- En-têtes des jours (Lun, Mar, etc.) --- */
        .fc-col-header-cell-cushion {
            color: #00d4ff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.8px;
        }

        /* --- Jours normaux --- */
        .fc-daygrid-day-number {
            color: #fff !important;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        #calendar-admin .fc-daygrid-day-number {
            color: blue !important;
        }

        .fc-daygrid-day:hover .fc-daygrid-day-number {
            color: #00d4ff !important;
            text-shadow: 0 0 6px rgba(0, 212, 255, 0.5);
        }

        .fc .fc-daygrid-day {
            min-height: 80px;
            padding: 5px;
        }

        /* Texte plus petit sur mobile */
        @media (max-width: 768px) {
            .fc-toolbar-title {
                font-size: 1.2rem;
            }

            .fc-button {
                font-size: 0.8rem;
                padding: 0.25rem 0.5rem;
            }

            .fc-daygrid-day-number {
                font-size: 0.8rem;
            }

            .fc-event {
                font-size: 0.7rem;
                padding: 2px 4px;
            }
        }

        /* Scroll horizontal pour les vues de liste */
        .fc .fc-view-harness {
            overflow-x: auto;
        }

        /* --- JOUR ACTUEL (mise en avant forte, lumineuse et stylée) --- */
        .fc .fc-daygrid-day.fc-day-today {
            background: radial-gradient(circle at center,
                    rgba(0, 234, 255, 0.4) 0%,
                    rgba(0, 234, 255, 0.2) 40%,
                    rgba(15, 23, 42, 0.8) 100%);
            border: 2px solid rgba(0, 234, 255, 0.9);
            box-shadow:
                0 0 10px rgba(0, 234, 255, 0.6),
                0 0 25px rgba(99, 102, 241, 0.4);
            border-radius: 10px;
            position: relative;
            transition: all 0.4s ease;
        }

        /* Effet lumineux pulsé (optionnel mais superbe) */
        .fc .fc-daygrid-day.fc-day-today::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 10px;
            background: radial-gradient(circle at center,
                    rgba(0, 234, 255, 0.25),
                    transparent 70%);
            animation: glowPulse 3s ease-in-out infinite;
            z-index: 0;
        }

        /* Texte plus lisible et en avant */
        .fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
            color: #ffffff !important;
            font-weight: 700;
            text-shadow: 0 0 6px rgba(0, 234, 255, 0.9);
            position: relative;
            z-index: 2;
        }

        /* --- Animation lumineuse --- */
        @keyframes glowPulse {

            0%,
            100% {
                opacity: 0.3;
                transform: scale(1);
            }

            50% {
                opacity: 0.8;
                transform: scale(1.05);
            }
        }

        .fc-day-future {
            pointer-events: visible;
            cursor: pointer;
        }

        /* --- Jours passés : barrés diagonalement avec style --- */
        #calendar .fc-daygrid-day.past-day {
            position: relative;
            color: #ff4444 !important;
            opacity: 0.6;
            overflow: hidden;
        }

        #calendar-admin .fc-daygrid-day.past-day {
            position: relative;
            color: #ff4444 !important;
            opacity: 0.6;
            overflow: hidden;
        }

        /* ✅ La diagonale stylée rouge néon (haut droite → bas gauche) */
        .fc-daygrid-day.past-day::after {
            content: "";
            position: absolute;
            top: -5px;
            right: -5px;
            bottom: -5px;
            left: -5px;
            background: linear-gradient(135deg, transparent 47%, rgba(255, 60, 60, 0.8) 48%, rgba(255, 60, 60, 0.8) 52%, transparent 53%);
            z-index: 1;
            pointer-events: none;
        }

        /* --- Boutons de navigation --- */
        .fc-button {
            background: rgba(0, 212, 255, 0.1) !important;
            color: #00d4ff !important;
            border: 1px solid rgba(0, 212, 255, 0.3) !important;
            border-radius: 8px !important;
            transition: all 0.3s ease;
            text-transform: capitalize;
            font-weight: 600;
        }

        .fc-button:hover {
            background: rgba(0, 212, 255, 0.3) !important;
            color: #fff !important;
            box-shadow: 0 0 10px rgba(0, 212, 255, 0.6);
        }

        /* --- Bouton actif (vue sélectionnée) --- */
        .fc-button-active {
            background: linear-gradient(135deg, #00d4ff, #007bff) !important;
            color: #000 !important;
            font-weight: 700;
            box-shadow: 0 0 15px rgba(0, 212, 255, 0.8);
        }

        /* --- Événements (cases) --- */
        .fc-event {
            background: linear-gradient(135deg, #00d4ff, #007bff);
            border: none;
            border-radius: 8px;
            color: #fff;
            padding: 4px 8px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: transform 0.2s ease, box-shadow 0.3s ease;
        }

        .fc-event:hover {
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(0, 212, 255, 0.8);
        }

        /* --- Message "Aucun événement" --- */
        .fc .fc-no-events {
            color: #00d4ff;
            font-style: italic;
            opacity: 0.8;
        }

        /* --- Modal (détails d'événement) --- */
        .modal-content {
            background: linear-gradient(145deg, #0c0c0c, #1a1a1a);
            color: #fff;
            border: 1px solid rgba(0, 212, 255, 0.3);
            box-shadow: 0 0 30px rgba(0, 212, 255, 0.4);
        }

        .modal-header,
        .modal-footer {
            border: none;
        }

        .btn-outline-info {
            border-color: #00d4ff;
            color: #00d4ff;
        }

        .btn-outline-info:hover {
            background-color: #00d4ff;
            color: #000;
            box-shadow: 0 0 10px rgba(0, 212, 255, 0.8);
        }


        /* --- Animation d'apparition --- */
        #calendar {
            animation: fadeIn 0.8s ease;
        }

        #calendar td:hover .fc-day-today {
            background-color: white;
        }

        #calendar-admin {
            animation: fadeIn 0.8s ease;
        }

        #calendar-admin td:hover .fc-day-today {
            background-color: white;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #eventModal .modal-content {
            border-radius: 18px;
            background: linear-gradient(135deg,
                    rgba(17, 22, 34, 0.9) 0%,
                    /* Noir profond (base avva39) */
                    rgba(0, 229, 255, 0.7) 40%,
                    /* Bleu / Cyan électrique */
                    rgba(255, 46, 99, 0.6) 75%,
                    /* Rouge vif */
                    rgba(255, 221, 0, 0.5) 100%
                    /* Jaune éclatant */
                );
            box-shadow: 0 0 25px rgba(0, 234, 255, 0.2), 0 0 60px rgba(99, 102, 241, 0.15);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(0, 234, 255, 0.3);
            color: #f8fafc;
            transition: all 0.4s ease;
        }

        #eventModal .modal-header {
            border-bottom: 1px solid rgba(0, 234, 255, 0.3);
        }

        #eventModal .modal-footer {
            border-top: 1px solid rgba(99, 102, 241, 0.3);
        }

        #eventModalLabel {
            color: #ffdd00 !important;
            /* Jaune vif avva39 pour le texte */
            text-shadow: 0 1px 4px rgba(0, 0, 0, 1) !important;
            /* Ombre noire totale à 100% pour un contraste parfait */
            font-weight: 600;
            font-size: 1.3rem;
        }

        #eventModalBody .text-info {
            color: #ffdd00 !important;
            /* Jaune vif avva39 pour le texte */
            text-shadow: 0 1px 4px rgba(0, 0, 0, 1) !important;
            /* Ombre noire totale à 100% pour un contraste parfait */
        }

        #rapportsAccordion .text-info {
            color: #1037a0 !important;
        }

        #eventModalBody {
            max-height: 70vh;
            overflow-y: auto;
            line-height: 1.6;
        }

        /* Bouton “Fermer” */
        #eventModal .btn-outline-info {
            color: #00eaff;
            border-color: rgba(0, 234, 255, 0.6);
            font-weight: 500;
            transition: all 0.3s ease;
        }

        #eventModal .btn-outline-info:hover {
            background: linear-gradient(135deg, #00eaff, #6366f1);
            border: none;
            color: #0f172a;
            box-shadow: 0 0 12px rgba(0, 234, 255, 0.8);
        }

        /* --- Titre dégradé --- */
        .text-gradient {
            background: linear-gradient(90deg, #00d4ff, #007bff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
            letter-spacing: 1px;
        }

        /* --- Champs glass modernisés --- */
        .glass-input {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(0, 212, 255, 0.4);
            border-radius: 12px;
            padding: 10px 14px;
            backdrop-filter: blur(10px);
            box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.3),
                0 0 8px rgba(0, 212, 255, 0.3);
            transition: all 0.3s ease;
        }

        .glass-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .glass-input:focus {
            background: rgba(0, 212, 255, 0.1);
            border-color: #00d4ff;
            outline: none;
            box-shadow: 0 0 12px rgba(0, 212, 255, 0.8),
                inset 0 0 8px rgba(0, 123, 255, 0.5);
        }

        /* --- Optionnel : effet "hover" --- */
        .glass-input:hover {
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 10px rgba(0, 212, 255, 0.4);
        }

        /* --- Boutons stylés --- */
        .btn-gradient {
            background: linear-gradient(90deg, #00d4ff, #007bff);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 10px 18px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            background: linear-gradient(90deg, #00b4ff, #0056ff);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 212, 255, 0.5);
        }

        /* --- Bouton supprimer date --- */
        .remove-date-btn {
            border-radius: 0 10px 10px 0;
            border: none;
            color: #ff6b6b;
            background: rgba(255, 0, 0, 0.1);
            transition: all 0.2s ease;
        }

        .remove-date-btn:hover {
            background: rgba(255, 0, 0, 0.3);
            color: #fff;
        }

        /* Style glass pour le select */
        /* --- Sélecteur glass modernisé --- */
        .glass-select {
            background: rgba(255, 255, 255, 0.1);
            color: #eaf6ff;
            border: 1px solid rgba(0, 212, 255, 0.4);
            border-radius: 12px;
            padding: 10px 14px;
            backdrop-filter: blur(10px);
            box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.3),
                0 0 8px rgba(0, 212, 255, 0.3);
            transition: all 0.3s ease;
            appearance: none;
            /* supprime la flèche native */
            background-image: linear-gradient(90deg, rgba(0, 212, 255, 0.5), rgba(0, 123, 255, 0.3));
            background-size: 200% 100%;
            background-position: right center;
        }

        /* --- Survol et focus --- */
        .glass-select:hover {
            background-position: left center;
            box-shadow: 0 0 10px rgba(0, 212, 255, 0.4);
        }

        .glass-select:focus {
            background: rgba(0, 212, 255, 0.1);
            border-color: #00d4ff;
            outline: none;
            box-shadow: 0 0 12px rgba(0, 212, 255, 0.8),
                inset 0 0 8px rgba(0, 123, 255, 0.5);
            color: #fff;
        }

        /* --- Options du menu déroulant --- */
        .glass-select option {
            background: rgba(10, 15, 30, 0.9);
            color: #eaf6ff;
            border: none;
        }

        /* --- Icône de flèche personnalisée --- */
        .glass-select {
            background-image:
                linear-gradient(90deg, rgba(0, 212, 255, 0.5), rgba(0, 123, 255, 0.3)),
                url("data:image/svg+xml;utf8,<svg fill='%23eaf6ff' height='20' viewBox='0 0 24 24' width='20' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/></svg>");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 200% 100%, 16px;
            padding-right: 36px;
        }

        /* --- MODAL MÉTÉO STYLÉ --- */
        #eventModal .modal-content {
            border-radius: 18px;
            background: linear-gradient(135deg,
                    rgba(10, 14, 23, 0.95) 0%,
                    /* Noir/Bleu nuit très profond */
                    rgba(0, 100, 120, 0.75) 40%,
                    /* Cyan foncé / Canard */
                    rgba(140, 20, 50, 0.7) 75%,
                    /* Rouge/Bordeaux assombri */
                    rgba(160, 130, 0, 0.6) 100%
                    /* Jaune ambré / Sombre */
                );
            box-shadow: 0 0 25px rgba(0, 150, 180, 0.25), 0 0 60px rgba(10, 14, 23, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(0, 180, 210, 0.25);
            color: #ffffff !important;
            transition: all 0.4s ease;
        }

        @media (max-width: 992px) {
            #weatherModal .modal-dialog {
                position: relative !important;
                right: 0 !important;
            }
        }

        /* En-tête du modal */
        #weatherModal .modal-header {
            border-bottom: 1px solid rgba(0, 234, 255, 0.3);
            background: linear-gradient(90deg, rgba(0, 234, 255, 0.1), rgba(99, 102, 241, 0.1));
        }

        #weatherModalLabel {
            color: #555;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* Pied du modal */
        #weatherModal .modal-footer {
            border-top: 1px solid rgba(0, 234, 255, 0.3);
        }

        /* Bouton fermer */
        #weatherModal .btn-secondary {
            background: linear-gradient(135deg, #00eaff, #6366f1);
            border: none;
            color: #0f172a;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        #weatherModal .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 15px rgba(0, 234, 255, 0.6);
        }

        /* --- MÉTÉO ACTUELLE --- */
        .current-weather {
            background: linear-gradient(135deg, rgba(0, 234, 255, 0.2), rgba(99, 102, 241, 0.2));
            border: 1px solid rgba(0, 234, 255, 0.3);
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 234, 255, 0.15);
            color: #ffffff;
            text-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
            transition: all 0.3s ease;
        }

        .current-weather:hover {
            transform: scale(1.02);
            box-shadow: 0 0 25px rgba(0, 234, 255, 0.3);
        }

        /* --- CARTES DE PRÉVISION (24h & 5 jours) --- */
        #forecast-24h div,
        #forecast-5days div {
            background: linear-gradient(135deg, rgba(0, 234, 255, 0.15), rgba(99, 102, 241, 0.15));
            border: 1px solid rgba(0, 234, 255, 0.2);
            border-radius: 12px;
            padding: 12px;
            text-align: center;
            min-width: 90px;
            flex: 0 0 auto;
            color: black;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(0, 234, 255, 0.1);
            transition: all 0.3s ease;
        }

        #forecast-24h div:hover,
        #forecast-5days div:hover {
            transform: translateY(-6px) scale(1.03);
            background: linear-gradient(135deg, rgba(0, 234, 255, 0.25), rgba(99, 102, 241, 0.25));
            box-shadow: 0 0 15px rgba(0, 234, 255, 0.3);
        }

        /* Icônes météo */
        #forecast-24h div img,
        #forecast-5days div img {
            width: 45px;
            height: 45px;
            margin-bottom: 8px;
        }

        /* --- SCROLLBAR STYLÉE POUR 24h --- */
        #forecast-24h {
            scrollbar-width: thin;
            scrollbar-color: #00eaff transparent;
        }

        #forecast-24h::-webkit-scrollbar {
            height: 6px;
        }

        #forecast-24h::-webkit-scrollbar-track {
            background: transparent;
        }

        #forecast-24h::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #00eaff, #6366f1);
            border-radius: 3px;
        }

        /* --- TITRES DE SECTION --- */
        #weatherModal h6 {
            color: #555;
            font-weight: 600;
            margin-top: 15px;
        }

        /* --- Flèche de retour stylée --- */
        .avva-back-arrow {
            position: absolute;
            top: 25px;
            left: 35px;
            font-size: 2.2rem;
            color: #00eaff;
            text-decoration: none;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            filter: drop-shadow(0 0 8px rgba(0, 234, 255, 0.6));
        }

        /* Effet au survol : glow + rotation légère */
        .avva-back-arrow:hover {
            color: #00f5ff;
            transform: translateX(-5px) scale(1.1);
            filter: drop-shadow(0 0 15px rgba(0, 234, 255, 0.9));
        }

        /* Animation d’apparition fluide */
        .avva-back-arrow i {
            animation: arrowPulse 2.5s ease-in-out infinite;
        }

        @keyframes arrowPulse {

            0%,
            100% {
                transform: translateX(0);
                opacity: 1;
            }

            50% {
                transform: translateX(-4px);
                opacity: 0.8;
            }
        }

        /* --- Flèche de retour stylée --- */
        .avva-back-arrow-2 {
            position: absolute;
            top: 25px;
            right: 35px;
            font-size: 2.2rem;
            color: #00eaff;
            text-decoration: none;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            filter: drop-shadow(0 0 8px rgba(0, 234, 255, 0.6));
        }

        /* Effet au survol : glow + rotation légère */
        .avva-back-arrow-2:hover {
            color: #00f5ff;
            transform: translateX(-5px) scale(1.1);
            filter: drop-shadow(0 0 15px rgba(0, 234, 255, 0.9));
        }

        /* Animation d’apparition fluide */
        .avva-back-arrow-2 i {
            animation: arrowPulse2 2.5s ease-in-out infinite;
        }

        @keyframes arrowPulse2 {

            0%,
            100% {
                transform: translateX(-4px);
                opacity: 1;
            }

            50% {
                transform: translateX(0);
                opacity: 0.8;
            }
        }

        /* Adaptation sur mobile */
        @media (max-width: 768px) {
            .avva-back-arrow {
                top: 15px;
                left: 15px;
                font-size: 1.8rem;
            }

            .avva-back-arrow-2 {
                top: 15px;
                right: 15px;
                font-size: 1.8rem;
            }
        }

        /* 🌈 Effet coloré pour le label "facultatif" */
        .facultatif-label {
            background: linear-gradient(90deg, #ff7eb9, #ff758c, #ffb199);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        /* Animation subtile au survol */
        .facultatif-label:hover {
            filter: brightness(1.2);
        }

        /* Encadré stylé pour les blocs */
        .date-block {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .date-block:hover {
            transform: scale(1.01);
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.15);
        }

        /* Bouton d’ajout stylé */
        .btn-gradient {
            background: linear-gradient(90deg, #007cf0, #00dfd8);
            color: #fff;
            border: none;
            transition: 0.3s;
        }

        .btn-gradient:hover {
            background: linear-gradient(90deg, #00dfd8, #007cf0);
            transform: scale(1.03);
        }

        /* Conteneur pour texte d'événement */
        .fc-event .fc-event-title {
            display: block;
            white-space: nowrap;
            /* Une seule ligne */
            overflow: hidden;
            /* Cache le dépassement */
            text-overflow: ellipsis;
            /* Ajoute "..." si le texte est trop long */
        }

        /* Défilement horizontal automatique au survol */
        .fc-event .fc-event-title.marquee {
            overflow: hidden;
            position: relative;
        }

        .fc-event .fc-event-title.marquee span {
            display: inline-block;
            padding-left: 100%;
            animation: marquee 8s linear infinite;
        }

        @keyframes marquee {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        /* Affichage des heures sous le titre */
        .fc-event .fc-event-time {
            display: block;
            font-size: 0.75rem;
            color: #f0f0f0;
        }

        /* Overlay du modal */
        #modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
        }

        .modal-admin {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(8px);
            overflow-y: auto;
            /* ✅ Permet de scroller si le contenu dépasse */
            padding: 60px 20px;
            /* Un peu d’espace en haut et en bas */
        }

        /* Card glassmorphism */
        .modal-content {
            background: rgba(255, 255, 255, 0.07);
            backdrop-filter: blur(12px);
            border-radius: 15px;
            padding: 25px 35px;
            max-width: 900px;
            /* 🔥 Plus large qu’avant */
            width: 95%;
            margin: 0 auto;
            color: #fff;
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.6);
            animation: slideDown 0.4s ease forwards;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        /* Animation */
        @keyframes slideDown {
            from {
                transform: translateY(-40px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Header */
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding-bottom: 10px;
            margin-bottom: 20px;
            position: sticky;
            top: 0;
            background: rgba(255, 255, 255, 0.07);
            backdrop-filter: blur(10px);
            z-index: 10;
            /* ✅ Toujours au-dessus du contenu */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            border-radius: 10px;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 1.6em;
            font-weight: 600;
            color: white;
        }

        .modal-body {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .close {
            cursor: pointer;
            font-size: 24px;
            font-weight: bold;
            color: black !important;
        }

        .close:hover {
            color: #ff6b6b;
        }

        /* Body */
        .modal-body {
            padding: 20px;
            color: #fff;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: white;
            font-size: 25px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.05);
            color: white;
            font-size: 0.95em;
            outline: none;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .note-placeholder {
            color: white;
        }

        /* Dates inline */
        .date-group {
            display: flex;
            gap: 15px;
        }

        .date-group>div {
            flex: 1;
        }

        @media (max-width: 992px) {
            .modal-content {
                max-width: 95%;
                padding: 25px;
            }
        }

        @media (max-width: 768px) {
            .modal-content {
                padding: 20px;
                border-radius: 10px;
            }

            .date-group {
                flex-direction: column;
            }

            .modal-header h3 {
                font-size: 1.3em;
            }

            .btn-save,
            .btn-delete {
                width: 100%;
                margin-top: 10px;
            }

            .modal-footer {
                display: flex;
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
        }

        .date-group input {
            width: 100%;
        }

        @media (max-width: 576px) {
            .date-group {
                flex-direction: column;
            }
        }

        /* Footer */
        .modal-footer {
            text-align: right;
            margin-top: 15px;
            display: contents;
        }

        .btn-save {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 0.95em;
            transition: all 0.3s ease;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-delete {
            background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
            /* Dégradé rouge */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 0.95em;
            transition: all 0.3s ease;
        }

        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* 🎨 Harmoniser la couleur du header et du champ */
        .note-editor.note-frame .note-toolbar,
        .note-editor.note-frame .note-editing-area .note-editable {
            background-color: rgba(255, 255, 255, 0.05);
            /* même couleur que les champs Bootstrap par défaut */
            border-color: #ced4da;
        }

        /* Harmoniser le contour du bloc entier */
        .note-editor.note-frame {
            border: 1px solid #ced4da;
            border-radius: .375rem;
        }

        .sortie .note-editor.note-frame {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(0, 212, 255, 0.4);
            border-radius: 12px;
            padding: 10px 14px;
            backdrop-filter: blur(10px);
            box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.3),
                0 0 8px rgba(0, 212, 255, 0.3);
            transition: all 0.3s ease;
        }

        .sortie:hover .note-editor.note-frame {
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 10px rgba(0, 212, 255, 0.4);
        }

        .sortie .note-statusbar {
            background: rgba(0, 212, 255, 0.4) !important;
            border-top: 1px solid rgba(0, 212, 255, 0.4) !important;
        }

        /* Adapter la couleur du texte */
        #description_date_event .note-editable {
            color: #000;
        }

        #eventModal .note-editable {
            color: white;
        }

        /* Supprimer le contraste blanc du header */
        .note-toolbar {
            border-bottom: 1px solid #ced4da;
        }

        /* Ajuster les boutons pour qu’ils se fondent */
        .note-btn {
            border: none;
        }

        .note-btn:hover {
            border-radius: .375rem;
        }

        /* 🌌 Compte rendu stylé (Summernote) */
        .compte-rendu-group .note-editor {
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            overflow: hidden;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.4);
            transition: all 0.3s ease-in-out;
            max-height: 400px;
        }

        .compte-rendu-group .note-editor:hover {
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 0 10px rgba(106, 17, 203, 0.4);
        }

        /* Barre d’outils */
        .compte-rendu-group .note-toolbar {
            background: rgba(255, 255, 255, 0.08) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            padding: 8px 10px !important;
        }

        .compte-rendu-group .note-toolbar button {
            background: transparent;
            border: none !important;
            color: black !important;
            font-size: 14px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .compte-rendu-group .note-toolbar button .note-color-btn {
            background: auto !important;
            border: none !important;
            color: black !important;
            font-size: 14px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .compte-rendu-group .note-toolbar button:hover {
            background: rgba(255, 255, 255, 0.15) !important;
            color: black !important;
        }

        /* Zone de texte */
        .compte-rendu-group .note-editable {
            background: rgba(255, 255, 255, 0.03) !important;
            color: black !important;
            font-family: 'Inter', sans-serif;
            font-size: 0.95em;
            line-height: 1.6;
            padding: 15px;
            min-height: 180px;
            transition: background 0.3s ease;
        }

        .compte-rendu-group .note-editable:focus {
            background: rgba(255, 255, 255, 0.06) !important;
        }

        /* Placeholder (texte d’aide) */
        .compte-rendu-group .note-placeholder {
            color: rgba(255, 255, 255, 0.5) !important;
            font-style: italic;
        }

        /* Focus / Active */
        .compte-rendu-group .note-editor.note-frame.focused {
            border: 1px solid #6a11cb;
            box-shadow: 0 0 15px rgba(106, 17, 203, 0.5);
        }

        /* Scrollbar stylée */
        .compte-rendu-group .note-editable::-webkit-scrollbar {
            width: 8px;
        }

        .compte-rendu-group .note-editable::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }

        .compte-rendu-group .note-editable::-webkit-scrollbar-thumb:hover {
            background-color: rgba(255, 255, 255, 0.4);
        }

        .compte-rendu-group .note-editor {
            animation: fadeInUp 0.4s ease forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .compte-rendu-group .note-editor {
                max-height: 300px;
            }
        }


        /* ============================
   🌈 DROPDOWNS DU COMPTE RENDU
   ============================ */

        /* Menus déroulants (police, style, couleur, etc.) */
        .compte-rendu-group .note-dropdown-menu,
        .compte-rendu-group .dropdown-style {
            background: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.6);
            padding: 5px 0;

            max-height: 250px;
            /* Limite la hauteur */
            overflow-y: auto;
            /* Active le scroll vertical */
            overflow-x: hidden;
            /* Cache l’overflow horizontal */
        }

        .compte-rendu-group .note-dropdown-menu::-webkit-scrollbar {
            width: 6px;
        }

        .compte-rendu-group .note-dropdown-menu::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        .compte-rendu-group .note-dropdown-menu::-webkit-scrollbar-track {
            background: transparent;
        }

        /* Items des dropdowns */
        .compte-rendu-group .note-dropdown-menu li a,
        .compte-rendu-group .note-dropdown-menu .note-dropdown-item {
            color: black !important;
            background: transparent !important;
            font-size: 0.9em;
            padding: 8px 15px;
            transition: background 0.2s, color 0.2s;
        }

        /* Survol d’un item */
        .compte-rendu-group .note-dropdown-menu li a:hover,
        .compte-rendu-group .note-dropdown-menu .note-dropdown-item:hover {
            background: rgba(255, 255, 255, 0.15) !important;
            color: black !important;
        }

        /* Séparateurs */
        .compte-rendu-group .note-dropdown-menu .note-dropdown-divider {
            background-color: rgba(255, 255, 255, 0.15) !important;
        }

        /* Style du menu de titres (H1, H2...) */
        .compte-rendu-group .dropdown-style li a {
            color: black !important;
            background: transparent;
            padding: 8px 12px;
            border-radius: 6px;
        }

        .compte-rendu-group .dropdown-style li a:hover {
            background: rgba(255, 255, 255, 0.15) !important;
            color: black !important;
        }

        /* ============================
   Summernote modals (image, link, table…) dans le compte rendu
   ============================ */
        .compte-rendu-group .note-modal,
        .compte-rendu-group .note-modal .note-modal-content {
            background: rgba(255, 255, 255, 0.95);
            /* fond sombre semi-transparent */
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            color: black;
            max-height: 80vh;
            /* pour scroll si modal trop grande */
            overflow-y: auto;
            overflow-x: hidden;
            padding: 15px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
        }

        /* Inputs et textarea dans les modals */
        .compte-rendu-group .note-modal input,
        .compte-rendu-group .note-modal textarea,
        .compte-rendu-group .note-modal select {
            background: rgba(255, 255, 255, 0.05);
            color: black;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            padding: 8px 10px;
            width: 100%;
        }

        /* Buttons dans les modals */
        .compte-rendu-group .note-modal button {
            background: rgba(255, 255, 255, 0.1);
            color: black;
            border: none;
            border-radius: 6px;
            padding: 6px 12px;
            margin-right: 5px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .compte-rendu-group .note-modal button:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        #notificationModal {
            border-radius: 0.5rem;
            color: #fff;
            font-weight: 500;
        }

        #notificationToast.success {
            background-color: #28a745;
        }

        #notificationToast.error {
            background-color: #dc3545;
        }

        #notificationToast.info {
            background-color: #17a2b8;
        }

        #notificationToast.warning {
            background-color: #ffc107;
            color: #212529;
        }

        /* === Section principale === */
        .content-section-page {
            color: #e6f1f5;
            font-family: "Poppins", sans-serif;
        }

        /* === Titre principal === */
        .content-section-page .text-gradient {
            background: linear-gradient(90deg, #00C6FF, #0072FF);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .content-section-page .underline {
            width: 120px;
            height: 4px;
            background: linear-gradient(90deg, #00C6FF, #0072FF);
            border-radius: 5px;
        }

        /* === Bloc de contenu === */
        .content-section-page .content {
            background:
                <?= $index ? $settings->getPageFondColor() : '' ?>
                <?= $index ? $settings->getPageFondTransparent() == 1 ? '0d' : '' : '' ?>
            ;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            box-shadow: 0 0 40px rgba(0, 114, 255, 0.1);
            backdrop-filter: blur(10px);
            transition: all 0.4s ease;
            padding: 8px;
        }

        .content-section-page .content:hover {
            box-shadow: 0 0 60px rgba(0, 180, 255, 0.25);
        }

        /* === Texte du contenu === */
        .content-section-page .content-inner p {
            margin-bottom: 1.5rem;
        }

        .content-section-page .content-inner h2,
        .content-section-page .content-inner h3 {
            color: #66ccff;
            margin-top: 1.5rem;
        }

        .content-section-page .content-inner a {
            color: #00C6FF;
            text-decoration: underline;
        }

        /* === Section principale === */
        .compte-rendu-section {
            color: #e6f1f5;
            font-family: "Poppins", sans-serif;
            min-height: 100vh;
        }

        /* === Titre principal === */
        .compte-rendu-section .text-gradient {
            background: linear-gradient(90deg, #00C6FF, #0072FF);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .compte-rendu-section .underline {
            width: 120px;
            height: 4px;
            background: linear-gradient(90deg, #00C6FF, #0072FF);
            border-radius: 5px;
        }

        /* === Bloc de contenu === */
        .compte-rendu-section .content {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            box-shadow: 0 0 40px rgba(0, 114, 255, 0.1);
            backdrop-filter: blur(10px);
            transition: all 0.4s ease;
        }

        .compte-rendu-section .content:hover {
            box-shadow: 0 0 60px rgba(0, 180, 255, 0.25);
        }

        /* === Texte du contenu === */
        .compte-rendu-section .content-inner p {
            margin-bottom: 1.5rem;
        }

        .compte-rendu-section .content-inner h2,
        .compte-rendu-section .content-inner h3 {
            color: #66ccff;
            margin-top: 1.5rem;
        }

        .compte-rendu-section .content-inner a {
            color: #00C6FF;
            text-decoration: underline;
        }

        /* === Bouton retour === */
        .compte-rendu-section .retour-btn {
            border-width: 2px;
            transition: all 0.3s ease;
        }

        .compte-rendu-section .retour-btn:hover {
            background: #00C6FF;
            border-color: #00C6FF;
            color: #fff;
            transform: scale(1.05);
        }

        /* === Responsive === */
        @media (max-width: 768px) {
            .compte-rendu-section .display-4 {
                font-size: 2rem;
            }

            .compte-rendu-section .content {
                padding: 1.5rem;
            }
        }

        /* MENU SEARCH-RESULTS PREMIUM */
        .search-results {
            position: absolute;
            display: none;
            top: 100%;
            left: 0;
            width: 100%;
            max-height: 300px;
            overflow-x: hidden;
            overflow-y: auto;
            background: linear-gradient(135deg, #0d47a1, #1976d2, #0d47a1);
            background-size: 400% 400%;
            animation: gradientBG 10s ease infinite;
            color: #ffeb3b;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 0.95rem;
            border-radius: 20px;
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.35);
            margin-top: 8px;
            font-size: 0.9rem;
            z-index: 1000;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            scrollbar-width: thin;
            scrollbar-color: #ffeb3b #0d47a1;
        }

        /* Glow subtil au survol du menu */
        .search-results:hover {
            box-shadow: 0 15px 35px rgba(255, 235, 59, 0.5);
            transform: translateY(-2px);
        }

        /* Gradient animé */
        @keyframes gradientBG {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* ITEMS */
        .search-results div,
        .search-results li {
            padding: 12px 16px;
            cursor: pointer;
            border-bottom: 1px solid rgba(255, 235, 59, 0.2);

            /* Animation cascade */
            transform: translateX(-10px) scale(0.95);
            opacity: 0;
            animation: slideInGlow 0.4s forwards;
        }

        /* Animation différée pour cascade */
        .search-results div:nth-child(1),
        .search-results li:nth-child(1) {
            animation-delay: 0.05s;
        }

        .search-results div:nth-child(2),
        .search-results li:nth-child(2) {
            animation-delay: 0.1s;
        }

        .search-results div:nth-child(3),
        .search-results li:nth-child(3) {
            animation-delay: 0.15s;
        }

        /* ajoute d'autres items si nécessaire */

        @keyframes slideInGlow {
            to {
                transform: translateX(0) scale(1);
                opacity: 1;
            }
        }

        .custom-scrollbar {
            display: none;
            position: absolute;
            top: 0;
            right: 4px;
            width: 8px;
            height: 100%;
            background: transparent;
            border-radius: 20px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .custom-scrollbar.visible {
            opacity: 1;
        }

        .custom-thumb {
            display: none;
            width: 100%;
            background-color: rgba(255, 235, 59, 0.8);
            border-radius: 20px;
            position: absolute;
            top: 0;
            cursor: pointer;
            transition: background 0.2s;
        }

        .custom-thumb:hover {
            background-color: rgba(255, 235, 59, 1);
        }

        .search-results ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .search-results li {
            padding: 8px 15px;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }

        .search-results li:hover {
            background-color: #ffeb3b;
            color: #0d47a1;
        }

        .search-results div {
            padding: 8px 15px;
            font-weight: bold;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Dans votre fichier style.css */
        #upload-progress-container {
            display: none;
            /* Toujours caché par défaut */
            height: 20px;
            width: 250px;

            /* Propriétés pour la position fixe en haut à droite */
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            /* Très important pour être sûr qu'elle soit au-dessus de l'éditeur */
        }

        /* Mise en évidence du statut via la bordure gauche Bootstrap */
        .card.border-success {
            border-left: 5px solid var(--bs-success) !important;
        }

        .card.border-danger {
            border-left: 5px solid var(--bs-danger) !important;
        }

        .text-primary {
            color: var(--bs-primary) !important;
        }

        .bg-primary-subtle {
            background-color: var(--bs-primary-bg-subtle) !important;
        }

        /* Améliore la lisibilité du badge de date */
        .text-primary-emphasis {
            color: var(--bs-primary-text-emphasis) !important;
        }

        /* Style CSS pour désactiver le clic sur les liens lorsqu'ils sont fermés */
        .disabled-link {
            pointer-events: none;
            cursor: not-allowed;
        }

        .bouton-absolu-droite {
            position: absolute;
            right: -230px;
            top: 90px;
            z-index: 10;
            padding: 10px 15px;
        }

        /* Vous pouvez ajouter une media query pour les vues mobiles si besoin */
        @media (max-width: 1780px) {
            .bouton-absolu-droite {
                right: -200px;
                top: 140px;
            }
        }

        @media (max-width: 1400px) {
            .bouton-absolu-droite {
                right: -140px;
                top: 140px;
            }
        }

        @media (max-width: 1270px) {
            .bouton-absolu-droite {
                right: 20px;
                top: 125px;
            }
        }

        @media (max-width: 767.98px) {
            .bouton-absolu-droite {
                position: relative;
                right: auto;
                top: auto;
            }
        }

        /* Styles de la Modale */
        #imageModal {
            display: none;
            position: fixed;
            z-index: 999999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            /* Empêche le défilement du corps */
            background-color: rgba(0, 0, 0, 0.85);
            /* Fond plus sombre */
            justify-content: center;
            align-items: center;
        }

        #modalContent {
            background-color: #fff;
            width: 90%;
            /* Augmenté légèrement */
            max-width: 100%;
            height: 90%;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.6);
            display: flex;
            flex-direction: column;
            border-radius: 20px;
        }

        /* En-tête de la modale (Status et Fermeture) */
        #modalHeader {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            border-bottom: 1px solid #eee;
            background-color: #f7f7f7;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
        }

        #imageStatusText {
            font-size: 1.1em;
            font-weight: bold;
            color: #333;
        }

        #closeModal {
            color: #888;
            font-size: 36px;
            font-weight: normal;
            /* Plus fin */
            cursor: pointer;
            transition: color 0.2s;
        }

        #closeModal:hover,
        #closeModal:focus {
            color: #000;
        }

        /* Conteneur de défilement des images */
        #imageContainer {
            flex-grow: 1;
            overflow-y: auto;
            /* Zone de défilement principale */
            padding: 20px;
            text-align: center;
        }

        /* Style des images dans le conteneur */
        .modal-image {
            /* Utilisation de la classe pour cibler précisément */
            max-width: 100%;
            width: auto;
            /* Permet à l'image de prendre sa taille naturelle ou 100% */
            height: auto;
            display: block;
            margin: 30px auto;
            /* Marge entre les images */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 70%;
        }

        /* Style de la zone de Drag & Drop */
        .drop-zone {
            border: 2px dashed #007bff;
            /* Bordure primaire */
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            background-color: #f8f9fa;
            /* Arrière-plan légèrement gris */
        }

        /* État actif lors du survol (drag over) */
        .drop-zone.drag-over {
            background-color: #e9f5ff;
            /* Bleu très clair */
            border-color: #0056b3;
            /* Bleu foncé */
            transform: scale(1.02);
            /* Petit effet de zoom */
        }

        .drop-zone-content i {
            font-size: 2.5rem;
            color: #007bff;
            margin-bottom: 10px;
        }

        .drop-zone-content p {
            margin: 5px 0;
            color: #6c757d;
        }

        .btn-browse {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.2s;
        }

        .btn-browse:hover {
            background-color: #0056b3;
        }

        /* Style de l'affichage de l'état du fichier */
        .gpx-status-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 10px;
            background-color: #ffffff;
        }

        .gpx-status-text {
            margin: 0;
            font-weight: 500;
            color: #343a40;
            /* Rendre le nom du fichier troncable si trop long */
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            flex-grow: 1;
            padding-right: 10px;
        }

        .btn-remove-gpx {
            background: none;
            border: none;
            color: #dc3545;
            /* Rouge pour la suppression */
            cursor: pointer;
            font-size: 1.2rem;
            line-height: 1;
            padding: 0;
        }

        .btn-remove-gpx:hover {
            color: #bd2130;
        }

        /* 0. VARIABLES CSS (Thème Immersif) */
        :root {
            --color-theme-primary: #198754;
            /* Vert Principal (Success) */
            --color-theme-secondary: #0f663c;
            /* Vert plus foncé */
            --color-text-dark: #000000ff;
            --color-background-soft: #e9ecef;
            --shadow-subtle: 0 2px 8px rgba(0, 0, 0, 0.1);
            --header-height: 60px;
        }

        .content-section-page-cartoguide {
            font-family: "Poppins", sans-serif;
        }

        .content-section-page-cartoguide .content {
            position: relative;
            background: #ffffff 0d;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            box-shadow: 0 0 40px rgba(0, 114, 255, 0.1);
            backdrop-filter: blur(10px);
            transition: all 0.4s ease;
            padding: 8px;
            z-index: -99;
        }

        .text-theme-dark {
            color: var(--color-text-dark) !important;
        }

        /* 2. LOADER ET ANIMATION DE ROTATION */
        @keyframes spin-logo {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .loader-global {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            /* Centrage garanti */
            background-color: rgba(255, 255, 255, 0.98);
            /* Fond presque opaque */
            backdrop-filter: blur(5px);
            z-index: 9999;
            opacity: 1;
            transition: opacity 0.8s ease-out;
        }

        .loader-global.hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .loader-logo-pulsating {
            width: 500px;
            height: auto;
            animation: spin-logo 2s linear infinite;
            /* Rotation fluide et infinie */
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        /* 3. STRUCTURE DE L'APPLICATION (App Shell) */
        .app-shell {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* 4. BARRE D'ENTÊTE ET BOUTON DE RETOUR */
        .app-header {
            height: var(--header-height);
            background-color: #ffffff;
            border-bottom: 1px solid #dee2e6;
            flex-shrink: 0;
        }

        .back-button-floating {
            background-color: var(--color-theme-primary);
            color: white;
            border: none;
            font-weight: 600;
            border-radius: 25px;
            padding: 8px 15px;
            transition: all 0.2s ease-out;
        }

        .back-button-floating:hover {
            background-color: var(--color-theme-secondary);
            transform: scale(1.05);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        /* 5. ZONE DE L'IFRAME (Maintenant le Conteneur de l'Application) */
        .app-iframe-area {
            flex-grow: 1;
            /* Prend tout l'espace restant */
            height: calc(100vh - var(--header-height));
        }

        /* 7. RESPONSIVE */
        @media (max-width: 767.98px) {
            .app-header .h5 {
                font-size: 1rem;
            }

            .d-none.d-md-inline {
                display: none !important;
            }
        }

        .ol-layer canvas {
            border-radius: 20px !important;
        }

        /* Ajoutez ce code à votre fichier CSS principal (ou dans une balise <style> temporaire) */
        @keyframes shake-alert {

            0%,
            100% {
                transform: translateX(0);
            }

            20%,
            60% {
                transform: translateX(-5px);
            }

            40%,
            80% {
                transform: translateX(5px);
            }
        }

        .animate__shake {
            animation: shake-alert 0.4s ease-in-out 1;
            /* Durée de 0.4s, une seule fois */
        }

        /* =======================================
   ANIMATIONS D'ENTRÉE PERSONNALISÉES
   ======================================= */

        /* 1. Apparition par le bas (pour le logo/bouton) */
        @keyframes customFadeInUp {
            from {
                opacity: 0;
                transform: translate3d(0, 20px, 0);
            }

            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        .custom-animate-fadeInUp {
            animation: customFadeInUp 0.6s ease-out both;
        }

        /* 2. Apparition par le haut (pour la carte) */
        @keyframes customFadeInDown {
            from {
                opacity: 0;
                transform: translate3d(0, -20px, 0);
            }

            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        .custom-animate-fadeInDown {
            animation: customFadeInDown 0.6s ease-out both;
        }

        /* 3. Simple fondu (pour les champs) */
        @keyframes customFadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .custom-animate-fadeIn {
            animation: customFadeIn 0.8s ease-out both;
        }

        /* 4. Animation de Secousse (pour l'erreur) */
        @keyframes shake-alert {

            0%,
            100% {
                transform: translateX(0);
            }

            20%,
            60% {
                transform: translateX(-5px);
            }

            40%,
            80% {
                transform: translateX(5px);
            }
        }

        .custom-animate-shake {
            animation: shake-alert 0.4s ease-in-out 1;
        }

        /* =======================================
   CLASSES DE DÉLAI 
   ======================================= */
        .delay-05s {
            animation-delay: 0.5s;
        }

        .delay-1s {
            animation-delay: 1s;
        }

        .delay-1-5s {
            animation-delay: 1.5s;
        }

        .delay-2s {
            animation-delay: 2s;
        }

        .delay-2-5s {
            animation-delay: 2.5s;
        }

        .delay-3s {
            animation-delay: 3s;
        }

        .delay-3-5s {
            animation-delay: 3.5s;
        }

        .paragraph-modern {
            text-align: center;
            margin: 2rem 0;
            position: relative;
            font-size: 1.1rem;
            font-weight: 700;
            color: #3282b8;
            text-transform: uppercase;
        }

        /*  */

        /* Ajustement pour le contenu principal */
        /* Il doit se décaler d'une distance égale à la Largeur du menu (250px) + Marge à gauche (ms-3 ≈ 1rem) */
        #main-content {
            margin-left: calc(250px + 1rem);
            padding: 20px;
        }

        /* Styles pour les sous-menus (pour rester vertical dans la sidebar) */
        /* #vertical-sidebar .dropdown-menu {
            position: static !important;
            transform: none !important;
            border: none;
            box-shadow: none;
            padding-left: 1rem;
            background-color: transparent;
        } */

        #contenu-page img {
            max-width: 200px;
            border-radius: 0.5rem;
        }

        .avva-footer-member {
            flex-direction: column;
        }
    </style>
    <?php
    $isCartoguide = strpos($_SERVER['REQUEST_URI'], '/cartoguide') !== false;
    ?>
    <link rel="manifest" href="<?php echo $isCartoguide ? '/manifest-cartoguide.webmanifest' : '/manifest-avva39.webmanifest'; ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body>
    <?php if ($index): ?>
        <?php
        // 1. Détection de l'URL actuelle
        $currentUri = strtolower($_SERVER['REQUEST_URI'] ?? '');
        $isCartoguide = str_contains($currentUri, '/cartoguide');
        ?>
        <?php if (!$isCartoguide): ?>
            <style>
                /* ==========================================================================
               1. VARIABLES DE THÈME (CHARTE AVVA39 & MATERIAL YOU)
               ========================================================================== */
                :root {
                    /* --- Palette officielle AVVA39 --- */
                    --avva-red: #e30613;
                    /* Rouge dynamique AVVA */
                    --avva-yellow: #e2e600;
                    /* Jaune/Vert fluo maillot */
                    --avva-dark: #1a1a1a;
                    /* Noir / Anthracite */
                    --avva-light: #f4f6f8;
                    /* Fond clair */

                    /* --- Arrière-plans principaux --- */
                    --pwa-bg-body: var(--avva-light);
                    --pwa-card-bg: #ffffff;
                    --pwa-overlay-bg: rgba(26, 26, 26, 0.6);

                    /* --- Composants de navigation & Header --- */
                    --pwa-bg-header: rgba(255, 255, 255, 0.92);
                    --pwa-bg-nav: rgba(244, 246, 248, 0.90);
                    --pwa-border-color: rgba(227, 6, 19, 0.15);
                    --pwa-shadow-nav: 0 4px 12px rgba(0, 0, 0, 0.12);

                    /* --- Typographie & Couleurs d'accentuation --- */
                    --pwa-text-main: var(--avva-dark);
                    --pwa-text-inactive: #666666;
                    --pwa-text-active: var(--avva-red);
                }

                /* --- Mode Sombre (Dark Mode) adaptatif aux couleurs AVVA39 --- */
                @media (prefers-color-scheme: dark) {
                    :root {
                        --pwa-bg-body: #121212;
                        --pwa-card-bg: #1e1e1e;
                        --pwa-overlay-bg: rgba(0, 0, 0, 0.75);

                        --pwa-bg-header: rgba(30, 30, 30, 0.90);
                        --pwa-bg-nav: rgba(30, 30, 30, 0.88);
                        --pwa-border-color: rgba(227, 6, 19, 0.3);
                        --pwa-shadow-nav: 0 4px 20px rgba(0, 0, 0, 0.5);

                        --pwa-text-main: #f0f0f0;
                        --pwa-text-inactive: #a0a0a0;
                        --pwa-text-active: #ff3b47;
                        /* Rouge légèrement plus vif pour lisibilité sur fond sombre */
                    }
                }

                /* ==========================================================================
               2. BOUTON FLOTTANT (FAB TRIGGER)
               ========================================================================== */
                .pwa-fab-trigger {
                    /* Positionnement */
                    position: fixed;
                    top: 24px;
                    right: 24px;
                    z-index: 9999999;

                    /* Dimensions & Layout */
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 48px;
                    height: 48px;
                    border-radius: 50%;

                    /* Style */
                    background-color: color-mix(in srgb, var(--pwa-card-bg) 60%, transparent);
                    color: var(--pwa-text-active);
                    border: 1px solid var(--pwa-border-color);
                    box-shadow: var(--pwa-shadow-nav);
                    cursor: pointer;

                    /* Effet Glassmorphism */
                    backdrop-filter: blur(8px);
                    -webkit-backdrop-filter: blur(8px);

                    /* Animations */
                    transition:
                        transform 0.2s ease,
                        opacity 0.3s ease,
                        background-color 0.25s ease,
                        border-color 0.25s ease;
                }

                .pwa-fab-trigger:hover {
                    background-color: color-mix(in srgb, var(--pwa-card-bg) 80%, transparent);
                }

                .pwa-fab-trigger:active {
                    transform: scale(0.90);
                }

                /* Option bouton discret */
                .pwa-fab-trigger.pwa-discret {
                    opacity: 0.3;
                }

                .pwa-fab-trigger.pwa-discret:hover {
                    opacity: 1;
                }

                /* Icône SVG */
                .pwa-fab-trigger svg {
                    width: 22px;
                    height: 22px;
                    fill: currentColor;
                    pointer-events: none;
                }

                #pwa-action-open {
                    /* Dimensions réduites du bouton */
                    width: 24px;
                    height: 24px;
                    padding: 2px;

                    /* Alignement parfait de l'icône SVG au centre */
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;

                    /* Style de base (adaptable selon vos besoins) */
                    border: none;
                    border-radius: 50%;
                    /* Rond */
                    cursor: pointer;
                }

                /* Ajustement de la taille de l'icône SVG */
                #pwa-action-open svg {
                    width: 14px;
                    height: 14px;
                    fill: currentColor;
                    /* Prend la couleur du texte */
                }

                /* ==========================================================================
               3. FENÊTRE MODALE & COMPOSANTS
               ========================================================================== */
                .pwa-modal-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: var(--pwa-overlay-bg);
                    backdrop-filter: blur(4px);
                    -webkit-backdrop-filter: blur(4px);
                    z-index: 1000;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    opacity: 0;
                    pointer-events: none;
                    transition: opacity 0.3s ease;
                    padding: 16px;
                    box-sizing: border-box;
                }

                .pwa-modal-overlay.is-open {
                    opacity: 1;
                    pointer-events: auto;
                }

                .pwa-modal-container {
                    width: 100%;
                    max-width: 400px;
                    transform: translateY(20px);
                    transition: transform 0.3s ease;
                }

                .pwa-modal-overlay.is-open .pwa-modal-container {
                    transform: translateY(0);
                }

                .pwa-card {
                    background: var(--pwa-card-bg);
                    border: 1px solid var(--pwa-border-color);
                    border-radius: 28px;
                    padding: 24px;
                    text-align: center;
                    box-shadow: var(--pwa-shadow-nav);
                    position: relative;
                }

                .pwa-btn-close {
                    position: absolute;
                    top: 16px;
                    right: 16px;
                    background: transparent;
                    border: none;
                    color: var(--pwa-text-inactive);
                    font-size: 20px;
                    cursor: pointer;
                    padding: 4px;
                    line-height: 1;
                    transition: color 0.2s ease;
                }

                .pwa-btn-close:hover {
                    color: var(--pwa-text-main);
                }

                .pwa-logo {
                    width: 64px;
                    height: 64px;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    margin-bottom: 16px;
                }

                .pwa-logo img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    border-radius: 16px;
                    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
                }

                /* TITRE DYNAMIQUE AVVA39 (ANIMATION DÉGRADÉ ROUGE / JAUNE / ROUGE) */
                .pwa-rainbow-title {
                    margin: 0 0 8px 0;
                    font-size: 20px;
                    font-weight: 700;
                    background: linear-gradient(90deg,
                            var(--avva-red) 0%,
                            var(--avva-yellow) 50%,
                            var(--avva-red) 100%);
                    background-size: 200% auto;
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    animation: avva-gradient-flow 6s linear infinite;
                }

                @keyframes avva-gradient-flow {
                    0% {
                        background-position: 0% 50%;
                    }

                    100% {
                        background-position: 200% 50%;
                    }
                }

                .pwa-badge-status {
                    font-size: 13px;
                    font-weight: 600;
                    color: var(--pwa-text-inactive);
                    background: var(--pwa-bg-header);
                    border: 1px solid var(--pwa-border-color);
                    padding: 6px 12px;
                    border-radius: 8px;
                    display: inline-block;
                    margin-bottom: 16px;
                }

                .pwa-list-instructions {
                    text-align: left;
                    padding-left: 20px;
                    color: var(--pwa-text-main);
                    margin-bottom: 20px;
                }

                .pwa-list-instructions li {
                    margin-bottom: 12px;
                    line-height: 1.5;
                    font-size: 14px;
                }

                /* Bouton d'installation automatique */
                .pwa-btn-install {
                    display: none;
                    width: 100%;
                    padding: 14px;
                    font-size: 15px;
                    border-radius: 14px;
                    background-color: var(--pwa-text-active);
                    color: #ffffff;
                    font-weight: 600;
                    border: none;
                    cursor: pointer;
                    transition: opacity 0.2s ease, transform 0.1s ease;
                    box-shadow: 0 3px 8px rgba(227, 6, 19, 0.25);
                }

                .pwa-btn-install:hover {
                    opacity: 0.9;
                }

                .pwa-btn-install:active {
                    transform: scale(0.98);
                }

                /* ==========================================================================
               4. AFFICHAGE ET MODES PWA INSTALLÉE
               ========================================================================== */
                .pwa-dashboard-view {
                    display: none !important;
                }

                @media (display-mode: standalone),
                (display-mode: fullscreen),
                (display-mode: minimal-ui) {
                    .pwa-dashboard-view {
                        display: block !important;
                    }

                    .pwa-browser-view,
                    .pwa-fab-trigger {
                        display: none !important;
                    }
                }
            </style>

            <button class="pwa-fab-trigger pwa-browser-view" id="pwa-action-open" title="Installer l'application">
                <svg viewBox="0 0 24 24">
                    <path d="M5 20h14v-2H5v2zm7-2l5-5h-4V4h-2v7H7l5 5z" />
                </svg>
            </button>

            <div class="pwa-modal-overlay pwa-browser-view" id="pwa-component-modal">
                <main class="pwa-modal-container">
                    <div class="pwa-card">
                        <button class="pwa-btn-close" id="pwa-action-close">✕</button>

                        <div class="pwa-logo"><img src="/assets/images/logo-avva39.png" alt="Logo"></div>

                        <h2 class="pwa-rainbow-title">Installer AVVA39</h2>

                        <p style="color: var(--pwa-text-inactive); margin: 0 0 16px 0; font-size: 14px;">
                            Ajoutez l'application à votre écran d'accueil pour profiter d'une expérience plein écran fluide.
                        </p>

                        <div class="pwa-badge-status" id="pwa-text-status">Analyse du système...</div>

                        <div id="pwa-guide-ios" style="display: none;">
                            <ol class="pwa-list-instructions">
                                <li>Ouvrez le menu de partage de Safari <span style="font-size: 16px;">⎋</span>.</li>
                                <li>Faites défiler et sélectionnez <strong>Sur l'écran d'accueil</strong>.</li>
                                <li>Validez en cliquant sur <strong>Ajouter</strong>.</li>
                            </ol>
                        </div>

                        <div id="pwa-guide-generic" style="display: none;">
                            <ol class="pwa-list-instructions" id="pwa-text-instructions">
                                <li>Ouvrez les options de votre navigateur <span style="font-weight: bold;">⋮</span>.</li>
                                <li>Choisissez l'option <strong>Installer l'application</strong> ou <i>Ajouter à l'écran
                                        d'accueil</i>.</li>
                            </ol>

                            <button id="pwa-action-install" class="pwa-btn-install">Installer maintenant</button>
                        </div>
                    </div>
                </main>
            </div>
        <?php else: ?>
            <style>
                /* ==========================================================================
               1. VARIABLES DE THÈME (CHARTE CARTOGUIDE & MATERIAL YOU)
               ========================================================================== */
                :root {
                    /* --- Palette officielle Cartoguide (Thème Cartographie & Nature) --- */
                    --cartoguide-emerald: #10b981;
                    /* Vert Émeraude principal */
                    --cartoguide-mint: #34d399;
                    /* Vert Menthe d'accentuation */
                    --cartoguide-dark: #0f172a;
                    /* Slate Sombre */
                    --cartoguide-light: #f8fafc;
                    /* Fond clair */

                    /* --- Arrière-plans principaux --- */
                    --cartoguide-pwa-bg-body: var(--cartoguide-light);
                    --cartoguide-pwa-card-bg: #ffffff;
                    --cartoguide-pwa-overlay-bg: rgba(15, 23, 42, 0.75);

                    /* --- Composants de navigation & Header --- */
                    --cartoguide-pwa-bg-header: rgba(255, 255, 255, 0.92);
                    --cartoguide-pwa-bg-nav: rgba(248, 250, 252, 0.90);
                    --cartoguide-pwa-border-color: rgba(16, 185, 129, 0.2);
                    --cartoguide-pwa-shadow-nav: 0 4px 14px rgba(0, 0, 0, 0.12);

                    /* --- Typographie & Couleurs d'accentuation --- */
                    --cartoguide-pwa-text-main: var(--cartoguide-dark);
                    --cartoguide-pwa-text-inactive: #64748b;
                    --cartoguide-pwa-text-active: var(--cartoguide-emerald);
                }

                /* --- Mode Sombre (Dark Mode) adaptatif aux couleurs Cartoguide --- */
                @media (prefers-color-scheme: dark) {
                    :root {
                        --cartoguide-pwa-bg-body: #0b0f19;
                        --cartoguide-pwa-card-bg: #1e293b;
                        --cartoguide-pwa-overlay-bg: rgba(0, 0, 0, 0.82);

                        --cartoguide-pwa-bg-header: rgba(30, 41, 59, 0.90);
                        --cartoguide-pwa-bg-nav: rgba(30, 41, 59, 0.88);
                        --cartoguide-pwa-border-color: rgba(52, 211, 153, 0.3);
                        --cartoguide-pwa-shadow-nav: 0 4px 20px rgba(0, 0, 0, 0.5);

                        --cartoguide-pwa-text-main: #f8fafc;
                        --cartoguide-pwa-text-inactive: #94a3b8;
                        --cartoguide-pwa-text-active: var(--cartoguide-mint);
                    }
                }

                /* ==========================================================================
               2. BOUTON FLOTTANT (FAB TRIGGER CARTOGUIDE)
               ========================================================================== */
                .cartoguide-pwa-fab-trigger {
                    /* Positionnement */
                    position: fixed;
                    top: 24px;
                    right: 24px;
                    z-index: 9999999;

                    /* Dimensions & Layout */
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 48px;
                    height: 48px;
                    border-radius: 50%;

                    /* Style */
                    background-color: color-mix(in srgb, var(--cartoguide-pwa-card-bg) 60%, transparent);
                    color: var(--cartoguide-pwa-text-active);
                    border: 1px solid var(--cartoguide-pwa-border-color);
                    box-shadow: var(--cartoguide-pwa-shadow-nav);
                    cursor: pointer;

                    /* Effet Glassmorphism */
                    backdrop-filter: blur(8px);
                    -webkit-backdrop-filter: blur(8px);

                    /* Animations */
                    transition:
                        transform 0.2s ease,
                        opacity 0.3s ease,
                        background-color 0.25s ease,
                        border-color 0.25s ease;
                }

                .cartoguide-pwa-fab-trigger:hover {
                    background-color: color-mix(in srgb, var(--cartoguide-pwa-card-bg) 80%, transparent);
                }

                .cartoguide-pwa-fab-trigger:active {
                    transform: scale(0.90);
                }

                /* Option bouton discret lors du scroll */
                .cartoguide-pwa-fab-trigger.cartoguide-pwa-discret {
                    opacity: 0.3;
                }

                .cartoguide-pwa-fab-trigger.cartoguide-pwa-discret:hover {
                    opacity: 1;
                }

                /* Ajustement de l'icône dans le bouton */
                #cartoguide-pwa-action-open {
                    width: 36px;
                    height: 36px;
                    padding: 4px;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    border: 1px solid var(--cartoguide-pwa-border-color);
                    border-radius: 50%;
                    cursor: pointer;
                }

                #cartoguide-pwa-action-open svg {
                    width: 18px;
                    height: 18px;
                    fill: currentColor;
                }

                /* ==========================================================================
               3. FENÊTRE MODALE & COMPOSANTS CARTOGUIDE
               ========================================================================== */
                .cartoguide-pwa-modal-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: var(--cartoguide-pwa-overlay-bg);
                    backdrop-filter: blur(4px);
                    -webkit-backdrop-filter: blur(4px);
                    z-index: 1000;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    opacity: 0;
                    pointer-events: none;
                    transition: opacity 0.3s ease;
                    padding: 16px;
                    box-sizing: border-box;
                }

                .cartoguide-pwa-modal-overlay.cartoguide-is-open {
                    opacity: 1;
                    pointer-events: auto;
                }

                .cartoguide-pwa-modal-container {
                    width: 100%;
                    max-width: 400px;
                    transform: translateY(20px);
                    transition: transform 0.3s ease;
                }

                .cartoguide-pwa-modal-overlay.cartoguide-is-open .cartoguide-pwa-modal-container {
                    transform: translateY(0);
                }

                .cartoguide-pwa-card {
                    background: var(--cartoguide-pwa-card-bg);
                    border: 1px solid var(--cartoguide-pwa-border-color);
                    border-radius: 28px;
                    padding: 24px;
                    text-align: center;
                    box-shadow: var(--cartoguide-pwa-shadow-nav);
                    position: relative;
                }

                .cartoguide-pwa-btn-close {
                    position: absolute;
                    top: 16px;
                    right: 16px;
                    background: transparent;
                    border: none;
                    color: var(--cartoguide-pwa-text-inactive);
                    font-size: 20px;
                    cursor: pointer;
                    padding: 4px;
                    line-height: 1;
                    transition: color 0.2s ease;
                }

                .cartoguide-pwa-btn-close:hover {
                    color: var(--cartoguide-pwa-text-main);
                }

                .cartoguide-pwa-logo {
                    width: 64px;
                    height: 64px;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    margin-bottom: 16px;
                }

                .cartoguide-pwa-logo img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    border-radius: 16px;
                    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
                }

                /* TITRE DYNAMIQUE CARTOGUIDE (ANIMATION DÉGRADÉ ÉMERAUDE / MENTHE) */
                .cartoguide-pwa-rainbow-title {
                    margin: 0 0 8px 0;
                    font-size: 20px;
                    font-weight: 700;
                    background: linear-gradient(90deg,
                            var(--cartoguide-emerald) 0%,
                            var(--cartoguide-mint) 50%,
                            var(--cartoguide-emerald) 100%);
                    background-size: 200% auto;
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    animation: cartoguide-gradient-flow 6s linear infinite;
                }

                @keyframes cartoguide-gradient-flow {
                    0% {
                        background-position: 0% 50%;
                    }

                    100% {
                        background-position: 200% 50%;
                    }
                }

                .cartoguide-pwa-badge-status {
                    font-size: 13px;
                    font-weight: 600;
                    color: var(--cartoguide-pwa-text-inactive);
                    background: var(--cartoguide-pwa-bg-header);
                    border: 1px solid var(--cartoguide-pwa-border-color);
                    padding: 6px 12px;
                    border-radius: 8px;
                    display: inline-block;
                    margin-bottom: 16px;
                }

                .cartoguide-pwa-list-instructions {
                    text-align: left;
                    padding-left: 20px;
                    color: var(--cartoguide-pwa-text-main);
                    margin-bottom: 20px;
                }

                .cartoguide-pwa-list-instructions li {
                    margin-bottom: 12px;
                    line-height: 1.5;
                    font-size: 14px;
                }

                /* Bouton d'installation automatique */
                .cartoguide-pwa-btn-install {
                    display: none;
                    width: 100%;
                    padding: 14px;
                    font-size: 15px;
                    border-radius: 14px;
                    background-color: var(--cartoguide-emerald);
                    color: #ffffff;
                    font-weight: 600;
                    border: none;
                    cursor: pointer;
                    transition: opacity 0.2s ease, transform 0.1s ease;
                    box-shadow: 0 3px 8px rgba(16, 185, 129, 0.25);
                }

                .cartoguide-pwa-btn-install:hover {
                    background-color: #059669;
                }

                .cartoguide-pwa-btn-install:active {
                    transform: scale(0.98);
                }

                /* ==========================================================================
               4. AFFICHAGE ET MODES PWA INSTALLÉE
               ========================================================================== */
                .cartoguide-pwa-dashboard-view {
                    display: none !important;
                }

                @media (display-mode: standalone),
                (display-mode: fullscreen),
                (display-mode: minimal-ui) {
                    .cartoguide-pwa-dashboard-view {
                        display: block !important;
                    }

                    .cartoguide-pwa-browser-view,
                    .cartoguide-pwa-fab-trigger {
                        display: none !important;
                    }
                }
            </style>
            <button class="cartoguide-pwa-fab-trigger cartoguide-pwa-browser-view" id="cartoguide-pwa-action-open"
                title="Installer le Cartoguide">
                <svg viewBox="0 0 24 24">
                    <path d="M12 2L4.5 20.29l.71.71L12 18l6.79 3 .71-.71z" />
                </svg>
            </button>

            <div class="cartoguide-pwa-modal-overlay cartoguide-pwa-browser-view" id="cartoguide-pwa-component-modal">
                <main class="cartoguide-pwa-modal-container">
                    <div class="cartoguide-pwa-card">
                        <button class="cartoguide-pwa-btn-close" id="cartoguide-pwa-action-close">✕</button>

                        <div class="cartoguide-pwa-logo">
                            <img src="/assets/images/logo-avva39.png" alt="Logo Cartoguide AVVA39">
                        </div>

                        <h2 class="cartoguide-pwa-rainbow-title">Cartoguide AVVA39</h2>

                        <p style="color: var(--cartoguide-pwa-text-inactive); margin: 0 0 16px 0; font-size: 14px;">
                            Ajoutez la carte interactive sur votre écran d'accueil pour suivre les parcours et dénivelés
                            <strong>hors-ligne</strong>.
                        </p>

                        <div class="cartoguide-pwa-badge-status" id="cartoguide-pwa-text-status">Analyse de la cartographie...
                        </div>

                        <div id="cartoguide-pwa-guide-ios" style="display: none;">
                            <ol class="cartoguide-pwa-list-instructions">
                                <li>Ouvrez le menu de partage de Safari <span style="font-size: 16px;">⎋</span>.</li>
                                <li>Faites défiler et sélectionnez <strong>Sur l'écran d'accueil</strong>.</li>
                                <li>Validez en cliquant sur <strong>Ajouter</strong>.</li>
                            </ol>
                        </div>

                        <div id="cartoguide-pwa-guide-generic" style="display: none;">
                            <ol class="cartoguide-pwa-list-instructions" id="cartoguide-pwa-text-instructions">
                                <li>Ouvrez les options de votre navigateur <span style="font-weight: bold;">⋮</span>.</li>
                                <li>Choisissez l'option <strong>Installer l'application</strong> ou <i>Ajouter à l'écran
                                        d'accueil</i>.</li>
                            </ol>

                            <button id="cartoguide-pwa-action-install" class="cartoguide-pwa-btn-install">Installer le
                                Cartoguide</button>
                        </div>
                    </div>
                </main>
            </div>
        <?php endif; ?>
        <div id="main-content-bg">
            <?php if (!$isCartoguide): ?>
            <header class="avva-header">
                <?php if ($_SERVER['REQUEST_URI'] !== '/'): ?>
                    <a href="/" class="avva-back-arrow" title="Retour">
                        <i class="fa-solid fa-arrow-left-long"></i>
                    </a>
                <?php endif; ?>
                <?php if ($_SERVER['REQUEST_URI'] !== '/'): ?>
                    <a href="/" class="avva-back-arrow-2" title="Retour">
                        <i class="fa-solid fa-arrow-right-long"></i>
                    </a>
                <?php endif; ?>
                <div class="avva-top-bar">
                    <div class="container d-flex justify-content-between align-items-center flex-column flex-md-row">

                        <div class="d-flex flex-column align-items-center">
                            <div class="d-flex align-items-center">

                                <a class="nav-link" style="color: yellow; font-size: 20px;"
                                    href="/page-a-propos/<?= $pageAPropos->getId() ?>">À
                                    Propos</a>

                                <a class="nav-link" style="color: yellow; font-size: 20px;"
                                    href="/page-status/<?= $pageStatus->getId() ?>">
                                    Status
                                </a>

                            </div>
                            <div>
                                <span class="info-item weather-info ms-3">
                                    <span id="weather-temp" style="color: yellow; font-size: 25px;">Météo</span><i
                                        class="fas fa-cloud-sun ms-1"
                                        style="color: yellow; font-size: 25px; cursor: pointer;"></i>
                                </span>
                            </div>
                        </div>
                        <div>
                            <div class="date-time d-flex flex-column align-items-center">
                                <h1 id="current-time"
                                    style="color: yellow; font-size: 45px; font-weight: bold; margin-top: 10px"></h1>
                                <span id="current-date" style="color:yellow; font-size: 20px;"></span>
                            </div>
                        </div>
                        <div style="position: relative;">
                            <form class="d-flex" role="search" id="searchForm">
                                <button class="btn search-btn" style="font-size: 25px;" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                                <input class="form-control search-input" type="search" placeholder="Recherche..."
                                    aria-label="Recherche" id="searchInput" autocomplete="off">
                            </form>
                            <div id="searchResults" class="search-results"></div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- Modal météo stylé et responsive -->
            <style>
                /* ==========================================
       VARIABLES DE COULEURS OFFICIELLES AVVA 39
       ========================================== */
                :root {
                    --avva-navy: #002B49;
                    /* Bleu marine profond de l'écusson */
                    --avva-sky: #134074;
                    /* Bleu aéronautique intermédiaire */
                    --avva-accent: #FFC72C;
                    /* Jaune vif / Or (Soleil & Planeur) */
                    --avva-light: #F4F6F9;
                    /* Fond clair très doux */
                    --avva-white: #FFFFFF;
                    /* Blanc pur */
                    --avva-border: #CBD5E1;
                    /* Bordures neutres */
                }

                /* ==========================================
       MODAL & DIMENSIONNEMENT
       ========================================== */
                #weatherModal .modal-content {
                    border: none;
                    border-radius: 1.25rem;
                    max-height: 90vh;
                    background-color: var(--avva-light);
                    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
                    overflow: hidden;
                }

                /* --- Responsive Mobile (< 576px) --- */
                @media (max-width: 575.98px) {
                    #weatherModal .modal-dialog-centered {
                        margin: 0;
                    }

                    #weatherModal .modal-content {
                        width: 100vw;
                        height: 100vh;
                        max-height: 100vh;
                        border-radius: 0;
                    }
                }

                /* --- Responsive Ordinateur (>= 992px) --- */
                @media (min-width: 992px) {
                    #weatherModal .modal-dialog {
                        max-width: 900px;
                    }

                    #weatherModal .modal-content {
                        width: 900px;
                    }
                }

                /* ==========================================
       SLIDES & CAROUSEL
       ========================================== */
                #weatherCarousel .carousel-item {
                    height: 60vh;
                    max-height: 600px;
                    min-height: 400px;
                    overflow-y: auto;
                    padding-right: 4px;
                }

                #slide-map {
                    height: 100%;
                }

                /* ==========================================
       CARTE MÉTÉO ACTUELLE
       ========================================== */
                .current-weather-card {
                    background: linear-gradient(135deg, var(--avva-navy) 0%, var(--avva-sky) 100%);
                    border: 1px solid rgba(255, 255, 255, 0.15);
                    border-radius: 1rem;
                    color: var(--avva-white);
                    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                }

                .current-weather-card #modal-icon {
                    color: var(--avva-accent);
                    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
                }

                /* ==========================================
       BOUTONS & INTERACTION (CHARTE AVVA39)
       ========================================== */

                /* Style global des boutons */
                #weatherModal .modal-content .btn {
                    border-radius: 50rem;
                    font-weight: 500;
                    transition: all 0.2s ease-in-out;
                }

                /* Boutons Outlines (ex: Voir la carte) */
                #weatherModal .modal-content .btn-outline-primary {
                    color: var(--avva-navy);
                    border-color: var(--avva-navy);
                }

                #weatherModal .modal-content .btn-outline-primary:hover,
                #weatherModal .modal-content .btn-outline-primary:focus {
                    background-color: var(--avva-navy);
                    border-color: var(--avva-navy);
                    color: var(--avva-white);
                }

                /* Boutons Pleins / Accentuation (ex: Action principale) */
                #weatherModal .modal-content .btn-primary {
                    background-color: var(--avva-navy);
                    border-color: var(--avva-navy);
                    color: var(--avva-white);
                }

                #weatherModal .modal-content .btn-primary:hover {
                    background-color: var(--avva-sky);
                    border-color: var(--avva-sky);
                    color: var(--avva-white);
                }

                /* Boutons Secondaires (ex: Fermer) */
                #weatherModal .modal-content .btn-secondary,
                #weatherModal .modal-content .btn-light {
                    background-color: rgba(0, 43, 73, 0.08);
                    border: none;
                    color: var(--avva-navy);
                }

                #weatherModal .modal-content .btn-secondary:hover,
                #weatherModal .modal-content .btn-light:hover {
                    background-color: var(--avva-navy);
                    color: var(--avva-white);
                }

                /* Croix de fermeture */
                #weatherModal .modal-content .btn-close {
                    opacity: 0.7;
                    transition: opacity 0.2s ease-in-out;
                }

                #weatherModal .modal-content .btn-close:hover {
                    opacity: 1;
                }

                /* ==========================================
       PRÉVISIONS & LAYOUTS
       ========================================== */

                /* Scroll horizontal (24h) */
                .forecast-scroll-container {
                    display: flex;
                    gap: 0.75rem;
                    overflow-x: auto;
                    scroll-snap-type: x mandatory;
                    -webkit-overflow-scrolling: touch;
                    scrollbar-width: thin;
                    padding-bottom: 8px;
                }

                .forecast-scroll-container>* {
                    scroll-snap-align: start;
                    flex: 0 0 auto;
                }

                /* Grid (5 jours) */
                .forecast-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
                    gap: 0.75rem;
                }

                /* ==========================================
       RECHERCHE & CARTE LEAFLET/MAP
       ========================================== */
                #search-results {
                    z-index: 1055;
                    max-height: 200px;
                    overflow-y: auto;
                    top: 100%;
                    margin-top: 4px;
                    border-radius: 0.5rem;
                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                }

                .map-wrapper {
                    position: relative;
                    min-height: 300px;
                    border-radius: 0.75rem;
                    border: 1px solid var(--avva-border);
                    overflow: hidden;
                    z-index: 1;
                }
            </style>
            <div class="modal fade" id="weatherModal" tabindex="-1" aria-labelledby="weatherModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
                    <div class="modal-content border-0 shadow-lg">

                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fs-5 fw-bold" id="weatherModalLabel">
                                <i class="fas fa-location-dot me-2 text-primary"></i>Prévisions météo pour Pagnoz
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>

                        <div class="modal-body p-3 p-md-4">
                            <div id="weatherCarousel" class="carousel slide h-100" data-bs-interval="false"
                                data-bs-touch="true">
                                <div class="carousel-inner h-100">

                                    <div class="carousel-item active" id="slide-weather">
                                        <div class="weather-container">

                                            <div
                                                class="current-weather-card p-3 p-md-4 mb-4 rounded-4 bg-primary text-white shadow-sm">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <i id="modal-icon" class="fas fa-sun display-4"></i>
                                                        <div>
                                                            <h2 id="modal-temp" class="display-5 fw-bold mb-0">--°C</h2>
                                                            <p id="modal-description"
                                                                class="mb-0 text-capitalize opacity-75"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="forecast-section mb-4">
                                                <h6 class="fw-bold mb-3">Prévisions prochaines 24h</h6>
                                                <div id="forecast-24h" class="forecast-scroll-container d-flex gap-2 pb-2">
                                                </div>
                                            </div>

                                            <div class="forecast-section">
                                                <h6 class="fw-bold mb-3">Prévisions sur 5 jours</h6>
                                                <div id="forecast-5days" class="forecast-grid gap-2">
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="carousel-item" id="slide-map">
                                        <div class="map-slide-container d-flex flex-column h-100">

                                            <div class="search-box position-relative mb-3">
                                                <div class="input-group">
                                                    <span class="input-group-text bg-white border-end-0">
                                                        <i class="fas fa-search text-muted"></i>
                                                    </span>
                                                    <input type="text" id="commune-search"
                                                        class="form-control border-start-0 ps-0"
                                                        placeholder="Rechercher une commune..." autocomplete="off">
                                                </div>
                                                <div id="search-results"
                                                    class="list-group position-absolute w-100 shadow rounded-3 d-none">
                                                </div>
                                            </div>

                                            <div class="map-wrapper flex-grow-1 rounded-4 overflow-hidden border">
                                                <div id="france-map" class="h-100 w-100"></div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="modal-footer border-0 pt-0 d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-primary rounded-pill px-3" id="btn-toggle-map">
                                <i class="fas fa-map-marked-alt me-2"></i><span>Voir la carte</span>
                            </button>
                            <button type="button" class="btn btn-light rounded-pill px-4"
                                data-bs-dismiss="modal">Fermer</button>
                        </div>

                    </div>
                </div>
            </div>
            <?php else: ?>
                <style>
  :root {
    --avva-yellow: #ffe600;
    --avva-green: #10b981;
    --avva-dark: #0f172a;
    --glass-bg: rgba(15, 23, 42, 0.85);
    --glass-border: rgba(255, 230, 0, 0.35);
  }

  /* BARRE FIXÉE */
  .cartoguide-floating-bar {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--glass-bg);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--glass-border);
    border-radius: 50px;
    padding: 0.6rem 1.4rem;
    width: 92%;
    max-width: 520px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5),
                0 0 15px rgba(255, 230, 0, 0.2);
    overflow: hidden;
  }

  /* Halo lumineux */
  .cartoguide-floating-bar::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,236,0,0.1) 0%, rgba(16,185,129,0.05) 50%, transparent 70%);
    animation: rotateGlow 12s linear infinite;
    pointer-events: none;
  }

  /* BOUTON FLÈCHE */
  .btn-back {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(255, 230, 0, 0.15), rgba(16, 185, 129, 0.2));
    border: 1px solid var(--avva-yellow);
    color: var(--avva-yellow);
    text-decoration: none;
    backdrop-filter: blur(8px);
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    z-index: 2;
    flex-shrink: 0;
  }

  .btn-back .arrow-icon {
    font-size: 1.1rem;
    font-weight: bold;
    line-height: 1;
    transition: transform 0.2s ease;
  }

  .btn-back:hover {
    background: linear-gradient(135deg, var(--avva-yellow), var(--avva-green));
    color: var(--avva-dark);
    border-color: transparent;
    box-shadow: 0 0 15px rgba(255, 230, 0, 0.5);
    transform: scale(1.08);
  }

  .btn-back:hover .arrow-icon {
    transform: translateX(-2px);
  }

  /* HEURE ET DATE */
  .center-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 2;
  }

  .time-display {
    font-family: 'Courier New', Courier, monospace, sans-serif;
    color: var(--avva-yellow);
    font-size: 1.5rem;
    font-weight: 800;
    letter-spacing: 1px;
    text-shadow: 0 0 10px rgba(255, 230, 0, 0.4);
    line-height: 1;
    margin: 0;
  }

  .time-colon {
    animation: pulseColon 1s infinite;
  }

  .date-display {
    color: #e2e8f0;
    font-size: 0.75rem;
    font-weight: 500;
    letter-spacing: 0.5px;
    margin-top: 3px;
  }

  /* MODULE MÉTÉO A DROITE */
  .weather-info {
    display: flex;
    align-items: center;
    gap: 6px;
    background: rgba(255, 255, 255, 0.05);
    padding: 4px 12px;
    border-radius: 20px;
    border: 1px solid rgba(255, 230, 0, 0.2);
    z-index: 2;
    flex-shrink: 0;
  }

  #weather-temp {
    color: var(--avva-yellow);
    font-size: 0.95rem;
    font-weight: 700;
  }

  .weather-icon {
    color: var(--avva-yellow);
    font-size: 1.1rem;
    cursor: pointer;
    transition: transform 0.3s ease;
  }

  .weather-info:hover .weather-icon {
    transform: scale(1.2) rotate(10deg);
  }

  @keyframes pulseColon {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.2; }
  }

  @keyframes rotateGlow {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
</style>

<div class="cartoguide-floating-bar">
    <a href="https://avva39.fr" class="btn-back" title="Retour au site">
        <span class="arrow-icon">←</span>
    </a>

    <div class="center-content">
        <h1 id="current-time-cartoguide" class="time-display">
            00<span class="time-colon">:</span>00<span class="time-colon">:</span>00
        </h1>
        <span id="current-date" class="date-display">-- -------- ----</span>
    </div>

    <div class="cartoguide-weather-wrapper">
    <div class="info-item weather-info-cartoguide d-flex align-items-center gap-2 px-3 py-1 rounded-pill" 
         id="weather-btn-cartoguide" 
         role="button" 
         tabindex="0" 
         title="Ouvrir les prévisions météo du Cartoguide">
        
        <span id="weather-temp-cartoguide" class="fw-bold fs-6 text-warning">--°C</span>
        
        <i class="fas fa-cloud-sun weather-icon text-warning fs-5" aria-hidden="true"></i>
        
    </div>
</div>
<style>
    /* Style du conteneur Météo Cartoguide */
.weather-info-cartoguide {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 230, 0, 0.25);
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    backdrop-filter: blur(8px);
    user-select: none;
}

/* Effet au survol */
.weather-info-cartoguide:hover {
    background: rgba(255, 230, 0, 0.15);
    border-color: #ffe600;
    box-shadow: 0 0 12px rgba(255, 230, 0, 0.3);
    transform: translateY(-1px);
}

/* Animation de l'icône au survol */
.weather-info-cartoguide:hover .weather-icon {
    transform: scale(1.15) rotate(8deg);
    transition: transform 0.25s ease;
}
</style>
</div>
<div class="modal fade" id="weatherModalCartoguide" tabindex="-1" aria-labelledby="weatherModalLabelCartoguide" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow-lg cartoguide-modal-bg text-white rounded-4 overflow-hidden d-flex flex-column">

            <div class="modal-header border-bottom border-secondary border-opacity-25 pb-3 flex-shrink-0">
                <h5 class="modal-title fs-6 fs-md-5 fw-bold text-warning d-flex align-items-center text-truncate" id="weatherModalLabelCartoguide">
                    <i class="fas fa-location-dot me-2 text-warning flex-shrink-0"></i>
                    <span class="text-truncate">Prévisions météo pour Pagnoz</span>
                </h5>
                <button type="button" class="btn-close btn-close-white flex-shrink-0" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body p-3 p-md-4 flex-grow-1 overflow-hidden d-flex flex-column">
                <div id="weatherCarouselCartoguide" class="carousel slide flex-grow-1 d-flex flex-column" data-bs-interval="false" data-bs-touch="true" style="min-height: 0;">
                    <div class="carousel-inner flex-grow-1" style="min-height: 0;">

                        <div class="carousel-item active h-100" id="slide-weather-cartoguide">
                            <div class="weather-container h-100 overflow-y-auto overflow-x-hidden pe-1">

                                <div class="current-weather-card p-3 mb-3 rounded-4 text-dark shadow-sm" style="background: linear-gradient(135deg, #ffe600, #10b981);">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                        <div class="d-flex align-items-center gap-3 min-w-0">
                                            <i id="modal-icon-cartoguide" class="fas fa-sun fs-1 text-dark flex-shrink-0"></i>
                                            <div class="min-w-0">
                                                <h2 id="modal-temp-cartoguide" class="fs-2 fw-bold mb-0 text-dark">--°C</h2>
                                                <p id="modal-description-cartoguide" class="mb-0 small text-capitalize fw-bold text-dark opacity-75 text-truncate"></p>
                                            </div>
                                        </div>
                                        <span class="badge bg-dark text-warning small px-3 py-2 rounded-pill flex-shrink-0">Cartoguide AVVA39</span>
                                    </div>
                                </div>

                                <div class="search-box position-relative mb-3">
                                    <h6 class="fw-bold mb-2 text-warning"><i class="fas fa-magnifying-glass me-2"></i>Changer de commune</h6>
                                    <div class="input-group">
                                        <span class="input-group-text bg-dark border-secondary border-opacity-50 text-warning">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" id="commune-search-cartoguide"
                                            class="form-control bg-dark text-white border-secondary border-opacity-50 ps-2"
                                            style="min-height: 44px;"
                                            placeholder="Rechercher une commune..." autocomplete="off">
                                    </div>
                                    <div id="search-results-cartoguide"
                                        class="list-group position-absolute w-100 shadow rounded-3 d-none z-3" style="max-height: 200px; overflow-y: auto;">
                                    </div>
                                </div>

                                <div class="forecast-section mb-3">
                                    <h6 class="fw-bold mb-2 text-warning"><i class="fas fa-clock me-2"></i>Prévisions prochaines 24h</h6>
                                    <div class="forecast-carousel" data-carousel-name="24h">
                                        <button type="button" class="forecast-nav forecast-nav-prev" aria-label="Créneau précédent">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <div class="forecast-track-wrapper">
                                            <div id="forecast-24h-cartoguide" class="forecast-track"></div>
                                        </div>
                                        <button type="button" class="forecast-nav forecast-nav-next" aria-label="Créneau suivant">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                    <div class="forecast-dots" id="forecast-24h-dots"></div>
                                </div>

                                <div class="forecast-section mb-2">
                                    <h6 class="fw-bold mb-2 text-warning"><i class="fas fa-calendar-days me-2"></i>Prévisions sur 5 jours</h6>
                                    <div class="forecast-carousel" data-carousel-name="5days">
                                        <button type="button" class="forecast-nav forecast-nav-prev" aria-label="Jour précédent">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <div class="forecast-track-wrapper">
                                            <div id="forecast-5days-cartoguide" class="forecast-track"></div>
                                        </div>
                                        <button type="button" class="forecast-nav forecast-nav-next" aria-label="Jour suivant">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                    <div class="forecast-dots" id="forecast-5days-dots"></div>
                                </div>

                            </div>
                        </div>

                        <div class="carousel-item h-100" id="slide-map-cartoguide">
                            <div class="map-slide-container d-flex flex-column h-100">
                                <div class="map-wrapper flex-grow-1 rounded-4 overflow-hidden border border-secondary border-opacity-25" style="min-height: 0;">
                                    <div id="france-map-cartoguide" class="h-100 w-100"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="modal-footer border-top border-secondary border-opacity-25 pt-2 flex-shrink-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4 w-100 w-md-auto" style="min-height: 44px;" data-bs-dismiss="modal">Fermer</button>
            </div>

        </div>
    </div>
</div>

<style>
  .cartoguide-modal-bg {
    background: rgba(15, 23, 42, 0.95) !important;
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 230, 0, 0.25) !important;
  }

  #weatherModalCartoguide .modal-dialog,
  #weatherModalCartoguide .modal-content {
    max-height: 100vh;
  }

  .weather-container::-webkit-scrollbar {
    width: 4px;
  }
  .weather-container::-webkit-scrollbar-thumb {
    background: rgba(255, 230, 0, 0.3);
    border-radius: 10px;
  }

  #search-results-cartoguide .list-group-item {
    background-color: #0f172a;
    color: #ffffff;
    border-color: rgba(255, 255, 255, 0.1);
    min-height: 44px;
    display: flex;
    align-items: center;
  }
  #search-results-cartoguide .list-group-item:hover {
    background-color: #ffe600;
    color: #0f172a;
  }

  .weather-fade-out {
    opacity: 0;
    transform: translateY(-8px);
    transition: opacity 0.25s ease-out, transform 0.25s ease-out;
  }

  .weather-fade-in {
    opacity: 1;
    transform: translateY(0);
    transition: opacity 0.3s ease-in, transform 0.3s ease-in;
  }

  /* Carousel de prévisions (1 créneau/jour à la fois) */
  .forecast-carousel {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .forecast-track-wrapper {
    flex: 1 1 auto;
    overflow: hidden;
    min-width: 0;
  }

  .forecast-track {
    display: flex;
    transition: transform 0.3s ease;
  }

  .forecast-track > * {
    flex: 0 0 100%;
    max-width: 100%;
  }

  .forecast-nav {
    flex-shrink: 0;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 1px solid rgba(255, 230, 0, 0.4);
    background: rgba(15, 23, 42, 0.6);
    color: #ffe600;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    padding: 0;
  }

  .forecast-nav:disabled {
    opacity: 0.3;
    pointer-events: none;
  }

  .forecast-nav:not(:disabled):hover {
    background: rgba(255, 230, 0, 0.15);
  }

  .forecast-dots {
    display: flex;
    justify-content: center;
    gap: 6px;
    margin-top: 8px;
  }

  .forecast-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.25);
    border: none;
    padding: 0;
  }

  .forecast-dot.active {
    background: #ffe600;
    width: 18px;
    border-radius: 4px;
  }

  @media (max-width: 575.98px) {
    #weatherModalCartoguide .modal-content {
      height: 100vh;
      max-height: 100vh;
    }
    #weatherModalLabelCartoguide {
      font-size: 1rem;
    }
    .current-weather-card {
      padding: 0.75rem !important;
    }
    #modal-temp-cartoguide {
      font-size: 1.5rem;
    }
    #modal-icon-cartoguide {
      font-size: 1.75rem !important;
    }
    .search-box .form-control,
    .search-box .input-group-text {
      font-size: 0.9rem;
    }
    .modal-footer .btn {
      font-size: 0.95rem;
    }
    .map-wrapper {
      min-height: 200px !important;
    }
  }
</style>

<script>
  function initForecastCarousel(trackId, dotsId) {
    const track = document.getElementById(trackId);
    const dotsContainer = document.getElementById(dotsId);
    const carouselEl = track.closest('.forecast-carousel');
    const prevBtn = carouselEl.querySelector('.forecast-nav-prev');
    const nextBtn = carouselEl.querySelector('.forecast-nav-next');

    let currentIndex = 0;

    function render() {
      const items = track.children;
      const total = items.length;

      dotsContainer.innerHTML = '';
      for (let i = 0; i < total; i++) {
        const dot = document.createElement('button');
        dot.type = 'button';
        dot.className = 'forecast-dot' + (i === currentIndex ? ' active' : '');
        dot.setAttribute('aria-label', 'Aller à l\'élément ' + (i + 1));
        dot.addEventListener('click', () => {
          currentIndex = i;
          update();
        });
        dotsContainer.appendChild(dot);
      }

      update();
    }

    function update() {
      const total = track.children.length;
      track.style.transform = `translateX(-${currentIndex * 100}%)`;

      prevBtn.disabled = currentIndex === 0;
      nextBtn.disabled = currentIndex >= total - 1;

      Array.from(dotsContainer.children).forEach((dot, i) => {
        dot.classList.toggle('active', i === currentIndex);
      });
    }

    prevBtn.addEventListener('click', () => {
      if (currentIndex > 0) {
        currentIndex--;
        update();
      }
    });

    nextBtn.addEventListener('click', () => {
      if (currentIndex < track.children.length - 1) {
        currentIndex++;
        update();
      }
    });

    // Reconstruit dots/positions dès que le JS de fetch météo remplit le track
    const observer = new MutationObserver(() => {
      currentIndex = 0;
      render();
    });
    observer.observe(track, { childList: true });

    render();
  }

  document.addEventListener('DOMContentLoaded', () => {
    initForecastCarousel('forecast-24h-cartoguide', 'forecast-24h-dots');
    initForecastCarousel('forecast-5days-cartoguide', 'forecast-5days-dots');
  });
</script>

<script>
// 1. Horloge en temps réel
function updateClock() {
    const now = new Date();
    
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    
    const timeHTML = `${hours}<span class="time-colon">:</span>${minutes}<span class="time-colon">:</span>${seconds}`;
    document.getElementById('current-time-cartoguide').innerHTML = timeHTML;
    
    const options = { weekday: 'short', day: 'numeric', month: 'short' };
    let dateString = now.toLocaleDateString('fr-FR', options);
    dateString = dateString.charAt(0).toUpperCase() + dateString.slice(1);
    
    document.getElementById('current-date').textContent = dateString;
}

updateClock();
setInterval(updateClock, 1000);

// 2. Récupération Météo automatique (Coordonnées du Jura / Lons-le-Saunier : 46.675, 5.554)
// ============================================================
// CONFIGURATION CARTOGUIDE
// ============================================================
const CARTOGUIDE_API_KEY = '35435894e047a1125ad6ef5ff1425ed6';
const CARTOGUIDE_DEFAULT_CITY = 'Pagnoz';
const CARTOGUIDE_UNITS = 'metric';
const CARTOGUIDE_LANG = 'fr';
const CARTOGUIDE_REFRESH_INTERVAL_MS = 600000; // 10 minutes

// ============================================================
// ÉTAT GLOBAL CARTOGUIDE
// ============================================================
let cartoguideCurrentOWMIconCode = '';
let cartoguideCurrentLocationName = CARTOGUIDE_DEFAULT_CITY;
window.cartoguideForecastData = null;

let cartoguideFranceMap = null;
let cartoguideCurrentSelectionMarker = null;
let cartoguideCarouselInstance = null;

// ============================================================
// APPELS API MÉTÉO CARTOGUIDE
// ============================================================

/**
 * Récupère la météo par nom de ville et met à jour l'affichage Cartoguide.
 */
async function fetchCartoguideWeather(cityName = CARTOGUIDE_DEFAULT_CITY) {
    const currentWeatherUrl = `https://api.openweathermap.org/data/2.5/weather?q=${encodeURIComponent(cityName)}&units=${CARTOGUIDE_UNITS}&lang=${CARTOGUIDE_LANG}&appid=${CARTOGUIDE_API_KEY}`;
    const forecastUrl = `https://api.openweathermap.org/data/2.5/forecast?q=${encodeURIComponent(cityName)}&units=${CARTOGUIDE_UNITS}&lang=${CARTOGUIDE_LANG}&appid=${CARTOGUIDE_API_KEY}`;

    await runCartoguideWeatherFetch(currentWeatherUrl, forecastUrl);
}

/**
 * Récupère la météo par coordonnées GPS.
 */
async function fetchCartoguideWeatherByCoords(lat, lon, cityName = null) {
    const weatherUrl = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&units=${CARTOGUIDE_UNITS}&lang=${CARTOGUIDE_LANG}&appid=${CARTOGUIDE_API_KEY}`;
    const forecastUrl = `https://api.openweathermap.org/data/2.5/forecast?lat=${lat}&lon=${lon}&units=${CARTOGUIDE_UNITS}&lang=${CARTOGUIDE_LANG}&appid=${CARTOGUIDE_API_KEY}`;

    const result = await runCartoguideWeatherFetch(weatherUrl, forecastUrl, { skipRender: true });
    if (!result) return;

    const { currentData } = result;
    const displayName = cityName || currentData.name || 'Lieu sélectionné';
    const temp = Math.round(currentData.main.temp);

    // Positionnement du marqueur sur la carte Cartoguide
    if (cartoguideFranceMap) {
        if (cartoguideCurrentSelectionMarker) cartoguideFranceMap.removeLayer(cartoguideCurrentSelectionMarker);

        cartoguideCurrentSelectionMarker = L.marker([lat, lon]).addTo(cartoguideFranceMap)
            .bindPopup(`<b>${displayName}</b><br>${temp}°C - ${currentData.weather[0].description}`)
            .openPopup();

        cartoguideFranceMap.setView([lat, lon], 10);
    }

    cartoguideCurrentLocationName = displayName;
    renderCartoguideAll();

    // Bascule fluide vers l'onglet météo si on était sur la carte
    if (cartoguideCarouselInstance) {
        cartoguideCarouselInstance.to(0);
    }
}

/**
 * Effectue les deux requêtes API et met à jour l'état.
 */
async function runCartoguideWeatherFetch(weatherUrl, forecastUrl, { skipRender = false } = {}) {
    try {
        const [currentResp, forecastResp] = await Promise.all([
            fetch(weatherUrl),
            fetch(forecastUrl)
        ]);

        if (!currentResp.ok || !forecastResp.ok) throw new Error('Erreur de récupération météo Cartoguide');

        const currentData = await currentResp.json();
        const forecastData = await forecastResp.json();

        window.cartoguideForecastData = forecastData;
        cartoguideCurrentOWMIconCode = currentData.weather[0].icon;
        cartoguideCurrentLocationName = currentData.name || cartoguideCurrentLocationName;

        if (!skipRender) {
            renderCartoguideAll();
        }

        return { currentData, forecastData };
    } catch (err) {
        console.error('Erreur météo Cartoguide:', err);
        const tempEl = document.getElementById('weather-temp-cartoguide');
        if (tempEl) tempEl.textContent = 'Indisponible';
        return null;
    }
}

// ============================================================
// RECHERCHE DE COMMUNES (API GOUV)
// ============================================================
async function searchCartoguideCommunes(query) {
    const resultsContainer = document.getElementById('search-results-cartoguide');
    if (!resultsContainer) return;

    if (!query || query.length < 2) {
        resultsContainer.classList.add('d-none');
        return;
    }

    try {
        const response = await fetch(`https://geo.api.gouv.fr/communes?nom=${encodeURIComponent(query)}&fields=nom,centre,codeDepartement&limit=8`);
        const communes = await response.json();

        resultsContainer.innerHTML = '';
        if (communes.length === 0) {
            resultsContainer.classList.add('d-none');
            return;
        }

        communes.forEach(c => {
            if (c.centre && c.centre.coordinates) {
                const lon = c.centre.coordinates[0];
                const lat = c.centre.coordinates[1];

                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'list-group-item list-group-item-action text-start';
                item.innerHTML = `<strong>${c.nom}</strong> <small class="text-muted">(${c.codeDepartement})</small>`;

                item.addEventListener('click', () => {
                    const searchInput = document.getElementById('commune-search-cartoguide');
                    if (searchInput) searchInput.value = c.nom;
                    resultsContainer.classList.add('d-none');
                    fetchCartoguideWeatherByCoords(lat, lon, c.nom);
                });

                resultsContainer.appendChild(item);
            }
        });

        resultsContainer.classList.remove('d-none');
    } catch (err) {
        console.error('Erreur API Geo Cartoguide:', err);
    }
}

// ============================================================
// RENDU CARTOGUIDE (AVEC ANIMATION)
// ============================================================

/**
 * Rafraîchit l'ensemble des éléments météo du Cartoguide.
 */
function renderCartoguideAll() {
    renderCartoguideHeader();

    if (isCartoguideWeatherModalOpen()) {
        renderCartoguideModalContent();
    }
}

/**
 * Met à jour l'en-tête et la barre fixe.
 */
function renderCartoguideHeader() {
    if (!window.cartoguideForecastData) return;

    const temp = Math.round(window.cartoguideForecastData.list[0].main.temp);
    const desc = window.cartoguideForecastData.list[0].weather[0].description;

    const modalTitle = document.getElementById('weatherModalLabelCartoguide');
    if (modalTitle) modalTitle.textContent = `Prévisions météo pour ${cartoguideCurrentLocationName}`;

    const weatherTempElement = document.getElementById('weather-temp-cartoguide');
    if (weatherTempElement) {
        weatherTempElement.textContent = `${temp}°C`;
    }

    const weatherIconElement = document.querySelector('.weather-info-cartoguide .weather-icon, .weather-info-cartoguide .fas');
    if (weatherIconElement) {
        updateCartoguideWeatherIcon(cartoguideCurrentOWMIconCode, weatherIconElement);
        weatherIconElement.title = `${cartoguideCurrentLocationName} : ${desc.charAt(0).toUpperCase() + desc.slice(1)}`;
    }
}

/**
 * Vérifie si le modal météo Cartoguide est ouvert.
 */
function isCartoguideWeatherModalOpen() {
    const modalEl = document.getElementById('weatherModalCartoguide');
    if (!modalEl) return false;
    return modalEl.classList.contains('show');
}

/**
 * Remplit le contenu du modal avec animation de transition.
 */
function renderCartoguideModalContent() {
    if (!window.cartoguideForecastData) return;

    const weatherContainer = document.querySelector('#slide-weather-cartoguide .weather-container');

    // Déclenchement de l'animation de sortie si le conteneur existe
    if (weatherContainer) {
        weatherContainer.classList.remove('weather-fade-in');
        weatherContainer.classList.add('weather-fade-out');

        setTimeout(() => {
            updateModalDOM();
            weatherContainer.classList.remove('weather-fade-out');
            weatherContainer.classList.add('weather-fade-in');
        }, 200);
    } else {
        updateModalDOM();
    }
}

/**
 * Met à jour les éléments du DOM dans le modal.
 */
function updateModalDOM() {
    const currentItem = window.cartoguideForecastData.list[0];
    const currentTemp = Math.round(currentItem.main.temp);
    const currentDescription = currentItem.weather[0].description;

    const modalTempEl = document.getElementById('modal-temp-cartoguide');
    if (modalTempEl) modalTempEl.textContent = `${currentTemp}°C`;

    const modalDescEl = document.getElementById('modal-description-cartoguide');
    if (modalDescEl) modalDescEl.textContent = currentDescription;

    const modalIconElement = document.getElementById('modal-icon-cartoguide');
    if (modalIconElement) {
        updateCartoguideWeatherIcon(cartoguideCurrentOWMIconCode, modalIconElement);
    }

    renderCartoguideForecastList('forecast-24h-cartoguide', window.cartoguideForecastData.list.slice(0, 8), { showHour: true });
    renderCartoguideForecastList('forecast-5days-cartoguide', dedupeCartoguideByDay(window.cartoguideForecastData.list).slice(0, 5), { showHour: false });
}

/**
 * Regroupe les prévisions par jour.
 */
function dedupeCartoguideByDay(list) {
    const daysMap = {};
    list.forEach(f => {
        const date = new Date(f.dt * 1000);
        const dayStr = date.toLocaleDateString('fr-FR', { weekday: 'short', day: 'numeric' });
        if (!daysMap[dayStr]) daysMap[dayStr] = f;
    });
    return Object.values(daysMap);
}

/**
 * Génère le HTML des prévisions.
 */
function renderCartoguideForecastList(containerId, items, { showHour }) {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.innerHTML = items.map(f => {
        const dt = new Date(f.dt * 1000);
        const temp = Math.round(f.main.temp);
        const iconCode = f.weather[0].icon;
        const faIconClass = getCartoguideIconClassFor(iconCode);
        const desc = f.weather[0].description;
        const label = showHour
            ? `${dt.getHours()}h`
            : dt.toLocaleDateString('fr-FR', { weekday: 'short', day: 'numeric' });
        const shortDesc = desc.length > 12 ? desc.substring(0, 12) + '...' : desc;

        return `
            <div style="text-align: center; min-width: ${showHour ? '65px' : '75px'};">
                <small>${label}</small><br>
                <i class="fas ${faIconClass} fa-2x my-1" style="color: #ffe600;"></i><br>
                <small class="fw-bold">${temp}°C</small><br>
                <small title="${desc}">${shortDesc}</small>
            </div>
        `;
    }).join('');
}

// ============================================================
// ICÔNES FONT AWESOME CARTOGUIDE
// ============================================================

function getCartoguideIconClassFor(iconCode) {
    if (iconCode.startsWith('01')) return iconCode.endsWith('d') ? 'fa-sun' : 'fa-moon';
    if (iconCode.startsWith('02') || iconCode.startsWith('03')) return 'fa-cloud-sun';
    if (iconCode.startsWith('04')) return 'fa-cloud';
    if (iconCode.startsWith('09') || iconCode.startsWith('10')) return 'fa-cloud-showers-heavy';
    if (iconCode.startsWith('11')) return 'fa-bolt';
    if (iconCode.startsWith('13')) return 'fa-snowflake';
    if (iconCode.startsWith('50')) return 'fa-smog';
    return 'fa-cloud-sun';
}

function updateCartoguideWeatherIcon(iconCode, iconElement) {
    const faIconClass = getCartoguideIconClassFor(iconCode);

    if (iconElement) {
        const currentClasses = iconElement.className.split(' ').filter(c => !c.startsWith('fa-'));
        iconElement.className = currentClasses.join(' ') + ` ${faIconClass}`;
    }

    return faIconClass;
}

// ============================================================
// CARTE LEAFLET CARTOGUIDE
// ============================================================
function initCartoguideMap() {
    if (cartoguideFranceMap) return;

    cartoguideFranceMap = L.map('france-map-cartoguide').setView([46.603354, 1.888334], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(cartoguideFranceMap);

    cartoguideFranceMap.on('click', (e) => {
        fetchCartoguideWeatherByCoords(e.latlng.lat, e.latlng.lng);
    });
}

// ============================================================
// ÉVÉNEMENTS / INITIALISATION CARTOGUIDE
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
    const carouselEl = document.getElementById('weatherCarouselCartoguide');
    if (carouselEl) {
        cartoguideCarouselInstance = new bootstrap.Carousel(carouselEl, {
            interval: false,
            touch: false
        });

        carouselEl.addEventListener('slid.bs.carousel', (e) => {
            const btnToggle = document.getElementById('btn-toggle-map-cartoguide');
            if (e.to === 1) {
                if (btnToggle) btnToggle.innerHTML = '<i class="fas fa-arrow-left me-1"></i> Retour à la météo';
                initCartoguideMap();
                if (cartoguideFranceMap) cartoguideFranceMap.invalidateSize();
            } else {
                if (btnToggle) btnToggle.innerHTML = '<i class="fas fa-map-marked-alt me-1"></i> Voir la carte';
            }
        });
    }

    // Recherche de commune avec anti-rebond
    const searchInput = document.getElementById('commune-search-cartoguide');
    if (searchInput) {
        let debounceTimer;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                searchCartoguideCommunes(e.target.value.trim());
            }, 250);
        });
    }

    // Masquer les résultats si clic extérieur
    document.addEventListener('click', (e) => {
        const searchContainer = document.getElementById('search-results-cartoguide');
        const searchInputEl = document.getElementById('commune-search-cartoguide');
        if (searchContainer && !searchContainer.contains(e.target) && e.target !== searchInputEl) {
            searchContainer.classList.add('d-none');
        }
    });

    const btnToggle = document.getElementById('btn-toggle-map-cartoguide');
    if (btnToggle) {
        btnToggle.addEventListener('click', () => {
            if (cartoguideCarouselInstance) cartoguideCarouselInstance.next();
        });
    }

    // Ouverture du modal météo Cartoguide
    const weatherInfoBtn = document.querySelector('.weather-info-cartoguide');
    if (weatherInfoBtn) {
        weatherInfoBtn.addEventListener('click', () => {
            renderCartoguideModalContent();
            if (cartoguideCarouselInstance) cartoguideCarouselInstance.to(0);
            const weatherModal = new bootstrap.Modal(document.getElementById('weatherModalCartoguide'));
            weatherModal.show();
        });
    }

    // Premier chargement + rafraîchissement périodique
    fetchCartoguideWeather();
    setInterval(() => fetchCartoguideWeather(cartoguideCurrentLocationName), CARTOGUIDE_REFRESH_INTERVAL_MS);
});
</script>
            <?php endif; ?>
            <?php if ($indexPage): ?>
                <!-- <hr> -->
            <?php endif; ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['isUserConnected'])): ?>
            <!-- Afficher le menu et le footer si l'utilisateur est connecté -->
            <!-- <nav id="vertical-sidebar" class="bg-light position-fixed ms-3 mt-3 mb-3 p-3">

                <p class="text-decoration-none text-dark mb-3 d-block" href="/admin/accueil">
                    <strong>Espace membres du site</strong>
                </p>

                <ul class="nav nav-pills flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?= $active1 == true ? 'active' : '' ?>" href="/admin/accueil">Accueil</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= $active2 == true ? 'active' : '' ?> <?= $active3 == true ? 'active' : '' ?> <?= $active4 == true ? 'active' : '' ?>"
                            href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Pages du site
                        </a>
                        <ul class="dropdown-menu border-0 shadow-none">
                            <li><a class="dropdown-item <?= $active2 == true ? 'active' : '' ?>"
                                    href="/avva-admin/page/liste">Liste de page</a></li>
                            <li><a class="dropdown-item <?= $active3 == true ? 'active' : '' ?>"
                                    href="/avva-admin/page/creer">Ajout de page</a></li>
                            <li><a class="dropdown-item <?= $active4 == true ? 'active' : '' ?>"
                                    href="/avva-admin/page/liste">Modification de page</a></li> -->
            <!-- <li><a class="dropdown-item" href="">Suppression de page</a> -->
            <!-- </li>
                </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/avva-admin/logout">Se déconnecter</a>
                </li>
                </ul>
            </nav> -->
            <!--  -->
            <div
                class="<?= $active1 || $active2 || $active3 || $active4 || $active5 || $active6 || $active7 || $active8 || $active9 || $active10 || $active11 || $active12 || $active13 || $active14 || $active15 ? 'd-flex flex-grow-1' : '' ?>">
                <aside
                    class="sidebar <?= $active1 || $active2 || $active3 || $active4 || $active5 || $active6 || $active7 || $active8 || $active9 || $active10 || $active11 || $active12 || $active13 || $active14 || $active15 ? 'flex-shrink-0' : '' ?><?= $active16 ? 'd-none' : 'd-block' ?>">
                    <!-- Sidebar header -->
                    <header class="sidebar-header">
                        <a href="/avva-admin/accueil" class="header-logo">
                            <img src="/assets/images/logo-avva39.png" alt="AVVA39">
                        </a>
                        <button class="toggler sidebar-toggler">
                            <span class="material-symbols-rounded">chevron_left</span>
                        </button>
                        <button class="toggler menu-toggler">
                            <span class="material-symbols-rounded">menu</span>
                        </button>
                    </header>

                    <nav class="sidebar-nav">
                        <!-- Primary top nav -->
                        <ul class="nav-list primary-nav" id="primary-nav-scroll">
                            <div class="scroll-indicator top" id="scroll-indicator-top">
                                <i class="fa-solid fa-angle-up"></i>
                            </div>

                            <li class="nav-item <?= $active1 ? 'active' : '' ?>">
                                <a href="/avva-admin/accueil" class="nav-link">
                                    <span class="nav-icon material-symbols-rounded">dashboard</span>
                                    <span class="nav-label">Accueil</span>
                                </a>
                                <span class="nav-tooltip">Accueil</span>
                            </li>

                            <?php if ($_SESSION['user']['idRole'] == 1): ?>
                                <li class="nav-item <?= $active2 ? 'active' : '' ?>">
                                    <a href="/avva-admin/liste-utilisateur" class="nav-link">
                                        <span class="nav-icon material-symbols-rounded">groups</span>
                                        <span class="nav-label">Gestion des utilisateurs</span>
                                    </a>
                                    <span class="nav-tooltip">Gestion des utilisateurs</span>
                                </li>
                            <?php endif; ?>

                            <li class="nav-item <?= $active3 ? 'active' : '' ?>">
                                <a href="/avva-admin/liste-membres" class="nav-link">
                                    <span class="nav-icon material-symbols-rounded">groups</span>
                                    <span class="nav-label">Membres</span>
                                </a>
                                <span class="nav-tooltip">Membres</span>
                            </li>

                            <li
                                class="nav-item <?= $active4 || $active5 || $active6 || $active7 || $active8 || $active9 ? 'active submenu-open' : '' ?>">
                                <a href="/avva-admin/page/liste" class="nav-link">
                                    <span class="nav-icon material-symbols-rounded">description</span>
                                    <span class="nav-label">Pages</span>
                                </a>
                                <span class="nav-tooltip">Pages</span>

                                <ul class="sub-menu">
                                    <li class="nav-item-sub">
                                        <a href="/avva-admin/page/liste" class="nav-link <?= $active4 ? 'active' : '' ?>">
                                            <span class="nav-label">Liste de pages</span>
                                        </a>
                                    </li>
                                    <li class="nav-item-sub">
                                        <a href="/avva-admin/page/creer" class="nav-link <?= $active5 ? 'active' : '' ?>">
                                            <span class="nav-label">Ajout de page</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="nav-item <?= $active10 ? 'active' : '' ?>">
                                <a href="/avva-admin/page/modifier/6" class="nav-link">
                                    <span class="nav-icon material-symbols-rounded">calendar_month</span>
                                    <span class="nav-label">Calendrier</span>
                                </a>
                                <span class="nav-tooltip">Calendrier</span>
                            </li>

                            <li class="nav-item <?= $active11 ? 'active' : '' ?>">
                                <a href="/avva-admin/randonnee" class="nav-link">
                                    <span class="nav-icon material-symbols-rounded">directions_bike</span>
                                    <span class="nav-label">Randonnées</span>
                                </a>
                                <span class="nav-tooltip">Randonnées</span>
                            </li>

                            <li class="nav-item <?= $active12 ? 'active' : '' ?>">
                                <a href="/avva-admin/sortie" class="nav-link">
                                    <span class="nav-icon material-symbols-rounded">directions_bike</span>
                                    <span class="nav-label">Sortie hebdomadaire</span>
                                </a>
                                <span class="nav-tooltip">Sortie hebdomadaire</span>
                            </li>

                            <li class="nav-item <?= $active13 ? 'active' : '' ?>">
                                <a href="/avva-admin/comptes-rendus" class="nav-link">
                                    <span class="nav-icon material-symbols-rounded">article</span>
                                    <span class="nav-label">Comptes rendus</span>
                                </a>
                                <span class="nav-tooltip">Comptes rendus</span>
                            </li>

                            <?php if ($_SESSION['user']['idRole'] == 1): ?>
                                <li class="nav-item <?= $active14 ? 'active' : '' ?>">
                                    <a href="/avva-admin/settings" class="nav-link">
                                        <span class="nav-icon material-symbols-rounded">settings</span>
                                        <span class="nav-label">Paramètres du site</span>
                                    </a>
                                    <span class="nav-tooltip">Paramètres du site</span>
                                </li>
                            <?php endif; ?>

                            <li class="nav-item">
                                <a href="/avva-admin/cartoguide" class="nav-link">
                                    <span class="nav-icon material-symbols-rounded">map</span>
                                    <span class="nav-label">Cartoguide</span>
                                </a>
                                <span class="nav-tooltip">Cartoguide</span>
                            </li>

                            <div class="scroll-indicator bottom" id="scroll-indicator-bottom">
                                <i class="fa-solid fa-angle-down"></i>
                            </div>
                        </ul>

                        <!-- Secondary bottom nav -->
                        <ul class="nav-list secondary-nav">
                            <li class="nav-item">
                                <a href="/avva-admin/profile" class="nav-link">
                                    <span class="nav-icon material-symbols-rounded">account_circle</span>
                                    <span class="nav-label"><?= $_SESSION['user']['email'] ?></span>
                                </a>
                                <span class="nav-tooltip"><?= $_SESSION['user']['email'] ?></span>
                            </li>
                            <li class="nav-item">
                                <a href="/avva-admin/logout" class="nav-link">
                                    <span class="nav-icon material-symbols-rounded">logout</span>
                                    <span class="nav-label">Se déconnecter</span>
                                </a>
                                <span class="nav-tooltip">Se déconnecter</span>
                            </li>
                        </ul>
                    </nav>
                </aside>
                <!--  -->
            <?php endif; ?>
            <main
                class="container-fluid <?= $active1 || $active2 || $active3 || $active4 || $active5 || $active6 || $active7 || $active8 || $active9 || $active10 || $active11 || $active12 || $active13 || $active14 || $active15 ? 'col-md-6 content-main flex-grow-1 p-3' : '' ?>">
                <?= $content ?>
            </main>
        </div>
    </div>
    <?php if ($index): ?>
        <?php if (!$isCartoguide): ?>
        <footer class="avva-footer sticky-bottom" style="background-color: var(--avva-bleu);">
            <div class="avva-footer-perspective">
                <div class="container-fluid">
                    <div
                        class="d-flex justify-content-center justify-content-md-between align-items-md-start align-items-center flex-column flex-md-row">
                        <div style="color: var(--avva-jaune)">
                            <ul class="list-unstyled small">
                                <li class="d-flex justify-content-center d-md-block">
                                    <span>Vous êtes le
                                        <?= $nombreVisite == 1 ? $nombreVisite . 'er' : $nombreVisite . 'ème' ?>
                                    </span>
                                </li>
                                <li class="d-flex align-items-center">
                                    <div>
                                        <a href="<?= $settings->getPartenaire1Url() ?>" target="_blank"><img class="me-2"
                                                style="max-width: 60px;" src="/assets/images/logo-jura-cycles.png"
                                                alt=""></a>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span><span style="font-weight: bold"><?= $settings->getPartenaire1Nom() ?></span>,
                                            <?= $settings->getPartenaire1AdresseRue() ?></span>
                                        <span><?= $settings->getPartenaire1AdresseCpVille() ?>. Tèl :
                                            <?= $settings->getPartenaire1Tel() ?></span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div style="color: var(--avva-jaune);">
                            <ul class="list-unstyled small">
                                <li><i class="fas fa-map-marker-alt me-2"></i><?= $settings->getPresidentNom() ?>
                                    /<span class="fw-italic">Président</span></li>
                                <li style="margin-left: 10%;"><?= $settings->getPresidentAdresseRue() ?></li>
                                <li style="margin-left: 10%;"><?= $settings->getPresidentAdresseCpVille() ?></li>
                            </ul>
                        </div>
                        <div style="color: var(--avva-jaune);">
                            <ul class="list-unstyled small">
                                <li><i class="fas fa-envelope me-2" style="color: var(--avva-jaune);"></i> <a
                                        href="mailto:<?= $settings->getContactEmail() ?>"
                                        style="color: var(--avva-jaune);"><?= $settings->getContactEmail() ?></a></li>
                                <li><i class="fas fa-phone me-2"></i> <a href="tel:<?= $settings->getContactPhone() ?>"
                                        style="color: var(--avva-jaune);"><?= $settings->getContactPhone() ?></a></li>
                            </ul>
                        </div>
                        <div>
                            <ul class="list-unstyled small">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <li><a href="<?= $settings->getSocialFacebookUrl() ?>" target="_blank"
                                                class="social-icon" style="color: var(--avva-jaune);"><i
                                                    class="fab fa-facebook-square me-2"
                                                    style="color: var(--avva-jaune);"></i>Facebook</a></li>
                                        <li><a href="<?= $settings->getSocialYoutubeUrl() ?>" target="_blank"
                                                class="social-icon" style="color: var(--avva-jaune);"><i
                                                    class="fab fa-youtube-square me-2"
                                                    style="color: var(--avva-jaune);"></i>Youtube</a></li>
                                    </div>
                                    <div class="ms-3">
                                        <li><a href="https://www.tiktok.com/@amicale.velo.du.v" target="_blank"
                                                class="social-icon" style="color: var(--avva-jaune);"><i
                                                    class="fab fa-tiktok me-2"
                                                    style="color: var(--avva-jaune);"></i>Tiktok</a>
                                        </li>
                                        <li><a href="https://www.instagram.com/amicale_velo_du_val_damour" target="_blank"
                                                class="social-icon" style="color: var(--avva-jaune);"><i
                                                    class="fab fa-instagram me-2"
                                                    style="color: var(--avva-jaune);"></i>Instagram</a></li>
                                    </div>
                                </div>
                            </ul>
                        </div>
                        <div class="d-flex align-items-center flex-column">
                            <div class="d-flex justify-content-center align-items-center">
                                <a href="<?= $settings->getFfveloUrl() ?>" target="_blank"><img class="me-2"
                                        style="max-width: 40px;" src="/assets/images/FFVelo-logo.png" alt=""></a>
                                <span style="color: var(--avva-jaune);">Fédération Française de Cyclotourisme</span>
                            </div>
                            <div class="d-flex justify-content-center align-items-center">
                                <a href="<?= $settings->getCodep39Url() ?>" target="_blank"><img class="me-2"
                                        style="max-width: 60px;" src="/assets/images/39.png" alt=""></a>
                                <span style="color: var(--avva-jaune);">FFVÉLO dépt. Jura</span>
                            </div>
                        </div>
                        <?php
                        $nombreSorties = count($sorties);
                        $margin_value = 10 + ($nombreSorties * 3);
                        ?>
                        <div class="rides-wrapper">
                            <div id="rides-list-container" class="d-flex flex-column"
                                style="margin-right: <?= $margin_value; ?>px;">
                                <div id="loading-rides" class="text-center p-3">
                                    <p class="text-white mb-0 italic">Recherche des prochaines sorties...</p>
                                </div>
                            </div>
                        </div>

                        <style>
                            .ride-container {
                                cursor: pointer;
                                transition: transform 0.2s ease;
                                border-radius: 8px;
                                padding: 5px;
                            }

                            .ride-container:hover {
                                transform: scale(1.02);
                                background: rgba(255, 255, 255, 0.1);
                            }

                            .countdown-text {
                                font-weight: bold;
                                display: block;
                            }

                            .departure-text {
                                color: yellow;
                                font-size: 0.85rem;
                                margin-top: -5px;
                            }

                            .type-badge-mini {
                                font-size: 0.7rem;
                                padding: 2px 6px;
                                margin-left: 5px;
                                vertical-align: middle;
                            }
                        </style>
                        <!-- <div class="col-md-3 mb-4">
                        <ul class="list-unstyled small">
                            <li><a href="mentions-legales.html">Mentions Légales</a></li>
                            <li><a href="politique-confidentialite.html">Politique de Confidentialité</a></li>
                        </ul>
                    </div> -->
                    </div>
                    <div
                        class="avva-credits container-fluid d-flex justify-content-between align-items-center flex-column flex-md-row">
                        <div class="avva-credits-logo small text-light text-center">
                            <a href="https://meteastro.fr" target="_blank"><img style="max-width: 70px"
                                    src="/assets/images/logo-meteastro.png" alt=""></a>
                        </div>
                        <div class="avva-credits-avva small text-light text-center">
                            &copy; <?= date('Y') ?> AVVA 39 - Amicale Vélo du Val d'Amour. Tous droits réservés.
                        </div>
                        <div class="avva-credits-logo small text-light text-center">
                            <a href="https://meteastro.fr" target="_blank"><img style="max-width: 70px"
                                    src="/assets/images/logo-meteastro.png" alt=""></a>
                        </div>
                    </div>
                    <div class="app-credits-bar container-fluid py-3 px-4">
                        <div class="row align-items-center g-3">

                            <div class="col-12 col-md-3 text-center text-md-start">
                                <a href="https://meteastro.fr" target="_blank" class="app-brand-link">
                                    <img src="/assets/images/logo-meteastro.png" alt="Meteastro" class="app-logo">
                                </a>
                            </div>

                            <div class="col-12 col-md-6 text-center">
                                <div class="app-control-panel d-inline-flex flex-column align-items-center gap-2">

                                    <button type="button" id="about-btn" onclick="app.showAboutModal()"
                                        class="btn app-btn-system d-inline-flex align-items-center gap-2">
                                        <span class="status-indicator"></span>
                                        <span class="btn-text">AVVA39</span>
                                        <span class="badge bg-light text-dark rounded-pill">Info</span>
                                    </button>
                                </div>
                            </div>

                            <div class="col-12 col-md-3 text-center text-md-end">
                                <a href="https://meteastro.fr" target="_blank" class="app-brand-link">
                                    <img src="/assets/images/logo-meteastro.png" alt="Meteastro" class="app-logo">
                                </a>
                            </div>

                        </div>
                    </div>
                    <style>
                        .avva-credits {
                            display: flex !important;
                        }

                        .app-credits-bar {
                            display: none !important;
                        }

                        @media (display-mode: standalone),
                        (display-mode: fullscreen),
                        (display-mode: minimal-ui) {
                            .app-credits-bar {
                                display: flex !important;
                            }

                            .avva-credits {
                                display: none !important;
                            }
                        }

                        /* Logos */
                        .app-logo {
                            max-width: 65px;
                            height: auto;
                            transition: transform 0.2s ease, opacity 0.2s ease;
                            opacity: 0.85;
                        }

                        .app-logo:hover {
                            transform: scale(1.05);
                            opacity: 1;
                        }

                        /* Bouton style Application / Fiche Système */
                        .app-btn-system {
                            background: rgba(255, 255, 255, 0.05);
                            border: 1px solid rgba(255, 255, 255, 0.15);
                            color: #ffffff;
                            padding: 6px 16px;
                            border-radius: 20px;
                            font-size: 0.85rem;
                            font-weight: 500;
                            letter-spacing: 0.3px;
                            transition: all 0.25s ease;
                            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
                        }

                        .app-btn-system:hover {
                            background: rgba(255, 255, 255, 0.12);
                            border-color: rgba(255, 255, 255, 0.3);
                            color: #ffffff;
                            transform: translateY(-1px);
                            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
                        }

                        /* Point vert clignotant (Status Indicator) */
                        .status-indicator {
                            width: 8px;
                            height: 8px;
                            background-color: #10b981;
                            border-radius: 50%;
                            box-shadow: 0 0 8px #10b981;
                            animation: pulse 2s infinite;
                        }

                        @keyframes pulse {
                            0% {
                                opacity: 1;
                                transform: scale(1);
                            }

                            50% {
                                opacity: 0.4;
                                transform: scale(0.85);
                            }

                            100% {
                                opacity: 1;
                                transform: scale(1);
                            }
                        }

                        /* Copyright Texte */
                        .app-copyright {
                            font-size: 0.78rem;
                            color: #9ca3af !important;
                        }
                    </style>
                </div>
            </div>
        </footer>
        <div class="modal fade" id="descriptionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-end"
                style="width: 70vw; max-height: 80vh; margin-bottom: 80px !important;">
                <div class="modal-content glass-input h-100">
                    <div class="modal-header bg-primary text-white">
                        <h4 class="modal-title" id="modal-ride-title"></h4>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body overflow-auto p-4">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="p-3 bg-light rounded shadow-sm h-100">
                                    <h6 class="text-primary mb-2"><i class="fas fa-tags"></i> Type(s) de sortie</h6>
                                    <div id="modal-ride-types-container" class="d-flex flex-wrap gap-2"></div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="p-3 bg-light rounded shadow-sm h-100">
                                    <h6 class="text-primary mb-2"><i class="far fa-clock"></i> Départ prévu</h6>
                                    <p id="modal-ride-date" class="fs-5 fw-bold text-dark mb-0"></p>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4 p-3 bg-light rounded shadow-sm">
                            <h6 class="text-primary mb-2"><i class="fas fa-align-left"></i> Description</h6>
                            <div id="modal-ride-description" class="fs-6 text-dark"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning fw-bold" data-bs-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
            <footer class="cartoguide-footer">
    <div class="cartoguide-control-panel d-flex flex-column align-items-center gap-1">

        <button type="button" id="cartoguide-about-btn" onclick="cartoguideApp.showAboutModal()"
            class="btn cartoguide-btn-system d-inline-flex align-items-center gap-2">
            <span class="cartoguide-status-indicator" aria-hidden="true"></span>
            <span class="btn-text fw-bold text-warning">AVVA39</span>
            <span class="badge bg-warning text-dark rounded-pill fw-bold">Cartoguide</span>
        </button>

    </div>
</footer>
<style>
    /* Container principal : Capsule flottante */
.cartoguide-footer {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1030;
    
    /* Dimensionnement compact */
    width: auto;
    max-width: calc(100vw - 32px);
    padding: 8px 20px !important;
    
    /* Style Glassmorphism sombre */
    background: rgba(15, 23, 42, 0.85);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 230, 0, 0.25);
    border-radius: 50px;
    
    /* Ombre et halo néon */
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5), 
                0 0 15px rgba(255, 230, 0, 0.1);
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.cartoguide-footer:hover {
    border-color: rgba(255, 230, 0, 0.5);
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.6), 
                0 0 20px rgba(255, 230, 0, 0.2);
}

/* Bouton style Système / Fiche Cartoguide */
.cartoguide-btn-system {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 230, 0, 0.25);
    color: #ffffff;
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 0.82rem;
    font-weight: 500;
    letter-spacing: 0.3px;
    transition: all 0.25s ease;
}

.cartoguide-btn-system:hover {
    background: rgba(255, 230, 0, 0.15);
    border-color: #ffe600;
    color: #ffffff;
    transform: translateY(-1px);
}

/* Témoin lumineux clignotant */
.cartoguide-status-indicator {
    width: 8px;
    height: 8px;
    background-color: #10b981;
    border-radius: 50%;
    box-shadow: 0 0 8px #10b981;
    animation: cartoguidePulse 2s infinite ease-in-out;
}

@keyframes cartoguidePulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.4;
        transform: scale(0.85);
    }
}

/* Copyright Texte */
.cartoguide-copyright {
    font-size: 0.72rem;
    color: #9ca3af !important;
    white-space: nowrap;
}

/* Logos Cartoguide */
.cartoguide-logo {
    max-width: 65px;
    height: auto;
    transition: transform 0.2s ease, opacity 0.2s ease;
    opacity: 0.85;
}

.cartoguide-logo:hover {
    transform: scale(1.05);
    opacity: 1;
}

/* Affichage conditionnel Web App / PWA */
.avva-credits {
    display: flex !important;
}

.cartoguide-credits-bar {
    display: none !important;
}

@media (display-mode: standalone),
       (display-mode: fullscreen),
       (display-mode: minimal-ui) {
    .cartoguide-credits-bar {
        display: flex !important;
    }

    .avva-credits {
        display: none !important;
    }
}
</style>
        <?php endif; ?>
    <?php endif; ?>
    <!-- Modal pour afficher les détails d’un événement -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-light text-light">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="eventModalLabel">Détail de l’événement</h5>
                    <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="modal"
                        aria-label="Fermer"></button>
                </div>
                <div class="modal-body" id="eventModalBody">
                    <!-- Contenu chargé dynamiquement -->
                    <div class="text-center py-4">
                        <div class="spinner-border text-info" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-info" data-bs-dismiss="modal">Fermer</button>

                    <a id="btnVoirCompteRendu" href="" class="btn btn-gradient px-4 rounded-pill" target="_blank">
                        <i class="bi bi-journal-text me-2"></i> Voir le compte rendu
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div id="notification-onboarding"
        style="display: none; position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); width: 90%; max-width: 400px; background: rgba(10, 14, 23, 0.95); border: 1px solid rgba(0, 180, 210, 0.3); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.6); backdrop-filter: blur(10px); padding: 20px; border-radius: 16px; z-index: 999999; font-family: system-ui, -apple-system, Roboto, sans-serif;">
        <h3 style="margin-top: 0; color: #ffffff; font-size: 16px; font-weight: 600;">Activer les notifications ? 🚴‍♂️
        </h3>
        <p style="color: #a0aec0; font-size: 13px; margin: 8px 0 0 0;">Restez informé en temps réel des sorties, courses
            et actualités du club AVVA39 directement sur votre appareil.</p>
        <div style="text-align: right; margin-top: 15px;">
            <button id="btn-refuse-notif"
                style="background: none; border: none; color: #a0aec0; margin-right: 15px; cursor: pointer; font-weight: 600; font-size: 13px;">Plus
                tard</button>
            <button id="btn-accept-notif"
                style="background: linear-gradient(135deg, #008099 0%, #005566 100%); border: none; color: #ffffff; padding: 8px 16px; border-radius: 100px; cursor: pointer; font-weight: 600; font-size: 13px; box-shadow: 0 2px 8px rgba(0, 180, 210, 0.3);">Autoriser</button>
        </div>
    </div>
    <script>
        /**
 * GESTION MULTI-SORTIES : Initialisation et Compte à Rebours
 */

        const ridesContainer = document.getElementById('rides-list-container');
        let colorToggle = true; // Pour l'alternance rouge/jaune globale

        /**
         * 1. Initialisation : Génère le HTML pour chaque sortie au chargement
         */
        function initRides() {
            if (!RIDES_DATA || RIDES_DATA.length === 0) {
                ridesContainer.innerHTML = '<p id="no-ride-msg" class="italic text-center countdown-text"><?= ($messageSortieHebdomadaireADefinir) ? strtoupper($messageSortieHebdomadaireADefinir->getMessage()) : ''; ?></p>';
                return;
            }

            ridesContainer.innerHTML = '';

            RIDES_DATA.forEach((ride, index) => {
                // On récupère l'icône du premier type ou une icône par défaut
                const firstIcon = ride.types.length > 0 ? ride.types[0].icon : 'fa-bicycle';

                const rideHtml = `
            <div class="ride-container mb-3" onclick="showRideDetails(${index})">
                <p id="timer-${index}" class="countdown-text mb-0">
                    <i class="fa-solid ${firstIcon} me-2"></i> Chargement...
                </p>
                <p id="dep-${index}" class="departure-text text-end">Départ à ${ride.heureDepart}</p>
            </div>
        `;
                ridesContainer.insertAdjacentHTML('beforeend', rideHtml);
            });
        }

        /**
         * 2. Utilitaire : Conversion HH:mm en Millisecondes
         */
        function durationToMs(timeString) {
            if (!timeString) return 0;
            const [hours, minutes] = timeString.split(':').map(Number);
            return ((hours * 60) + (minutes || 0)) * 60 * 1000;
        }

        /**
         * 3. Logique principale du compte à rebours
         */
        function updateCountdowns() {
            const now = new Date();

            // Couleur alternée globale pour synchroniser les clignotements
            const currentColor = colorToggle ? 'var(--avva-rouge)' : 'var(--avva-jaune)';
            colorToggle = !colorToggle;

            const noRideMsg = document.getElementById('no-ride-msg');
            if (noRideMsg) {
                noRideMsg.style.color = currentColor;
                return; // On arrête là si aucune donnée n'est présente
            }

            RIDES_DATA.forEach((ride, index) => {
                const timerElement = document.getElementById(`timer-${index}`);
                const depElement = document.getElementById(`dep-${index}`);

                if (!timerElement) return;

                const targetDate = new Date(ride.dateString);
                const distance = targetDate.getTime() - now.getTime();
                const durationMs = durationToMs(ride.temps);
                const endDate = new Date(targetDate.getTime() + durationMs);

                // Récupération des infos de types pour l'affichage
                const firstIcon = ride.types.length > 0 ? ride.types[0].icon : 'fa-bicycle';
                const typesLabel = ride.types.map(t => t.nom).join(' / '); // Combine les noms (ex: Route / VTT)
                const difficulte = ride.difficulte;

                // CAS 1 : La sortie est passée
                if (distance < 0) {
                    depElement.style.display = 'none';
                    timerElement.style.color = 'var(--avva-rouge)';
                    timerElement.id = 'no-ride-msg';

                    if (now.getTime() < endDate.getTime()) {
                        // En cours
                        timerElement.innerHTML = `<span class="icone-velo">🚴</span> ${typesLabel} ${difficulte} : SORTIE EN COURS !`;
                    } else {
                        // Terminée (Message personnalisé de la base de données)
                        const rideContainer = timerElement.closest('.ride-container');
                        if (rideContainer) {
                            rideContainer.style.pointerEvents = 'none'; // Rend l'élément non cliquable
                            rideContainer.style.cursor = 'default';     // Remet le curseur normal
                        }
                        timerElement.innerHTML = `${ride.messageApresSortieHebdomadaire}`;
                    }
                    return;
                }

                // CAS 2 : Compte à rebours actif
                const MS_PER_DAY = 1000 * 60 * 60 * 24;
                const MS_PER_HOUR = 1000 * 60 * 60;
                const MS_PER_MIN = 1000 * 60;

                let timeText = "";

                if (distance >= MS_PER_DAY) {
                    const days = Math.floor(distance / MS_PER_DAY);
                    timeText = `J-${days}`;
                } else if (distance >= MS_PER_HOUR) {
                    const hours = Math.ceil(distance / MS_PER_HOUR);
                    timeText = `H-${hours}`;
                } else if (distance >= MS_PER_MIN) {
                    const minutes = Math.ceil(distance / MS_PER_MIN);
                    timeText = `M-${minutes}`;
                } else {
                    const seconds = Math.ceil(distance / 1000);
                    timeText = `DÉPART IMMINENT S-${seconds}`;
                }

                timerElement.innerHTML = `<span class="icone-velo">🚴</span> ${typesLabel} ${difficulte} : ${timeText}`;
                timerElement.style.color = currentColor;
                depElement.style.display = 'block';
            });
        }

        /**
         * 4. Lancement
         */
        document.addEventListener('DOMContentLoaded', () => {
            initRides();
            updateCountdowns();
            setInterval(updateCountdowns, 1000); // Mise à jour toutes les secondes
        });
    </script>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/navbar.js"></script>
    <script>
        /**
         * 1. PREPARATION DES DONNEES (PHP vers JS)
         * On transforme la collection de types en un tableau exploitable par le JS.
         */
        const RIDES_DATA = <?php
        $export = [];
        // Mapping des icônes selon le nom du type
        $iconMapping = [
            'Route' => 'fa-bicycle',
            'VTT' => 'fa-mountain-sun',
            'Gravel' => 'fa-bicycle',
            'Course' => 'fa-hiking'
        ];

        if (!empty($sorties)) {
            foreach ($sorties as $sortie) {
                // Extraction de TOUS les types associés (ManyToMany)
                $typesCollection = [];
                foreach ($sortie->getTypesSorties() as $typeEntity) {
                    $name = $typeEntity->getNom();
                    $typesCollection[] = [
                        'nom' => $name,
                        'icon' => $iconMapping[$name] ?? 'fa-bicycle'
                    ];
                }

                $export[] = [
                    'id' => $sortie->getId(),
                    'titre' => $sortie->getTitre(),
                    'dateString' => $sortie->getDate()->format('M j, Y H:i:s'),
                    'heureDepart' => $sortie->getDate()->format('H\hi'),
                    'temps' => $sortie->getTemps()->format('H:i'),
                    'messageApresSortieHebdomadaire' => strtoupper($messageApresSortieHebdomadaire->getMessage()),
                    'types' => $typesCollection, // Tableau d'objets {nom, icon}
                    'description' => nl2br(htmlspecialchars($sortie->getDescription() ?? '')),
                    'difficulte' => nl2br(htmlspecialchars($sortie->getDifficulte() ?? ''))
                ];
            }
        }
        echo json_encode($export);
        ?>;

        /**
         * 2. FONCTION D'AFFICHAGE DES DETAILS (MODALE)
         * Gère l'affichage dynamique des badges de types multiples.
         */
        function showRideDetails(index) {
            const ride = RIDES_DATA[index];
            if (!ride) return;

            // --- Formatage de la date ---
            const dateObj = new Date(ride.dateString);
            let dateFr = dateObj.toLocaleDateString('fr-FR', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            dateFr = dateFr.charAt(0).toUpperCase() + dateFr.slice(1);

            // --- Injection du Titre ---
            document.getElementById('modal-ride-title').textContent = `Détails de la sortie : ${ride.titre}`;

            // --- Injection des Types (Multiples) ---
            const typeContainer = document.getElementById('modal-ride-types-container');
            if (typeContainer) {
                typeContainer.innerHTML = ''; // Reset

                if (ride.types.length === 0) {
                    typeContainer.innerHTML = '<span class="text-muted small">Aucun type spécifié</span>';
                } else {
                    ride.types.forEach(t => {
                        const badge = `
                        <span class="badge bg-primary fs-6 me-1 mb-1">
                            <i class="fa-solid ${t.icon} me-1"></i> ${t.nom}
                        </span>`;
                        typeContainer.insertAdjacentHTML('beforeend', badge);
                    });
                }
            }

            // --- Injection de la Date et l'Heure ---
            const dateElement = document.getElementById('modal-ride-date');
            if (dateElement) {
                dateElement.innerHTML = `
                <i class="far fa-calendar-alt text-primary me-2"></i>
                ${dateFr} à <span class="text-primary">${ride.heureDepart}</span>
            `;
            }

            // --- Injection de la Description ---
            const descElement = document.getElementById('modal-ride-description');
            if (descElement) {
                descElement.innerHTML = ride.description || '<em class="text-muted">Aucune description fournie.</em>';
            }

            // --- Affichage de la modale ---
            const modalElement = document.getElementById('descriptionModal');
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
            modalInstance.show();
        }
    </script>
    <script src="/assets/js/decompte-sortie-depart.js"></script>
    <script src="/assets/js/heure-date.js"></script>
    <script src="/assets/js/plein-ecran.js"></script>
    <script src="/assets/js/battement-logo.js"></script>
    <script src="/assets/js/chargement-logo.js"></script>
    <script src="/assets/js/animation-bouton.js"></script>
    <script src="/assets/js/meteo.js"></script>
    <!-- <script>
        $('#contenu_page').summernote({
            placeholder: 'Contenu de la page',
            tabsize: 2,
            height: 120,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    </script> -->

    <!--  -->
    <script>
        const sidebar = document.querySelector(".sidebar");
        const sidebarToggler = document.querySelector(".sidebar-toggler");
        const menuToggler = document.querySelector(".menu-toggler");
        // Ensure these heights match the CSS sidebar height values
        let collapsedSidebarHeight = "56px"; // Height in mobile view (collapsed)
        let fullSidebarHeight = "calc(100vh - 32px)"; // Height in larger screen
        // Toggle sidebar's collapsed state
        sidebarToggler.addEventListener("click", () => {
            sidebar.classList.toggle("collapsed");
        });
        // Update sidebar height and menu toggle text
        const toggleMenu = (isMenuActive) => {
            sidebar.style.height = isMenuActive ? `${sidebar.scrollHeight}px` : collapsedSidebarHeight;
            menuToggler.querySelector("span").innerText = isMenuActive ? "close" : "menu";
        }
        // Toggle menu-active class and adjust height
        menuToggler.addEventListener("click", () => {
            toggleMenu(sidebar.classList.toggle("menu-active"));
        });
        // (Optional code): Adjust sidebar height on window resize
        window.addEventListener("resize", () => {
            if (window.innerWidth >= 1024) {
                sidebar.style.height = fullSidebarHeight;
            } else {
                sidebar.classList.remove("collapsed");
                sidebar.style.height = "auto";
                toggleMenu(sidebar.classList.contains("menu-active"));
            }
        });
    </script>
    <script>
        const form = document.getElementById('editPageForm');
        const previewIframe = document.getElementById('livePreview');
        let timeout = null;

        // Fonction pour mettre à jour l'iframe
        function updatePreview() {
            const formData = new FormData(form);

            fetch('/avva-admin/page/apercu/<?= $page->getId() ?>?live=1', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // permet de détecter un live preview côté PHP
                }
            })
                .then(response => response.text())
                .then(html => {
                    previewIframe.srcdoc = html; // injecte le HTML dans l'iframe
                })
                .catch(err => console.error('Erreur de prévisualisation :', err));
        }

        // Fonction pour ajuster la hauteur de l'iframe automatiquement
        function resizeIframe() {
            try {
                const doc = previewIframe.contentDocument || previewIframe.contentWindow.document;
                previewIframe.style.height = doc.body.scrollHeight + 'px';
            } catch (e) {
                console.error('Impossible de redimensionner l’iframe :', e);
            }
        }

        // Déclencher update avec un debounce pour éviter trop de requêtes
        function debounceUpdate() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                updatePreview();
            }, 300); // 300ms d'attente après la dernière frappe
        }

        // Écoute tous les changements du formulaire
        form.addEventListener('input', debounceUpdate);
        form.addEventListener('change', debounceUpdate); // pour select, checkbox, etc.

        // Ajuster la hauteur après chaque chargement de l'iframe
        previewIframe.addEventListener('load', resizeIframe);

        // Initialisation au chargement
        updatePreview();
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                themeSystem: 'bootstrap5',
                firstDay: 1, // Lundi

                buttonText: {
                    today: "Aujourd'hui",
                    month: 'Mois',
                    week: 'Semaine',
                    day: 'Jour',
                    list: 'Liste'
                },

                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },

                events: <?= $events ?>, // Les données dynamiques PHP avec start / end

                editable: false,
                selectable: true,
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },

                eventContent: function (arg) {
                    const start = arg.event.start;
                    const end = arg.event.end;

                    const options = { hour: '2-digit', minute: '2-digit', hour12: false };
                    const startTime = new Date(start).toLocaleTimeString('fr-FR', options);
                    const endTime = end ? new Date(end).toLocaleTimeString('fr-FR', options) : null;

                    // Heure affichée si différente
                    let timeHTML = `<div class="fc-event-time">${startTime}`;
                    if (endTime && endTime !== startTime) {
                        timeHTML += ` - ${endTime}`;
                    }
                    timeHTML += `</div>`;

                    // Titre avec ou sans défilement
                    let titleHTML;
                    if (arg.event.title.length > 5) {
                        titleHTML = `<div class="fc-event-title marquee"><span>${arg.event.title}</span></div>`;
                    } else {
                        titleHTML = `<div class="fc-event-title">${arg.event.title}</div>`;
                    }

                    // ⚡ Important : renvoyer un tableau de nodes ou html
                    return { html: timeHTML + titleHTML };
                },

                noEventsText: 'Aucun événement à afficher',

                // 🔹 Met en gris ou diagonale les jours passés
                dayCellClassNames: function (arg) {
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    const cellDate = arg.date;
                    if (cellDate < today) {
                        return ['past-day'];
                    }
                    return [];
                },

                // 🔹 Clique sur un événement
                eventClick: function (info) {
                    info.jsEvent.preventDefault();

                    const modalBody = document.getElementById('eventModalBody');
                    const modalTitle = document.getElementById('eventModalLabel');
                    const btnVoirCR = document.getElementById('btnVoirCompteRendu');
                    modalTitle.textContent = info.event.title;

                    const modal = new bootstrap.Modal(document.getElementById('eventModal'));
                    // Vérification si le compte-rendu existe et n'est pas vide
                    if (info.event.extendedProps.compteRendu && info.event.extendedProps.compteRendu !== null) {
                        btnVoirCR.href = "/page/" + info.event.extendedProps.categorieUrl + "/compte-rendu/" + info.event.extendedProps.compteRenduId;
                        btnVoirCR.style.display = 'inline-block'; // Affiche le bouton
                    } else {
                        btnVoirCR.style.display = 'none'; // Masque le bouton si compte-rendu vide ou null
                    }
                    modal.show();

                    // 🕓 Formattage FR (heure de Paris)
                    const options = {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false,
                        timeZone: 'Europe/Paris'
                    };

                    const start = new Date(info.event.start).toLocaleString('fr-FR', options);
                    const end = info.event.end
                        ? new Date(info.event.end).toLocaleString('fr-FR', options)
                        : null;

                    const description = info.event.extendedProps.description || 'Aucune description disponible.';

                    // 📅 Affichage intelligent selon présence de date de fin
                    let dateHTML = '';
                    if (end) {
                        dateHTML = `
                    <p class="text-info mb-2">
                        <i class="bi bi-calendar-range"></i>
                        <strong>Du :</strong> ${start}<br>
                        <strong>Au :</strong> ${end}
                    </p>`;
                    } else {
                        dateHTML = `
                    <p class="text-info mb-2">
                        <i class="bi bi-calendar-event"></i>
                        <strong>Date :</strong> ${start}
                    </p>`;
                    }

                    modalBody.innerHTML = `
                ${dateHTML}
                <hr class="border-info opacity-50">
                <p>${description}</p>
            `;
                },

                // 🔹 Recoloration après navigation dans le calendrier
                datesSet: function () {
                    appliquerStyleJoursPasses();
                }
            });

            calendar.render();

            // 🔸 Styliser les jours passés
            function appliquerStyleJoursPasses() {
                const pastDays = document.querySelectorAll('.fc-daygrid-day.past-day');
                pastDays.forEach(day => {
                    if (!day.querySelector('.diagonal-strike')) {
                        const line = document.createElement('div');
                        line.classList.add('diagonal-strike');
                        day.appendChild(line);
                    }
                });
            }

            appliquerStyleJoursPasses();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const addBtn = document.getElementById('addDateBtn');
            const container = document.getElementById('dates-container');

            const createDateBlock = () => {
                const block = document.createElement('div');
                block.classList.add('date-block', 'mb-3', 'p-3', 'border', 'border-info', 'rounded', 'bg-light', 'shadow-sm');

                block.innerHTML = `
            <div class="row g-3 align-items-center">
                <div class="col-md-5">
                    <label class="form-label text-dark fw-semibold">
                        <i class="bi bi-clock-history text-info"></i> Du
                    </label>
                    <input type="datetime-local" name="dates_start[]" class="form-control glass-input"
                           placeholder="Date de début" required>
                </div>

                <div class="col-md-5">
                    <label class="form-label fw-semibold">
                        <span class="text-dark"><i class="bi bi-calendar-week text-warning"></i> Au</span>
                        <span class="facultatif-label ms-1">(facultatif)</span>
                    </label>
                    <input type="datetime-local" name="dates_end[]" class="form-control glass-input"
                           placeholder="Date de fin (facultative)">
                </div>

                <div class="col-md-2 text-center">
                    <button type="button" class="btn btn-outline-danger remove-date-btn mt-4">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
        `;

                block.querySelector('.remove-date-btn').addEventListener('click', () => {
                    block.remove();
                });

                return block;
            };

            addBtn.addEventListener('click', () => {
                container.appendChild(createDateBlock());
            });

            // Cache le bouton de suppression sur le premier bloc
            const firstRemove = container.querySelector('.remove-date-btn');
            if (firstRemove) firstRemove.style.display = 'none';
        });
    </script>
    <script>
        document.getElementById('categorie_event').addEventListener('change', function () {
            const newInput = document.getElementById('new_categorie_input');
            if (this.value === 'new') {
                newInput.style.display = 'block';
                newInput.required = true;
            } else {
                newInput.style.display = 'none';
                newInput.required = false;
            }
        });
    </script>
    <script>
        document.querySelectorAll('.toggle-submenu').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const submenu = this.parentElement.querySelector('.sub-menu');

                // Toggle affichage
                if (submenu.style.display === 'block') {
                    submenu.style.display = 'none';
                } else {
                    submenu.style.display = 'block';
                }
            });
        });
    </script>
    <script>
        $('#description_complete_randonnee').summernote({
            placeholder: 'Description compète de la randonnée',
            tabsize: 2,
            height: 120,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    </script>
    <script>
        $('#description_sortie').summernote({
            placeholder: 'Description / Détails de la sortie',
            tabsize: 2,
            height: 120,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ---------------------- DÉCLARATION DES ÉLÉMENTS ----------------------
            const calendarEl = document.getElementById('calendar-admin');
            const modalEl = document.getElementById('eventModal');
            const modalTitleEl = modalEl.querySelector('.modal-header h3');
            const gpxDropZone = document.getElementById('gpxDropZone');
            const gpxInput = document.getElementById('modalGpxFile');
            const currentGpxFileEl = document.getElementById('currentGpxFile');
            const gpxStatusContainer = document.getElementById('gpxStatusContainer');
            const removeGpxFileBtn = document.getElementById('removeGpxFile');
            const browseButton = modalEl.querySelector('.btn-browse');

            let currentEventData = {
                id: null,
                existingGpxPath: null,
                isGpxMarkedForDeletion: false
            };
            newGpxFile = null
            currentGpxPath = null

            // ---------------------- FONCTIONS UTILES ----------------------

            function showNotificationAndReload(message, type = 'info', duration = 2000) {
                const toastEl = document.getElementById('notificationToast');
                const messageEl = toastEl.querySelector('.toast-body');

                toastEl.classList.remove('success', 'error', 'info', 'warning');
                toastEl.classList.add(type);

                messageEl.textContent = message;

                // Utilisation directe de Bootstrap 5 Toast pour initialisation et affichage
                const toast = new bootstrap.Toast(toastEl, { delay: duration });
                toast.show();

                // Recharger après la durée
                setTimeout(() => {
                    location.reload();
                }, duration);
            }

            function openModal() {
                modalEl.style.display = 'block';
            }

            function closeModal() {
                modalEl.style.display = 'none';
            }

            function toLocalDatetimeInput(date) {
                if (!date) return '';
                const pad = n => n.toString().padStart(2, '0');
                const d = new Date(date);
                // Formate la date pour un input datetime-local (YYYY-MM-DDTHH:MM)
                return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
            }

            function fromLocalDatetimeInput(inputValue) {
                if (!inputValue) return null;
                const [datePart, timePart] = inputValue.split('T');
                const [year, month, day] = datePart.split('-').map(Number);
                const [hour, minute] = timePart.split(':').map(Number);
                // Crée la date en UTC pour éviter le décalage basé sur le fuseau horaire local
                const date = new Date(Date.UTC(year, month - 1, day, hour, minute));
                return date;
            }

            /**
             * Met à jour l'affichage de l'état du fichier GPX
             * @param {string | null} fileName - Le nom du fichier à afficher (ou null)
             * @param {boolean} isExisting - Vrai si c'est un fichier déjà en base
             */
            function updateGpxStatusDisplay(pathOrName, isExisting = false) {

                // Réinitialisation des drapeaux de l'état
                currentEventData.existingGpxPath = null;
                currentEventData.isGpxMarkedForDeletion = false;
                // Ne pas vider l'input ici, car il pourrait contenir un nouveau fichier non enregistré.
                // Utilisez gpxInput.value = '' uniquement dans resetModal ou removeGpxFileBtn.

                if (pathOrName) {
                    const fileName = pathOrName.split('/').pop();
                    currentGpxFileEl.textContent = `Fichier : ${fileName}`;

                    if (isExisting) {
                        currentEventData.existingGpxPath = pathOrName;
                    }

                    // Un fichier est affiché s'il est existant OU s'il y a un fichier dans l'input
                    const hasNewFileInInput = gpxInput.files.length > 0;

                    gpxStatusContainer.style.backgroundColor = isExisting && !hasNewFileInInput ? '#d4edda' : '#fff3cd';
                    gpxDropZone.style.display = 'none';
                    removeGpxFileBtn.style.display = 'inline-block';

                } else {
                    // Pas de fichier
                    currentGpxFileEl.textContent = 'Aucun fichier GPX sélectionné.';
                    gpxStatusContainer.style.backgroundColor = '#ffffff';
                    removeGpxFileBtn.style.display = 'none';
                    gpxDropZone.style.display = 'block';
                    gpxInput.value = ''; // Vider l'input si l'affichage est vide
                }
            }

            function resetModal() {
                document.getElementById('modalEventId').value = '';
                document.getElementById('modalTitle').value = '';
                $('#modalDescription').summernote('code', '');
                document.getElementById('modalCategorie').value = '';
                document.getElementById('modalStart').value = '';
                document.getElementById('modalEnd').value = '';
                $('#modalCompteRendu').summernote('code', '');

                // Réinitialisation GPX
                updateGpxStatusDisplay(null);
            }

            // ---------------------- GESTION FULLCALENDAR ----------------------

            // Fonction pour gérer les changements (Drag/Resize)
            function handleEventChange(info) {
                const event = info.event;

                // Les changements de FullCalendar n'incluent pas les champs custom (GPX, CR, Description)
                // On utilise les valeurs étendues existantes
                const existingGpxPath = event.extendedProps.gpxFilePath || null;

                // Création du FormData pour l'envoi
                const formData = new FormData();
                formData.append('id', event.id);
                formData.append('title', event.title);
                formData.append('description', event.extendedProps.description || '');
                formData.append('categorieId', event.extendedProps.categorieId || '');
                formData.append('start', event.start.toISOString());
                formData.append('end', event.end ? event.end.toISOString() : '');
                formData.append('compteRendu', event.extendedProps.compteRendu || '');

                // Conserver le chemin GPX existant s'il y en a un
                if (existingGpxPath) {
                    formData.append('existingGpxPath', existingGpxPath);
                }

                fetch('/avva-admin/page/modifier-evenement', {
                    method: 'POST',
                    // Pas besoin de headers: {'Content-Type': 'application/json'} pour FormData
                    body: formData
                })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) {
                            showNotificationAndReload("Erreur lors de la mise à jour par Drag/Resize !", "error")
                        };
                    })
                    .catch(() => {
                        showNotificationAndReload("Erreur de connexion lors de la mise à jour.", "error")
                    });
            }

            // Initialisation du calendrier
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                themeSystem: 'bootstrap5',
                firstDay: 1,
                selectable: true,
                editable: true,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                buttonText: {
                    today: "Aujourd'hui",
                    month: 'Mois',
                    week: 'Semaine',
                    day: 'Jour',
                    list: 'Liste'
                },
                events: <?= $events ?>, // Événements PHP
                eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },

                // AJOUT NOUVEL ÉVÉNEMENT
                select: function (info) {
                    selectedEvent = null;
                    resetModal();
                    modalTitleEl.textContent = "Ajouter un événement";
                    document.getElementById('modalStart').value = toLocalDatetimeInput(info.start);
                    document.getElementById('modalEnd').value = info.end ? toLocalDatetimeInput(info.end) : '';
                    // S'assurer que la zone Compte rendu est cachée
                    document.querySelector('.compte-rendu-group').style.display = 'none';
                    openModal();
                },

                // MODIFICATION EXISTANTE
                eventClick: function (info) {
                    info.jsEvent.preventDefault();
                    selectedEvent = info.event;

                    modalTitleEl.textContent = "Modifier l'événement";
                    document.getElementById('modalEventId').value = selectedEvent.id;
                    document.getElementById('modalTitle').value = selectedEvent.title;
                    $('#modalDescription').summernote('code', selectedEvent.extendedProps.description || '');
                    document.getElementById('modalCategorie').value = selectedEvent.extendedProps.categorieId || '';
                    document.getElementById('modalStart').value = toLocalDatetimeInput(selectedEvent.start);
                    document.getElementById('modalEnd').value = selectedEvent.end ? toLocalDatetimeInput(selectedEvent.end) : '';

                    // Gestion de l'affichage du Compte rendu
                    const now = new Date();
                    const eventEnd = selectedEvent.end ? new Date(selectedEvent.end) : new Date(selectedEvent.start);
                    const compteRenduGroup = document.querySelector('.compte-rendu-group');

                    if (eventEnd < now) {
                        compteRenduGroup.style.display = 'block';
                        $('#modalCompteRendu').summernote('code', selectedEvent.extendedProps.compteRendu || '');
                    } else {
                        compteRenduGroup.style.display = 'none';
                        $('#modalCompteRendu').summernote('code', '');
                    }

                    // Gestion du fichier GPX
                    const gpxFilePath = selectedEvent.extendedProps.gpxFilePath || '';
                    if (gpxFilePath) {
                        const fileName = gpxFilePath.split('/').pop();
                        updateGpxStatusDisplay(gpxFilePath, true); // On passe le chemin complet et on dit qu'il est existant
                    } else {
                        updateGpxStatusDisplay(null);
                    }

                    openModal();
                },

                // DRAG / RESIZE
                eventDrop: handleEventChange,
                eventResize: handleEventChange,

                datesSet: appliquerStyleJoursPasses,

                noEventsText: 'Aucun événement à afficher',

                dayCellClassNames: function (arg) {
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    const cellDate = new Date(arg.date);
                    cellDate.setHours(0, 0, 0, 0);
                    if (cellDate < today) {
                        return ['past-day'];
                    }
                    return [];
                },
            });

            calendar.render();


            // ---------------------- GESTION DU MODAL ET GPX ----------------------
            document.getElementById('closeModal').addEventListener('click', closeModal);
            window.addEventListener('click', (event) => { if (event.target === modalEl) closeModal(); });

            // Gestion du Drag & Drop
            gpxDropZone.addEventListener('click', () => gpxInput.click()); // Clic sur la zone ouvre l'input file
            gpxDropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                gpxDropZone.classList.add('drag-over');
            });

            gpxDropZone.addEventListener('dragleave', () => {
                gpxDropZone.classList.remove('drag-over');
            });

            gpxDropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                gpxDropZone.classList.remove('drag-over');

                const files = e.dataTransfer.files;

                if (files.length && files[0].name.toLowerCase().endsWith('.gpx')) {
                    // Créer un DataTransfer pour assigner le fichier à l'input file
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(files[0]);
                    gpxInput.files = dataTransfer.files;

                    newGpxFile = files[0];
                    updateGpxStatusDisplay(newGpxFile.name, false);
                } else if (files.length) {
                    showNotification("Seuls les fichiers .gpx sont acceptés.", "warning");
                }
            });

            // Changement via l'input file
            gpxInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    newGpxFile = e.target.files[0];
                    updateGpxStatusDisplay(newGpxFile.name, false);
                }
            });

            // Suppression du fichier GPX côté client (pour annuler l'upload ou la suppression)
            removeGpxFileBtn.addEventListener('click', () => {
                updateGpxStatusDisplay(null); // Réinitialise tout
            });


            // ---------------------- SAUVEGARDE (AJOUT / MODIFICATION) ----------------------
            document.getElementById('saveEvent').addEventListener('click', () => {
                const id = document.getElementById('modalEventId').value;
                const title = document.getElementById('modalTitle').value.trim();
                const description = $('#modalDescription').summernote('code');
                const categorieId = document.getElementById('modalCategorie').value;
                const start = fromLocalDatetimeInput(document.getElementById('modalStart').value);
                const end = document.getElementById('modalEnd').value ? fromLocalDatetimeInput(document.getElementById('modalEnd').value) : null;
                const compteRendu = $('#modalCompteRendu').summernote('code');

                if (!title) return showNotification("Le titre est obligatoire !", "warning");

                const formData = new FormData();
                formData.append('id', id);
                formData.append('title', title);
                formData.append('description', description);
                formData.append('categorieId', categorieId);
                formData.append('start', start.toISOString());
                formData.append('end', end ? end.toISOString() : '');
                formData.append('compteRendu', compteRendu);

                // GESTION DU FICHIER GPX
                if (newGpxFile) {
                    // Nouveau fichier sélectionné (prioritaire)
                    formData.append('gpxFile', newGpxFile);
                } else if (id && currentGpxPath && gpxInput.files.length === 0) {
                    // Modification: le fichier existant est conservé (pas de nouveau fichier ni de demande de suppression)
                    formData.append('existingGpxPath', currentGpxPath);
                } else if (id && !currentGpxPath && selectedEvent && selectedEvent.extendedProps.gpxFilePath) {
                    // Modification: L'utilisateur a cliqué sur Supprimer pour un fichier existant 
                    // (currentGpxPath est vide, mais selectedEvent avait un path)
                    formData.append('deleteGpx', 'true');
                }


                const url = id ? '/avva-admin/page/modifier-evenement' : '/avva-admin/page/ajouter-evenement';

                fetch(url, {
                    method: 'POST',
                    body: formData // Envoi du FormData
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showNotificationAndReload("Événement " + (id ? "modifié" : "ajouté") + " avec succès !", "success");
                            closeModal();
                        } else {
                            showNotificationAndReload("Erreur : " + (data.message || 'Problème de communication'), "error");
                        }
                    })
                    .catch(error => {
                        console.error('Erreur Fetch:', error);
                        showNotificationAndReload("Erreur réseau ou serveur.", "error");
                    });
            });

            // ---------------------- SUPPRESSION ----------------------
            document.getElementById('deleteEvent').addEventListener('click', () => {
                if (!selectedEvent) return;
                const confirmModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
                confirmModal.show();
            });

            document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
                if (!selectedEvent) return;

                fetch('/avva-admin/page/supprimer-evenement', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: selectedEvent.id })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            selectedEvent.remove();
                            closeModal();
                            bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal')).hide();
                            showNotificationAndReload("Événement supprimé avec succès !", "success");
                        } else {
                            showNotificationAndReload("Erreur : " + (data.message || ''), "error");
                        }
                    })
                    .catch(() => {
                        showNotificationAndReload("Erreur de connexion lors de la suppression.", "error");
                    });
            });

            // ---------------------- STYLE JOURS PASSÉS ----------------------
            function appliquerStyleJoursPasses() {
                document.querySelectorAll('.fc-daygrid-day.past-day').forEach(day => {
                    if (!day.querySelector('.diagonal-strike')) {
                        const line = document.createElement('div');
                        line.classList.add('diagonal-strike');
                        day.appendChild(line);
                    }
                });
            }

            appliquerStyleJoursPasses()
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#modalDescription').summernote({
                placeholder: 'Description de l\'événement',
                tabsize: 2,
                height: 200, // hauteur en pixels
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#modalCompteRendu').summernote({
                placeholder: 'Compte rendu de l\'événement',
                tabsize: 2,
                height: 200, // hauteur en pixels
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        });
    </script>
    <script>
        document.querySelectorAll('.compte-rendu-card').forEach(card => {
            card.addEventListener('click', function () {
                const title = this.getAttribute('data-title');
                const content = this.getAttribute('data-content');

                const modal = document.getElementById('compteRenduModal');
                modal.querySelector('.modal-title').textContent = title;
                modal.querySelector('.modal-body').innerHTML = content;
            });
        });
    </script>
    <script>
        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');
        const searchForm = document.getElementById('searchForm');
        let debounceTimeout;

        // Fonction pour initialiser la scrollbar personnalisée
        function initializeCustomScrollbar() {
            const container = searchResults.querySelector('ul');
            const thumb = searchResults.querySelector('.custom-thumb');
            const scrollbar = searchResults.querySelector('.custom-scrollbar');

            if (!container || !thumb) return;

            function updateThumb() {
                const containerHeight = container.clientHeight;
                const contentHeight = container.scrollHeight;
                const scrollRatio = container.scrollTop / (contentHeight - containerHeight);
                const thumbHeight = Math.max(containerHeight * (containerHeight / contentHeight), 20); // min 20px
                thumb.style.height = thumbHeight + 'px';
                thumb.style.top = scrollRatio * (containerHeight - thumbHeight) + 'px';
            }

            container.addEventListener('scroll', updateThumb);
            window.addEventListener('resize', updateThumb);
            updateThumb();

            // Drag du thumb
            let isDragging = false, startY, startScroll;

            thumb.onmousedown = (e) => {
                isDragging = true;
                startY = e.clientY;
                startScroll = container.scrollTop;
                document.body.style.userSelect = 'none';
            };

            document.onmousemove = (e) => {
                if (!isDragging) return;
                const delta = e.clientY - startY;
                const containerHeight = container.clientHeight;
                const contentHeight = container.scrollHeight;
                container.scrollTop = startScroll + delta * (contentHeight / containerHeight);
            };

            document.onmouseup = () => {
                isDragging = false;
                document.body.style.userSelect = 'auto';
            };
        }

        // Fonction de recherche AJAX
        function doSearch(query) {
            if (!query) {
                searchResults.style.display = 'none';
                searchResults.innerHTML = '';
                return;
            }

            fetch(`/search?ajax=1&query=${encodeURIComponent(query)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(res => res.json())
                .then(data => {
                    let html = '';

                    if (data.pages.length) {
                        html += '<div style="padding:5px;"><strong>Pages</strong></div>';
                        html += '<ul style="list-style:none;margin:0;padding:0;max-height:200px;overflow-y:auto;">';
                        data.pages.forEach(p => {
                            html += `<li style="padding:5px; cursor:pointer;" onclick="window.location='${p.url}'">${p.title}</li>`;
                        });
                        html += '</ul>';
                    }

                    if (data.events.length) {
                        html += '<div style="padding:5px;"><strong>Comptes rendus</strong></div>';
                        html += '<ul style="list-style:none;margin:0;padding:0;max-height:200px;overflow-y:auto;">';
                        data.events.forEach(e => {
                            html += `<li style="padding:5px; cursor:pointer;" onclick="window.location='${e.url}'">${e.title}</li>`;
                        });
                        html += '</ul>';
                    }

                    if (!html) {
                        html = '<div style="padding:8px;">Aucun résultat</div>';
                    }

                    // Ajouter la scrollbar custom après le contenu
                    html += '<div class="custom-scrollbar"><div class="custom-thumb"></div></div>';

                    searchResults.innerHTML = html;
                    searchResults.style.display = 'block';

                    // Initialiser le scroll personnalisé
                    initializeCustomScrollbar();
                });
        }

        // Recherche en temps réel (debounce)
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimeout);
            const query = this.value.trim();
            debounceTimeout = setTimeout(() => doSearch(query), 300);
        });

        // Recherche sur Entrée
        searchForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const query = searchInput.value.trim();
            doSearch(query);
        });

        // Fermer la liste si clic en dehors
        document.addEventListener('click', function (e) {
            if (!searchResults.contains(e.target) && e.target !== searchInput) {
                searchResults.style.display = 'none';
            }
        });
    </script>
    <script>
        $(document).ready(function () {

            // --- VÉRIFICATION CRITIQUE POUR JQUERY/AJAX ---
            if (typeof $ === 'undefined' || typeof $.ajax !== 'function') {
                alert("ERREUR: jQuery est manquant ou incomplet ('slim' version). L'éditeur ne peut pas fonctionner.");
                return;
            }
            // ----------------------------------------------------

            // Fonction d'affichage de la barre de progression (corrige la rapidité)
            function showProgressBar(percent, isProcessing = false) {
                const progressBar = $('#upload-progress-bar');
                const progressContainer = $('#upload-progress-container');

                // Change le texte à 'Traitement...' après 100% d'upload XHR
                const text = isProcessing ? 'Traitement...' : (percent + '%');

                if (percent > 0) {
                    progressBar.css('width', percent + '%').attr('aria-valuenow', percent).text(text);
                    progressContainer.show();
                }
            }

            // Fonction pour masquer la barre (appelée uniquement après la réponse du serveur)
            function hideProgressBar() {
                const progressBar = $('#upload-progress-bar');
                const progressContainer = $('#upload-progress-container');

                // Délai pour que l'utilisateur voie la fin du processus
                setTimeout(function () {
                    progressContainer.hide();
                    progressBar.css('width', '0%').attr('aria-valuenow', 0).text('0%');
                }, 500);
            }


            // --- 1. Fonction pour l'envoi d'un fichier binaire unique (AVEC BARRE DE PROGRESSION) ---
            function sendFile(file, editorInstance) {
                var data = new FormData();
                data.append("file", file);

                $.ajax({
                    data: data,
                    type: "POST",
                    url: "../../upload-file", // Chemin relatif pour contourner le 404
                    cache: false,
                    contentType: false,
                    processData: false,

                    // Gestion de la progression XHR pour le suivi de l'upload
                    xhr: function () {
                        const xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function (evt) {
                            if (evt.lengthComputable) {
                                const percentComplete = Math.round((evt.loaded / evt.total) * 100);
                                showProgressBar(percentComplete);

                                // Quand l'upload XHR est fini (100%), on passe au statut "Traitement..."
                                if (percentComplete === 100) {
                                    showProgressBar(100, true);
                                }
                            }
                        }, false);
                        return xhr;
                    },

                    beforeSend: function () {
                        showProgressBar(1);
                    },
                    success: function (response) {
                        hideProgressBar(); // Masque la barre UNIQUEMENT après succès du serveur
                        if (response && response.url) {
                            editorInstance.summernote('insertImage', response.url);
                        } else {
                            alert("Erreur: L'URL du fichier n'a pas été retournée par le serveur.");
                        }
                    },
                    error: function (jqXHR) {
                        hideProgressBar(); // Masque la barre en cas d'erreur
                        let errorMessage = "Échec upload binaire.";
                        try {
                            const response = JSON.parse(jqXHR.responseText);
                            errorMessage = response.error || errorMessage;
                        } catch (e) { }
                        alert("L'image locale n'a pas pu être téléchargée. (Statut " + jqXHR.status + ") " + errorMessage);
                    }
                });
            }

            // --- 2. Fonction pour l'envoi d'une URL distante (Sans progression temps réel) ---
            function uploadRemoteImage(url, editorInstance) {
                // Pas de progression temps réel, car le téléchargement est côté serveur
                $.ajax({
                    type: "POST",
                    url: "../../upload-url",
                    data: { imageUrl: url },
                    success: function (response) {
                        if (response && response.url) {
                            editorInstance.summernote('insertImage', response.url);
                        } else {
                            alert("Échec du téléchargement de l'URL distante. Vérifiez les logs PHP.");
                        }
                    },
                    error: function (jqXHR) {
                        alert("Erreur lors du traitement de l'URL distante. (Statut " + jqXHR.status + ")");
                    }
                });
            }

            // --- 3. Fonction pour analyser l'événement de collage (onPaste) ---
            function pasteHandler(e, editorInstance) {
                const clipboardData = (e.originalEvent || e).clipboardData;
                if (!clipboardData) return;

                for (let i = 0; i < clipboardData.items.length; i++) {
                    const item = clipboardData.items[i];

                    if (item.kind === 'string' && item.type === 'text/plain') {
                        item.getAsString(function (text) {
                            if (text.match(/^(http|https):\/\/[^\s$.?#].[^\s]*$/i)) {
                                e.preventDefault();
                                uploadRemoteImage(text, editorInstance);
                                return;
                            }
                        });
                    }
                }
            }


            // --- 4. Initialisation de l'Éditeur (Summernote) ---
            $('#contenu_page').summernote({
                placeholder: 'Contenu de la page',
                tabsize: 2,
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                callbacks: {
                    onImageUpload: function (files) {
                        const editor = $(this);
                        for (let i = 0; i < files.length; i++) {
                            sendFile(files[i], editor);
                        }
                    },

                    onPaste: function (e) {
                        pasteHandler(e, $(this));
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const primaryNav = document.getElementById('primary-nav-scroll');
            const indicatorTop = document.getElementById('scroll-indicator-top');
            const indicatorBottom = document.getElementById('scroll-indicator-bottom');

            if (primaryNav && indicatorTop && indicatorBottom) {

                // Fonction pour vérifier si le scroll est nécessaire ou si on est en bas
                const checkScroll = () => {
                    const scrollHeight = primaryNav.scrollHeight; // Hauteur totale du contenu
                    const clientHeight = primaryNav.clientHeight; // Hauteur visible du conteneur
                    const scrollTop = primaryNav.scrollTop; // Position actuelle de défilement

                    // La zone n'est pas défilable si le contenu est plus court que la zone visible
                    const isScrollable = scrollHeight > clientHeight;

                    // Décalage de sécurité (1 pixel pour éviter les problèmes d'arrondi)
                    const tolerance = 1;

                    if (isScrollable) {
                        // 1. GESTION DE L'INDICATEUR DU BAS (Quand on est en haut)
                        // Afficher l'indicateur BAS si on n'est pas encore en bas
                        const isScrolledToBottom = (scrollHeight - scrollTop) <= (clientHeight + tolerance);
                        if (!isScrolledToBottom) {
                            indicatorBottom.classList.add('show');
                        } else {
                            indicatorBottom.classList.remove('show');
                        }

                        // 2. GESTION DE L'INDICATEUR DU HAUT (Quand on n'est plus en haut)
                        // Afficher l'indicateur HAUT si on a déjà scrollé
                        const isScrolledToTop = scrollTop <= tolerance;
                        if (!isScrolledToTop) {
                            indicatorTop.classList.add('show');
                        } else {
                            indicatorTop.classList.remove('show');
                        }

                    } else {
                        // Si le contenu n'est pas défilable, masquer les deux indicateurs
                        indicatorTop.classList.remove('show');
                        indicatorBottom.classList.remove('show');
                    }
                };

                // Écoute les événements de défilement
                primaryNav.addEventListener('scroll', checkScroll);

                // Vérifie au chargement et au redimensionnement
                setTimeout(checkScroll, 300);
                window.addEventListener('resize', checkScroll);
            }
        });
        document.addEventListener('DOMContentLoaded', function () {
            const navItemsWithSubmenu = document.querySelectorAll('.nav-item .toggle-submenu');

            navItemsWithSubmenu.forEach(link => {
                link.addEventListener('click', function (e) {
                    // Empêche la navigation vers '#'
                    e.preventDefault();

                    const parentItem = this.closest('.nav-item');
                    const subMenu = parentItem.querySelector('.sub-menu');

                    // Toggle la classe 'submenu-open' (qui contrôle l'affichage via CSS)
                    if (subMenu) {
                        parentItem.classList.toggle('submenu-open');
                    }
                });
            });
        });
    </script>
    <script>
        // LOGIQUE DU LOADER (Le code React/TS dans le bundle final appellera probablement hideLoader)
        document.addEventListener('DOMContentLoaded', () => {
            const loader = document.getElementById('global-loader-overlay');

            // Fonction pour masquer le loader
            const hideLoader = () => {
                if (loader) {
                    // Délai pour la transition CSS (400ms)
                    setTimeout(() => {
                        loader.classList.add('hidden');
                    }, 400);
                }
            };

            // Si l'application React prend en charge le masquage, elle appellera cette fonction globalement.
            // Sinon, vous pouvez décommenter la ligne suivante pour un masquage simple au chargement du DOM :
            // hideLoader(); 
        });
    </script>
    <!--  -->
    <script>
/* ==========================================================================
   GESTIONNAIRE PWA UNIFIÉ (AVVA39 vs CARTOGUIDE) - ISOLATION STRICTE
   ========================================================================== */

(() => {
    // 1. Détection stricte de la route actuelle
    const currentPath = window.location.pathname.toLowerCase();
    const isCartoguide = currentPath.includes('/cartoguide');

    // Clé unique par application pour stocker le prompt d'installation
    const promptKey = isCartoguide ? 'cartoguidePwaPrompt' : 'avvaPwaPrompt';

    // Capturer l'événement d'installation natif dédié à la page
    window[promptKey] = null;
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        window[promptKey] = e;
        // Met à jour l'interface si l'événement arrive après le DOM
        updatePlatformUX();
    });

    // Configuration dynamique des identifiants DOM
    const prefix = isCartoguide ? 'cartoguide-pwa-' : 'pwa-';
    const openClass = isCartoguide ? 'cartoguide-is-open' : 'is-open';
    const discretClass = isCartoguide ? 'cartoguide-pwa-discret' : 'pwa-discret';

    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

    // 2. Sélecteur d'éléments DOM dynamique
    const getDOM = () => ({
        modal: document.getElementById(`${prefix}component-modal`),
        btnOpen: document.getElementById(`${prefix}action-open`),
        btnClose: document.getElementById(`${prefix}action-close`),
        btnInstall: document.getElementById(`${prefix}action-install`),
        txtStatus: document.getElementById(`${prefix}text-status`),
        guideIos: document.getElementById(`${prefix}guide-ios`),
        guideGeneric: document.getElementById(`${prefix}guide-generic`),
        txtInstructions: document.getElementById(`${prefix}text-instructions`)
    });

    // 3. Gestion de la modale (Ouverture / Fermeture)
    const toggleModal = (isOpen) => {
        const DOM = getDOM();
        if (!DOM.modal) return;

        if (isOpen) {
            DOM.modal.classList.add(openClass);
            DOM.modal.setAttribute('aria-hidden', 'false');
        } else {
            DOM.modal.classList.remove(openClass);
            DOM.modal.setAttribute('aria-hidden', 'true');
        }
    };

    // 4. Adaptation de l'interface (iOS vs Android / Manuel vs Automatique)
    const updatePlatformUX = () => {
        const DOM = getDOM();

        if (isIOS) {
            if (DOM.txtStatus) {
                DOM.txtStatus.textContent = isCartoguide
                    ? "📱 iOS : Ajouter le Cartoguide à l'écran d'accueil"
                    : "📱 iOS : Ajouter AVVA39 à l'écran d'accueil";
            }
            if (DOM.guideIos) DOM.guideIos.style.display = "block";
            if (DOM.guideGeneric) DOM.guideGeneric.style.display = "none";
        } else {
            if (DOM.txtStatus) {
                DOM.txtStatus.textContent = isCartoguide
                    ? "🗺️ Installation de l'application Cartoguide"
                    : "🚴 Installation de l'application AVVA39";
            }
            if (DOM.guideIos) DOM.guideIos.style.display = "none";
            if (DOM.guideGeneric) DOM.guideGeneric.style.display = "block";

            // Afficher le bouton direct uniquement si le prompt de cette PWA est dispo
            if (window[promptKey] && DOM.btnInstall) {
                DOM.btnInstall.style.display = 'block';
                if (DOM.txtInstructions) DOM.txtInstructions.style.display = 'none';
            }
        }
    };

    // 5. Action au clic sur "Installer"
    const triggerInstall = async () => {
        const activePrompt = window[promptKey];

        if (!activePrompt) {
            // Pas de prompt natif disponible (iOS, Firefox ou déjà installé)
            toggleModal(true);
            return;
        }

        try {
            await activePrompt.prompt();
            const { outcome } = await activePrompt.userChoice;

            if (outcome === 'accepted') {
                toggleModal(false);
                const DOM = getDOM();
                if (DOM.btnOpen) DOM.btnOpen.style.display = 'none';
            }
        } catch (err) {
            console.error(`[PWA ${isCartoguide ? 'Cartoguide' : 'AVVA39'}] Erreur d'installation :`, err);
        } finally {
            window[promptKey] = null;
        }
    };

    // 6. Délégation des événements DOM
    const initEvents = () => {
        document.addEventListener('click', (e) => {
            const btnOpenTarget = e.target.closest(`#${prefix}action-open`);
            if (btnOpenTarget) {
                e.preventDefault();
                e.stopPropagation();
                updatePlatformUX();
                toggleModal(true);
                return;
            }

            const btnCloseTarget = e.target.closest(`#${prefix}action-close`);
            if (btnCloseTarget || e.target.id === `${prefix}component-modal`) {
                e.preventDefault();
                toggleModal(false);
                return;
            }

            const btnInstallTarget = e.target.closest(`#${prefix}action-install`);
            if (btnInstallTarget) {
                e.preventDefault();
                triggerInstall();
                return;
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') toggleModal(false);
        });

        window.addEventListener('appinstalled', () => {
            toggleModal(false);
            const DOM = getDOM();
            if (DOM.btnOpen) DOM.btnOpen.style.display = 'none';
        });

        let lastScrollTop = 0;
        window.addEventListener('scroll', () => {
            const DOM = getDOM();
            if (!DOM.btnOpen) return;

            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                DOM.btnOpen.classList.add(discretClass);
            } else {
                DOM.btnOpen.classList.remove(discretClass);
            }
            lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
        }, { passive: true });
    };

    // 7. Initialisation
    const init = () => {
        initEvents();
        updatePlatformUX();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
    <script>
        (function () {
            var fullUrl = window.location.href.toLowerCase();
            var hasCartoguide = fullUrl.indexOf('cartoguide') !== -1;

            // Détermination du chemin du fichier
            var scriptPath = hasCartoguide
                ? '/assets/js/wpa-cartoguide.js'
                : '/assets/js/wpa-avva39.js';

            console.log('[PWA Loader] URL détectée :', fullUrl);
            console.log('[PWA Loader] Chargement du fichier :', scriptPath);

            var v = Date.now();
            var script = document.createElement('script');
            script.src = scriptPath + '?v=' + v;
            script.defer = true;

            script.onload = function () {
                console.log('[PWA Loader] ✅ Fichier chargé avec succès :', scriptPath);
            };

            script.onerror = function () {
                console.error('[PWA Loader] ❌ Échec de chargement (404/Réseau) sur :', scriptPath);
            };

            (document.head || document.documentElement).appendChild(script);
        })();
    </script>
</body>

</html>