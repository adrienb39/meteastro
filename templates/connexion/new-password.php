<?php
/**
 * Vue de mot de passe oublié - étape 3 (nouveau mot de passe).
 * Variables injectées par LoginController::changePassword() via $this->render():
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
                <h2 class="box-title title">MOT DE PASSE</h2>
                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="password" class="box-input" name="password" placeholder="Créer un nouveau mot de passe"
                        required>
                </div>
                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="password" class="box-input" name="cpassword" placeholder="Confirmez votre mot de passe"
                        required>
                </div>
                <input type="submit" value="Changer" name="change-password" class="box-button btn solid">

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