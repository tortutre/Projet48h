<?php
require_once '../db.php';

$email = 'admin@ydate.com';
$password = 'admin123';
// On génère un vrai hash cryptographique valide pour PHP
$hash = password_hash($password, PASSWORD_DEFAULT);

global $pdo;

// On vide la table des admins buggés
$pdo->query("TRUNCATE TABLE admins");

// On insère le bon admin avec le vrai hash
$stmt = $pdo->prepare("INSERT INTO admins (email, password) VALUES (?, ?)");
$stmt->execute([$email, $hash]);

echo "<h1>✅ C'est réparé !</h1>";
echo "<p>L'administrateur a été recréé avec succès.</p>";
echo "<p>Email : <strong>admin@ydate.com</strong></p>";
echo "<p>Mot de passe : <strong>admin123</strong></p>";
echo '<br><a href="admin_login.php">Aller à la page de connexion</a>';
?>