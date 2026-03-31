<?php
require_once 'db.php'; // On se connecte à la BDD

$email_admin = "boss@ynov.com";
$mot_de_passe_clair = "Admin123!"; // Le mot de passe que tu taperas
$mot_de_passe_crypte = password_hash($mot_de_passe_clair, PASSWORD_DEFAULT);

try {
    // On insère l'admin dans la table "admins"
    $stmt = $pdo->prepare("INSERT INTO admins (email, password) VALUES (?, ?)");
    $stmt->execute([$email_admin, $mot_de_passe_crypte]);
    echo "<h1>✅ Compte Admin créé avec succès !</h1>";
    echo "<p>Email : <b>admin.admin@ynov.com</b></p>";
    echo "<p>Mot de passe : <b>adminynov123</b></p>";
    echo "<p>Tu peux maintenant supprimer ce fichier 'creer_admin.php' par sécurité.</p>";
} catch (Exception $e) {
    echo "Erreur (peut-être que ce compte existe déjà ?) : " . $e->getMessage();
}
?>