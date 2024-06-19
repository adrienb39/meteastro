<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>

<head>
<title>fichier du compteur - Webinfotuto.free.fr</title>
</head>

<body>

<?
$fnombre = fopen("visit-count.txt","r+"); // Ouverture du fichier en lecture et écriture
$npages = fgets($fnombre,10);               // Récupération du nombre dans le fichier
$npages++;                                  // Incrément de +1
fseek($fnombre,0);                          // Position au début du fichier
fputs($fnombre,$npages);                    // Ecriture du nombre dans le fichier
fclose($fnombre);                           // Fermeture du fichier
//echo"<tr><td><font size='"14"' color='".$color."'>".$tab['choix'].":</font></td>";
print("$npages");                           // Affichage du résultat
?>

</body>
</html>