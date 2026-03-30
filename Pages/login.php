<?php
session_start();
require_once '../db.php';
require_once '../classes/User.php';

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $erreur = "Veuillez remplir tous les champs.";
    } else {
        $user = new User();
        $resultat = $user->connexion($email, $password);

        if ($resultat) {
            $_SESSION['user_id'] = $resultat['id'];
            $_SESSION['user_nom'] = $resultat['nom'];
            $_SESSION['user_prenom'] = $resultat['prenom'];
            header('Location: feed.php');
            exit();
        } else {
            $erreur = "Email ou mot de passe incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>YDate — Connexion</title>
</head>
<body>
    <h1>Se connecter à YDate</h1>

    <?php if ($erreur): ?>
        <p style="color:red"><?= $erreur ?></p>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <input type="email" name="email" placeholder="Email *" required><br>
        <input type="password" name="password" placeholder="Mot de passe *" required><br>
        <button type="submit">Se connecter</button>
    </form>

    <a href="register.php">Pas encore de compte ? S'inscrire</a>
</body>
</html>