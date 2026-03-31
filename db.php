<?php
$host = "127.0.0.1";
$dbname = "ylink";
$user = "root";
$password = "";

try {
    // VÉRIFIE BIEN QUE C'EST $pdo ICI :
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>