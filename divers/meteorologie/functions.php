<?php
require_once '../../config/connexion_bdd.php';

$dbType = 'pdo';

if ($dbType === 'pdo') {
    $db = createPdoConnection();
} else {
    $mysqli = createMysqliConnection();
}

function getAllProducts()
{
	global $conn;
    $products = [];

	$res = $conn->query("SELECT * FROM meteorologie, users WHERE meteorologie.id_users = users.id_users AND verified = 'y' ORDER BY meteorologie.date_meteorologie DESC;");
	while ($row = $res->fetch_assoc()) {
		$products[] = $row;
	}
	return $products;
}