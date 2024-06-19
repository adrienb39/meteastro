<?php
require_once '../../config/connexion_bdd.php';

function getAllProducts()
{
	global $conn;
    $products = [];
	$res = $conn->query("SELECT * FROM meteorologie ORDER BY id DESC");
	while ($row = $res->fetch_assoc()) {
		$products[] = $row;
	}
	return $products;
}
