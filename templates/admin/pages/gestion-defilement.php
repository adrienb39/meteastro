<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-11 col-lg-12">

            <header class="mb-5 border-bottom pb-3 d-flex justify-content-between align-items-center">
                <h1 class="display-6 fw-bold text-dark">
                    <i class="fa-solid fa-arrows-left-right text-primary me-2"></i> Gestion du Texte Défilant
                </h1>
            </header>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                    <?= $_SESSION['error_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i>
                    <?= $_SESSION['success_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <div class="card shadow-lg border-0 rounded-4 mb-5 bg-white">
                <div class="card-header bg-primary text-white p-3 rounded-top-4">
                    <h3 class="mb-0 fw-light">
                        <i class="fa-solid fa-pen-to-square me-2"></i>
                        Modifier le message d'accueil
                    </h3>
                </div>

                <div class="card-body p-4 p-md-5">
                    <form method="post" action="">

                        <div class="mb-4">
                            <label for="defilement_texte" class="form-label text-muted">
                                <i class="fa-solid fa-comment-dots me-1"></i> Contenu du bandeau défilant
                            </label>
                            <textarea name="defilement_texte" id="defilement_texte" class="form-control form-control-lg"
                                placeholder="Saisissez le texte qui s'affichera sur la page d'accueil..."
                                style="min-height: 120px;"><?= htmlspecialchars($contenuTexte) ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="couleur_defilement_texte" class="form-label text-muted">
                                    <i class="fa-solid fa-palette me-1"></i> Couleur du texte
                                </label>
                                <input type="color" name="couleur_defilement_texte" id="couleur_defilement_texte"
                                    class="form-control form-control-color w-100" value="<?= $couleurTexte ?>"
                                    title="Choisir la couleur">
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label text-muted">
                                    <i class="fa-solid fa-palette me-1"></i> Fond du texte & Transparence
                                </label>

                                <div class="d-flex align-items-center gap-2">
                                    <input type="color" id="color_picker" class="form-control form-control-color"
                                        value="<?= substr($fondTexte, 0, 7) ?>" title="Choisir la couleur">

                                    <input type="range" id="opacity_picker" class="form-range" min="0" max="1"
                                        step="0.1" value="1" title="Opacité">
                                </div>

                                <input type="hidden" name="fond_defilement_texte" id="final_color"
                                    value="<?= $fondTexte ?>">
                            </div>

                            <script>
                                // Petit script pour combiner la couleur et l'opacité en RGBA
                                const cp = document.getElementById('color_picker');
                                const op = document.getElementById('opacity_picker');
                                const final = document.getElementById('final_color');

                                function updateColor() {
                                    const hex = cp.value;
                                    const r = parseInt(hex.slice(1, 3), 16);
                                    const g = parseInt(hex.slice(3, 5), 16);
                                    const b = parseInt(hex.slice(5, 7), 16);
                                    const a = op.value;

                                    final.value = `rgba(${r}, ${g}, ${b}, ${a})`;
                                }

                                cp.addEventListener('input', updateColor);
                                op.addEventListener('input', updateColor);
                            </script>

                            <div class="col-md-6 mb-4">
                                <label for="taille_defilement_texte" class="form-label text-muted">
                                    <i class="fa-solid fa-text-height me-1"></i> Taille (px)
                                </label>
                                <input type="number" name="taille_defilement_texte" id="taille_defilement_texte"
                                    class="form-control" value="<?= $tailleTexte ?>" min="10" max="100">
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="position_defilement_texte" class="form-label text-muted">
                                    <i class="fa-solid fa-text-height me-1"></i> Position (px)
                                </label>
                                <input type="number" name="position_defilement_texte" id="position_defilement_texte"
                                    class="form-control" value="<?= $positionTexte ?>" min="0" max="1000">
                            </div>
                        </div>

                        <?php if (isset($error) && $error != ""): ?>
                            <div class="alert alert-danger">
                                <i class="fa-solid fa-circle-exclamation me-2"></i> <?= $error; ?>
                            </div>
                        <?php endif; ?>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-lg btn-primary rounded-pill shadow-sm">
                                <i class="fa-solid fa-save me-2"></i> Mettre à jour le texte
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <h2 class="display-6 fw-bold text-dark mt-5 mb-4">
                <i class="fa-solid fa-eye text-secondary me-2"></i> Aperçu du rendu
            </h2>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-body p-0 bg-dark" style="min-height: 100px; display: flex; align-items: center;">
                    <div class="marquee-admin-container">
                        <div class="marquee-admin-text">
                            <span class="bike-icon">🚴</span> <?= htmlspecialchars($contenuTexte) ?>
                        </div>
                    </div>
                    <style>
                        <?php
                        $hauteurDynamique = ($tailleTexte * 0.3) + 20;
                        $marginTopDynamique = -$hauteurDynamique;
                        ?>
                        .marquee-container {
                            position: relative;
                            width: 100%;
                            height:
                                <?= $hauteurDynamique . 'px' ?>
                            ;
                            background:
                                <?= $fondTexte ?>
                            ;
                            margin-top:
                                <?= $marginTopDynamique . 'px' ?>
                            ;
                        }

                        .marquee-text {
                            position: absolute;
                            white-space: nowrap;
                            margin: 0;
                            padding-left: 100%;
                            animation: marquee 15s linear infinite;
                            font-weight: bold;
                            color:
                                <?= $couleurTexte ?>
                            ;
                            font-size:
                                <?= $tailleTexte . 'px' ?>
                            ;
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
                </div>
                <div class="card-footer bg-light text-center small text-muted">
                    Rendu visuel tel qu'il apparaît sur la page d'accueil
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    /* Style de l'aperçu du défilement */
    .marquee-admin-container {
        position: relative;
        width: 100%;
        height: 60px;
        background:
            <?= $fondTexte ?>
        ;
        display: flex;
        align-items: center;
    }

    .marquee-admin-text {
        position: absolute;
        white-space: nowrap;
        margin: 0;
        padding-left: 100%;
        animation: marquee-scroll 15s linear infinite;
        font-weight: bold;
        color:
            <?= $couleurTexte ?>
        ;
        /* On respecte votre charte graphique */
        font-size:
            <?= $tailleTexte . 'px' ?>
        ;
        font-family: sans-serif;
    }

    @keyframes marquee-scroll {
        0% {
            transform: translateX(0);
        }

        100% {
            transform: translateX(-100%);
        }
    }

    /* Pause au survol pour faciliter la lecture si besoin */
    .marquee-admin-container:hover .marquee-admin-text {
        animation-play-state: paused;
    }
</style>

<div class="container mt-4">
    <div class="iframe-responsive-wrapper">
        <iframe src="https://avva39.fr" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>
</div>
<style>
    .iframe-responsive-wrapper {
        position: relative;
        width: 100%;
        background: rgba(255, 255, 255, 0.1);
        /* Fond léger si l'iframe met du temps à charger */
        backdrop-filter: blur(5px);
        border-radius: 15px;
        /* Arrondis pour coller au design */
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        /* Ombre portée réaliste */
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .iframe-responsive-wrapper iframe {
        display: block;
        width: 100%;
        /* Pour garder un ratio 16/9 sur mobile, vous pouvez utiliser aspect-ratio */
        aspect-ratio: 16 / 9;
        height: auto;
        min-height: 300px;
    }
</style>