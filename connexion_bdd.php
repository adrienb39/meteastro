<?php
$hostname='localhost';
$username='meteastro';
$password='Paq6VZGKy2ZcsbYz';

try {
$db = new PDO("mysql:host=$hostname;dbname=test;charset=utf8mb4",$username,$password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
}
catch(PDOException $e)
{
echo $e->getMessage();
}