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
  $id_users = $fetch_info['id'];
  // Requête mysql pour insérer des données
  $sql = "INSERT INTO astronomie (title, title_contenu, contenu, id_users) VALUES (:title,:title_contenu, :contenu, :id_users)";
  $res = $pdo->prepare($sql);
  $exec = $res->execute(array(":title" => $title, ":title_contenu" => $title_contenu, ":contenu" => $contenu, ":id_users" => $id_users));
  // vérifier si la requête d'insertion a réussi
  if ($exec) {
    header("Location: ../index-connect.php?msg=Donnée enregistrée avec succes");
  } else {

  }
}
?>