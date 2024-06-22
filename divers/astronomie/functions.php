<?php
require_once '../../config/connexion_bdd.php';

function getAllProducts()
{
	global $conn;
    $products = [];

	$res = $conn->query("SELECT * FROM astronomie, usertable WHERE astronomie.id_users = usertable.id_users AND verified = 'y' ORDER BY astronomie.date_astronomie DESC;");
	while ($row = $res->fetch_assoc()) {
		$products[] = $row;
	}
	return $products;
}