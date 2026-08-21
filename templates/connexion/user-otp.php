<?php
/**
 * Vue de vérification du code (OTP).
 * Variables injectées par LoginController::checkOtp() via $this->render():
 * @var array<string,string> $errors
 */
$errors ??= [];
?>
<div class="star-field"></div>
<div class="glowing-stars"></div>
<div class="container">
    <div class="forms-container">
        <div class="signin-signup">
            <form class="box sign-in-form" action="" method="post" name="login">
                <h2 class="box-title title">VÉRIFICATION</h2>
                <div class="input-field">
                    <i class="fas fa-check"></i>
                    <input type="number" class="box-input" name="otp" placeholder="Saisir le code de vérification"
                        required>
                </div>
                <input type="submit" value="Soumettre" name="check" class="box-button btn solid">

                <?php if (isset($_SESSION['info']) && $_SESSION['info'] !== ''): ?>
                    <div style="color: red; text-align: center;">
                        <?php echo htmlspecialchars($_SESSION['info']); ?>
                    </div>
                <?php endif; ?>

                <?php if (count($errors) > 0): ?>
                    <div style="color: red; text-align: center;">
                        <?php foreach ($errors as $showerror): ?>
                            <?php echo htmlspecialchars($showerror); ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <div class="planet"></div>
    <div class="asteroid"></div>
</div>
</body>

</html>