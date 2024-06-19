<?php
function connect()
{
	$mysqli = new mysqli('localhost', 'root', 'Robot500', 'meteastro');
	if ($mysqli->connect_errno != 0) {
		return $mysqli->connect_error;
	} /* else{
																					$mysqli->set_charset("utf8mb4");	
																				} */
	return $mysqli;
}

function getAllProducts()
{
	$mysqli = connect();
	$res = $mysqli->query("SELECT * FROM astronomie, usertable WHERE astronomie.id_users = usertable.id_users AND verified = 'y' ORDER BY astronomie.date_astronomie DESC;");
	while ($row = $res->fetch_assoc()) {
		$products[] = $row;
	}
	return $products;
}

function getProductsByCategory($category)
{
	$mysqli = connect();
	$res = $mysqli->query("SELECT * FROM astronomie WHERE title = '$category'");
	while ($row = $res->fetch_assoc()) {
		$products[] = $row;
	}
	return $products;
}
