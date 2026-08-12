<div class="modal fade" id="statusSuccessModal" tabindex="-1" role="dialog" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-lg-4">
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                    <circle class="path circle" fill="none" stroke="#198754" stroke-width="6" stroke-miterlimit="10"
                        cx="65.1" cy="65.1" r="62.1" />
                    <polyline class="path check" fill="none" stroke="#198754" stroke-width="6" stroke-linecap="round"
                        stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 " />
                </svg>
                <h4 class="text-success mt-3">Succès !</h4>
                <p class="mt-3">Inscription finalisée et payée avec succès !</p>
                <a class="btn btn-sm mt-3 btn-success" href="/page/randos">Terminer</a>
            </div>
        </div>
    </div>
</div>

<?php if ($paymentSuccess ?? false): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var successModal = new bootstrap.Modal(document.getElementById('statusSuccessModal'));
        successModal.show();
    });
</script>
<?php endif; ?>

<?php if ($error ?? null): ?>
    <div class="container mt-5">
        <div class="alert alert-danger text-center shadow-lg" role="alert">
            <h4 class="alert-heading">Échec du Paiement !</h4>
            <p>Une erreur est survenue lors de la validation du paiement :</p>
            <p class="mb-0 fw-bold"><?= htmlspecialchars($error) ?></p>
            <hr>
            <a href="/page/randos" class="btn btn-danger">Retourner aux randonnées</a>
        </div>
    </div>
<?php endif; ?>