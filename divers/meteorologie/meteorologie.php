<?php
require_once '../../config/connexion_bdd.php';
?>
<?php
$query = "SELECT title FROM meteorologie";
$result = $conn->query($query);
if ($result->num_rows > 0) {
    $options = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>
<?php include "functions.php" ?>
<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meteastro : Astronomie / meteorologie</title>

    <link rel="icon" type="image/png" sizes="16x16" href="/ressources/logo.png">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">

    <link rel="stylesheet" href="/divers/divers.css">
    <link rel="stylesheet" href="../../CSS/style.css">
</head>

<body>
    <?php
    include "../../import_dans_le_php/menu.php";
    ?>
    <!-- Contenu de la page principale pour la Météorologie -->
    <div class="page">
        <div class="left">
            <select name="products" id="products">
                <option value="">Tous sur la Météorologie</option>
                <?php
                foreach ($options as $option) {
                    ?>
                    <option value="<?php echo $option['title']; ?>">
                        <?php echo $option['title']; ?>
                    </option>
                    <?php
                }
                ?>
            </select>
        </div>
        <div class="right">
            <h2>Tous sur la Météorologie</h2>
            <div class="product-wrapper">

                <?php
                $products = getAllProducts();
                foreach ($products as $product) {
                    ?>
                    <div class="product">
                        <div class="left">

                        </div>
                        <div class="right">
                            <p class="title">
                                <?php echo $product['title_contenu'] ?>
                            </p>
                            <p class="description">
                                <?php echo $product['contenu'] ?>
                            </p>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>



    <script src="script.js"></script>

    <?php
    include "../../cookie/cookie.php";
    ?>
    <?php
    include "../../import_dans_le_php/footer.php";
    ?>

    <script src="../app.js"></script>
</body>

</html>