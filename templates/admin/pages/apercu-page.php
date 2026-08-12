<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-12 col-lg-12 col-md-12">
            
            <header class="text-center mb-5">
                <h1 class="display-5 fw-bold text-dark">
                    <i class="bi bi-eye-fill text-primary me-2"></i> Aperçu de la page
                </h1>
                <p class="text-secondary lead">Visualisation de la page complète (incluant le header et le footer).</p>
            </header>

            <div class="card shadow-sm border-0 rounded-4 mb-4 bg-light">
                <div class="card-body p-4">
                    <h3 class="mb-3 text-primary"><i class="bi bi-file-earmark-text me-2"></i> Informations : <?= htmlspecialchars($page_preview['nom']) ?></h3>
                    
                    <div class="row gx-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <span class="text-muted d-block fw-semibold mb-1">URL :</span>
                            <code class="text-dark fw-bold">/<?= htmlspecialchars($page_preview['url']) ?></code>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted d-block fw-semibold mb-1">Ordre :</span>
                            <span class="badge bg-info text-dark fs-6 p-2 rounded-pill">
                                #<?= htmlspecialchars($page_preview['ordre_accueil']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <h4 class="mt-5 mb-3 pb-2 border-bottom text-secondary">
                <i class="bi bi-tv me-2"></i> Rendu final
            </h4>

            <div class="content-preview p-2 bg-white border rounded-3 shadow-lg">
                <iframe 
                    src="/page/<?= htmlspecialchars($page_preview['url']) ?>" 
                    style="width:100%; min-height: 800px; border: none; border-radius: 0.5rem;"
                    title="Aperçu en direct de la page <?= htmlspecialchars($page_preview['nom']) ?>">
                </iframe>
            </div>
            
            <div class="text-center mt-5">
                <a href="<?= $_SERVER['HTTP_REFERER'] ?? '/avva-admin/page/liste' ?>"
                   class="btn btn-lg btn-secondary rounded-pill shadow-sm px-5">
                    <i class="bi bi-arrow-left me-2"></i> Retour à la modification
                </a>
            </div>

        </div>
    </div>
</div>