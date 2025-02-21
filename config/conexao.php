<?php
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . '/escola');

$host = "localhost";
$dbname = "escola";
$username = "root";
$password = "";

//$host = "50.116.87.140";
//$dbname = "adtc2m99_ebd";
//$username = "adtc2m99_ebd";
//$password = "Alves1974#";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar: " . $e->getMessage());
}
?>
