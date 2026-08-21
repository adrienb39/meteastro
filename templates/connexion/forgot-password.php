<?php
/**
 * Vue de mot de passe oublié - étape 1 (saisie de l'e-mail).
 * Variables injectées par LoginController::forgotPassword() via $this->render():
 * @var array<string,string> $errors
 * @var string               $email
 */
$errors ??= [];
$email ??= '';
?>
<div class="star-field"></div>
<div class="glowing-stars"></div>
<div class="container">
    <div class="forms-container">
        <div class="signin-signup">
            <form class="box sign-in-form" action="" method="post" name="login">
                <h2 class="box-title title">MOT DE PASSE OUBLIÉ</h2>
                <div class="input-field">
                    <i class="fas fa-envelope"></i>
                    <input type="text" class="box-input" name="email" placeholder="Email" required
                        value="<?php echo $email ?>">
                </div>
                <input type="submit" value="Continuer" name="check-email" class="box-button btn solid">
                <?php
                if (count($errors) > 0) {
                    ?>
                    <div style="color: red; text-align: center;">
                        <?php
                        foreach ($errors as $error) {
                            echo $error;
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