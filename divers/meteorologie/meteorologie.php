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
    <!-- Contenu de la page principale pour l'Astronomie -->
    <div class="cards">
        <?php $products = getAllProducts(); ?>
        <?php foreach ($products as $product) { ?>
            <a href="contenu-meteorologie.php?id=<?= $product['id'] ?>" class="card">
                <div class="card-banner">
                    <p class="category-tag popular">
                        <?php echo $product['title'] ?>
                    </p>
                    <img class="banner-img" src="../../uploads/<?php echo $product['filename']; ?>" alt="">
                </div>
                <div class="card-body">
                    <!-- <p class="blog-hashtag">#webdevelopment #frontend</p> -->
                    <h2 class="blog-title">
                        <?php echo $product['title_contenu'] ?>
                    </h2>
                    <p class="blog-description">
                        <?php if ($product["contenu"] > 10) {
                            echo "<p>" . substr(substr($product["contenu"], 0, 100), 0, strrpos(substr($product["contenu"], 0, 100), ' ')) . "... <span style='color: red;' href='contenu-meteorologie.php?id=" . $product['id'] . "'>Lire plus</span></p>";
                        } ?>
                    </p>

                    <div class="card-profile">
                        <!-- <img class="profile-img"
                            src='https://images.unsplash.com/photo-1554780336-390462301acf?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=400&fit=max&ixid=eyJhcHBfaWQiOjE0NTg5fQ'
                            alt=''> -->
                        <div class="card-profile-info">
                            <h3 class="profile-name">
                                Date :
                                <?php echo date("d/m/Y H:i:s", strtotime($product['date_meteorologie'])) ?>
                            </h3>
                            <h3 class="profile-name">
                                Auteur :
                                <?php echo $product['name'] ?>
                            </h3>
                            <!-- <p class="profile-followers">1.2k followers</p> -->
                        </div>
                    </div>
                </div>
            </a>
        <?php } ?>
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