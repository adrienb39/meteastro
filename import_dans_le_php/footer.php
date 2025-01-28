<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Ce site permet d'avoir les informations d'astronomie et de météorologie et de contacter pour avoir des renseignement supplémentaire et bien d'autre">
    <meta name="keywords" content="">
    <title>Meteastro : Astronomie / meteorologie</title>

    <link rel="stylesheet" href="../CSS/style.css" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js">
    </script>

</head>

<body>
    <footer>
        <p class="footer_block">
            Nom du Site Web : <b>Meteastro</b><br>
            Site Web créé par : <b>Adrien Bruyère</b><br>
        </p>
        <p class="footer_block">
            Site Web créé le : <b>Vendredi 8 Avril 2022</b><br>
            Dernière mise à jour du Site Web : <b>Mardi 28 Janvier 2025</b><br>
            Version du Site Web : <b>1.9</b><br>
            <?php include "counter.php"; ?>
        </p>

        <div class="switch" id="switch">
            <span class="switch-light"><img src="/ressources/sun-fill.svg" alt="soleil" style="width: 40px;"></span>
            <span class="switch-dark"><img src="/ressources/moon-fill.svg" alt="lune" style="width: 40px;"></span>
        </div>
    </footer>
    <script src="/import_dans_le_php/footer.js"></script>
</body>

</html>