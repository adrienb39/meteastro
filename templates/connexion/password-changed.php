<div class="star-field"></div>
<div class="glowing-stars"></div>
<div class="container">
    <div class="forms-container">
        <div class="signin-signup">
            <form class="box sign-in-form" action="" method="post" name="login">
                <h2 class="box-title title">CONNEXION</h2>
                <input type="submit" value="Se connecter maintenant" name="login-now" class="box-button btn solid">

                <?php if (isset($_SESSION['info']) && $_SESSION['info'] !== ''): ?>
                    <div style="color: red; text-align: center;">
                        <?php echo htmlspecialchars($_SESSION['info']); ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <div class="planet"></div>
    <div class="asteroid"></div>