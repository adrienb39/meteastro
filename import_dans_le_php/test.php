<?php
//pour que le script fonctionne, créez le dossier /compteur/ et créez le fichier stats.txt dedans
function compteur($Afficher=0){
 //vous pouvez modifier le chemin ici:
 $CheminStats="compteur.txt";
 /*
 Utilisation:
 echo compteur(1);//affichera le nombre d'affichages aujourd'hui
 echo compteur(2);//affichera le total depuis la création du compteur
 compteur();//comptera l'affichage
 */
 //on vérifie si aujourd'hui il y a eu des visites, si oui on ajoute une visite, si non on ajoute une nouvelle ligne
 $Contenu=file_get_contents($CheminStats);//lecture du fichier stats
 if($Contenu==""){
 if($Afficher==0){
 //si le contenu du fichier est vide, on commence par ajouter une ligne
 file_put_contents($CheminStats,date("d-m-Y").":1\n");//notez le \n qui nous permettra de faire un saut de ligne à chaque nouvelle journée
 } else {
 return "1";//si on veut seulement afficher le nbr d'affichage aujourd'hui
 }
 } else {//sinon on ajoute cette visite
 /* le contenu de ce fichier est de type suivant: "date:nombre d'affichage"
 exemple:
 10-12-15:2548
 11-12-15:4528
 12-12-15:3584
 13-12-15:1253
 */
 //on vérifie donc si il existe une ligne avec la date d'ajourd'hui, pour cela:
 $Lignes=explode("\n",$Contenu);//extraction de chaque ligne dans un tableau (array)
 $NbrDeLigne=count($Lignes);//compte le nombre de ligne, pour pouvoir choisir la dernière
 $DerniereLigne=$Lignes[$NbrDeLigne-2];//-2 parce qu'un tableau commence toujours par 0 et souvenez vous, l'ajout de \n nous rajoute une ligne
 $ExLigne=explode(":",$Lignes[$NbrDeLigne-2]);//va nous permettre de récupérer la date ou le nbr d'affichage
 if($ExLigne[0]==date("d-m-Y")){
 if($Afficher==0){
 //si la date de la dernière ligne est aujourd'hui, on rajoute 1 affichage à cette ligne
 $LigneAmodifier=date("d-m-Y").":".$ExLigne[1];
 $LaRemplacerPar=date("d-m-Y").":".($ExLigne[1]+1);
 $NouveauContenu=str_replace($LigneAmodifier,$LaRemplacerPar,$Contenu);
 file_put_contents($CheminStats,$NouveauContenu,LOCK_EX);
 //la fonction str_replace de php nous serira à prendre la ligne entière d'ajourd'hui puis de la modifier en y rajoutant 1 affichage
 } elseif($Afficher==1){
 return $ExLigne[1];//si on veut seulement afficher le nbr d'affichage aujourd'hui
 } elseif($Afficher==2){
 //on additione toutes les lignes pour retourner le total
 $StatsTotales=0;//on initialise notre variable
 foreach($Lignes as $ligne){
 if($ligne!=""){
 $LigneStat=explode(":",$ligne);
 $StatsTotales+=$LigneStat[1];
 }
 }
 return $StatsTotales;//on retourne le total
 }
 } else {
 if($Afficher==0){
 //si aujourd'hui il y a eu aucun affichage, on lance une nouvelle ligne pour ajourd'hui:
 file_put_contents($CheminStats,date("d-m-Y").":1\n",FILE_APPEND | LOCK_EX);//notez l'utilisation de FILE_APPEND qui servira à placer la nouvelle ligne en bas du fichier sans écraser son contenu, puis LOCK_EX sert à ce que personne d'autre puisse écrire en même temps dans ce fichier
 } elseif($Afficher==1){
 return "1";//si on veut seulement afficher le nbr d'affichage aujourd'hui
 } elseif($Afficher==2){
 //on additione toutes les lignes pour retourner le total
 $StatsTotales=0;//on initialise notre variable
 foreach($Lignes as $ligne){
 if($ligne!=""){
 $LigneStat=explode(":",$ligne);
 $StatsTotales+=$LigneStat[1];
 }
 }
 return $StatsTotales;//on retourne le total
 }
 }
 }
}
compteur();//on compte la visite
echo "Le site comptabilise ".compteur(1)." visite(s) aujourd'hui et ".compteur(2)." au total!";
?>