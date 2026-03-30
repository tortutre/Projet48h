<?php
session_start();
require_once '../db.php';
require_once '../classes/User.php';

$erreur = "";
$succes = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $filiere = trim($_POST['filiere']);
    $promotion = trim($_POST['promotion']);

    if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
        $erreur = "Tous les champs obligatoires doivent être remplis.";
    } else {
        $user = new User();
        $resultat = $user->inscription($nom, $prenom, $email, $password, $filiere, $promotion);
        if ($resultat) {
            $succes = "Compte créé avec succès ! Tu peux maintenant te connecter.";
        } else {
            $erreur = "Cet email est déjà utilisé.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>YDate — Inscription</title>
</head>
<body>
    <h1>Créer un compte YDate</h1>

    <?php if ($erreur): ?>
        <p style="color:red"><?= $erreur ?></p>
    <?php endif; ?>

    <?php if ($succes): ?>
        <p style="color:green"><?= $succes ?></p>
    <?php endif; ?>

    <form method="POST" action="register.php">
        <input type="text" name="nom" placeholder="Nom *" required><br>
        <input type="text" name="prenom" placeholder="Prénom *" required><br>
        <input type="email" name="email" placeholder="Email *" required><br>
        <input type="password" name="password" placeholder="Mot de passe *" required><br>
        <input type="text" name="filiere" placeholder="Filière (ex: Cybersécurité)"><br>
        <select name="promotion">
            <option value="">-- Promotion --</option>
            <option value="B1">B1</option>
            <option value="B2">B2</option>
            <option value="B3">B3</option>
        </select><br>
        <button type="submit">S'inscrire</button>
    </form>

    <a href="login.php">Déjà un compte ? Se connecter</a>
</body>
</html>