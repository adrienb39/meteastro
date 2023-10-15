<?php
session_start();
$host = 'localhost';
$dbname = 'meteastro';
$username = 'root';
$password = 'Robot500';
if (isset($_POST['insert'])) {
  try {
    // se connecter à mysql
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", "$username", "$password");
  } catch (PDOException $exc) {
    echo $exc->getMessage();
    exit();
  }
  // récupérer les valeurs 
  $title = $_POST['title'];
  $title_contenu = $_POST['title_contenu'];
  $contenu = $_POST['contenu'];
  $users = $_SESSION['username'];
  // Requête mysql pour insérer des données
  $sql = "INSERT INTO astronomie (title, title_contenu, contenu, users) VALUES (:title,:title_contenu, :contenu, :users)";
  $res = $pdo->prepare($sql);
  $exec = $res->execute(array(":title" => $title, ":title_contenu" => $title_contenu, ":contenu" => $contenu, ":users" => $users));
  // vérifier si la requête d'insertion a réussi
  if ($exec) {
    echo "Les données ont bien été insérées. Voici ce qui a été ajouter pour l'Astronomie : <li>le titre est : " . $title . "</li>";
    echo "<li>Le titre du contenu est : " . $title_contenu . "</li>";
    echo "<li>Le contenu est : " . $contenu . "</li>";
    echo "<li>Ce contenu a été ajouter par " . $users . "</li>";
    echo "<a style='text-decoration: none; color: red;' href='/index-connect.php'>Retour sur votre espace du site Web</a>";
  } else {
    echo "Échec de l'opération d'insertion";
  }
}
?>