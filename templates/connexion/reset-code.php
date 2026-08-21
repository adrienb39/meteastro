<?php
/**
 * Vue de mot de passe oublié - étape 2 (vérification du code).
 * Variables injectées par LoginController::checkResetOtp() via $this->render():
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
                <input type="submit" value="Soumettre" name="check-reset-otp" class="box-button btn solid">
                <?php
                if (isset($_SESSION['info'])) {
                    ?>
                    <div style="color: red; text-align: center;">
                        <?php echo $_SESSION['info']; ?>
                    </div>
                    <?php
                }
                ?>
                <?php
                if (count($errors) > 0) {
                    ?>
                    <div style="color: red; text-align: center;">
                        <?php
                        foreach ($errors as $showerror) {
                            echo $showerror;
                        }
                        ?>
                    </div>
                    <?php
                }
                ?>
            </form>
        </div>
    </div>
    <div class="planet"></div>
    <div class="asteroid"></div>