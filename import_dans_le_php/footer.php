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
    <link rel='stylesheet' href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css'>

</head>

<body>
    <footer>
        <p class="footer_block">
            Nom du Site Web : <b>Meteastro</b><br>
            Site Web créé par : <b>Adrien Bruyère</b><br>
        </p>
        <p class="footer_block">
            Site Web créé le : <b>Vendredi 8 Avril 2022</b><br>
            Dernière mise à jour du Site Web : <b>Vendredi 5 Avril 2024</b><br>
            Version du Site Web : <b>1.4.2</b><br>
            <?php include "counter.php"; ?>
        </p>

        <button class="switch" id="switch">
            <span class="switch-light">OFF</span>
            <span class="switch-dark">ON</span>
        </button>

        <!-- <div class="mode">
            mode sombre:
            <span class="change">OFF</span>
        </div> -->


    </footer>

    <!-- <script>
        $(".change").on("click", function () {
            if ($("body").hasClass("dark")) {
                $("body").addClass("light");
                $("body").addClass("light");
                $("body").removeClass("dark");
                $("body").removeClass("dark");
                $(".change").text("OFF");
            } else {
                $("body").hasClass("light")
                $("body").removeClass("light");
                $("body").removeClass("light");
                $("body").addClass("dark");
                $("body").addClass("dark");
                $(".change").text("ON");
            }
        });
    </script> -->
    <script src="/import_dans_le_php/footer.js"></script>
</body>

</html>