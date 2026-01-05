<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Ce site permet d'avoir les informations d'astronomie et de météorologie et de contacter pour avoir des renseignement supplémentaire et bien d'autre">
    <meta name="keywords" content="">
    <title>Meteastro : Astronomie / meteorologie</title>

    <link rel="stylesheet" href="../CSS/style.css" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js">
    </script>
    <link rel='stylesheet' href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css'>

</head>

<body>
    <footer class="mt-5 py-5 footer-glass">
        <div class="container">
            <div class="row gy-4 align-items-center">

                <div class="col-md-4 text-center text-md-start">
                    <div class="footer-brand mb-3">
                        <span class="fs-4 fw-bold text-gradient">METEASTRO</span>
                    </div>
                    <p class="text-muted small mb-0">
                        <i class='bx bx-code-alt me-2'></i>Créé par <strong>Adrien Bruyère</strong><br>
                        <i class='bx bx-copyright me-2'></i>Tous droits réservés © 2026
                    </p>
                </div>

                <div class="col-md-4 text-center">
                    <div class="stats-card p-3 rounded-4">
                        <p class="small text-muted mb-2">
                            <i class='bx bx-history me-1'></i> Version 2.0 (Lundi 5 Janvier 2026)<br>
                            <i class='bx bx-calendar me-1'></i> Lancé le 8 Avril 2022
                        </p>
                        <div class="visitor-counter py-1 px-3 bg-primary bg-opacity-10 rounded-pill d-inline-block">
                            <span class="small fw-bold text-primary">
                                <i class='bx bx-group me-1'></i>
                                <?php include "counter.php"; ?> visiteurs
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 text-center text-md-end">
                    <div class="d-flex flex-column align-items-md-end align-items-center gap-3">
                        <div class="darkmode-control" id="switch">
                            <span class="text-muted-extra small">Thème</span>
                            <div class="switch-ui">
                                <i class="fas fa-sun sun-icon"></i>
                                <i class="fas fa-moon moon-icon"></i>
                            </div>
                        </div>

                        <div class="social-links h4 mb-0">
                            <a href="https://github.com/adrienb39/meteastro" class="text-muted me-3" title="GitHub"><i
                                    class='bx bxl-github'></i></a>
                            <a href="/#contacts" class="text-muted" title="Contact"><i class='bx bx-envelope'></i></a>
                        </div>
                    </div>
                </div>

            </div>

            <hr class="my-4 opacity-10">

            <div class="text-center">
                <p class="small text-muted-extra">
                    Station spatiale Meteastro — Astronomie & Météorologie en temps réel.
                </p>
            </div>
        </div>
    </footer>
    <div id="termsModal" class="modal-overlay">
    <div class="modal-content glass-card animate-in">
        <div class="modal-header">
            <h2><i class="fa-solid fa-file-contract"></i> Termes et Conditions</h2>
            <button id="closeTerms" class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
            <h3>1. Acceptation des termes</h3>
            <p>En accédant à ce site, vous acceptez d'être lié par ces termes et conditions et toutes les lois et réglementations applicables.</p>
            
            <h3>2. Utilisation du site</h3>
            <p>Vous pouvez utiliser notre site uniquement à des fins légales et d'une manière qui ne porte pas atteinte aux droits des autres utilisateurs.</p>
            
            <h3>3. Propriété intellectuelle</h3>
            <p>Tous les contenus présents sur ce site (textes, graphiques, logos) sont la propriété de Meteastro.</p>
            
            <h3>4. Limitation de responsabilité</h3>
            <p>Meteastro ne peut être tenu responsable des dommages résultant de l'utilisation de ce site.</p>
            
            <h3>5. Modifications</h3>
            <p>Meteastro se réserve le droit de modifier ces termes à tout moment.</p>
        </div>
        <div class="modal-footer">
            <button id="acceptTermsBtn" class="box-button btn-glow">J'AI COMPRIS</button>
        </div>
    </div>
</div>
    <script src="/import_dans_le_php/footer.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>

</html>