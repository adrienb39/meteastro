<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meteastro : Astronomie / meteorologie</title>

    <link rel="icon" type="image/png" sizes="16x16"  href="/ressources/logo.png">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">

    <link rel="stylesheet" href="/divers/divers.css">
</head>
<body>
    <?php
        include ("../import_dans_le_php/menu.html");
    ?>
    <!-- Contenu de la page principale pour la Météorologie -->
    <div class="container-onglets">
        <div class="onglets active" data-anim="1">test 1</div>
        <div class="onglets" data-anim="2">test 2</div>
        <div class="onglets" data-anim="3">test 3</div>
    </div>
    <div class="container">
        <div class="contenu activeContenu" data-anim="1">
            <h3>Lorem ipsum dolor sit amet. 1</h3>
            <hr>
            <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                 Illo obcaecati deserunt, dolorum qui ab sed natus ea rem provident hic dolorem architecto. De
                 leniti, minima velit!</p>
        </div>
        <div class="contenu" data-anim="2">
            <h3>Lorem ipsum dolor sit amet. 2</h3>
            <hr>
            <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                 Illo obcaecati deserunt, dolorum qui ab sed natus ea rem provident hic dolorem architecto. De
                 leniti, minima velit!</p>
        </div>
        <div class="contenu" data-anim="3">
            <h3>Lorem ipsum dolor sit amet. 3</h3>
            <hr>
            <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                 Illo obcaecati deserunt, dolorum qui ab sed natus ea rem provident hic dolorem architecto. De
                 leniti, minima velit!</p>
        </div>
    </div>
    
    <?php
        include "../cookie/cookie.php"
    ?>
    <?php
    include "../import_dans_le_php/footer.php";
    ?>
    
    <script src="app.js"></script>
</body>
</html>