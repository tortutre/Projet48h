<?php
session_start();
require_once '../db.php';
require_once '../Classe/Admin.php';

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $erreur = "Veuillez remplir tous les champs.";
    } else {
        $admin = new Admin();
        $resultat = $admin->connexion($email, $password);
        if ($resultat) {
            $_SESSION['admin_id'] = $resultat['id'];
            $_SESSION['admin_email'] = $resultat['email'];
            header('Location: admin_dashboard.php');
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
    <title>YDate — Admin</title>
</head>
<body>
    <h1>Connexion Administrateur</h1>

    <?php if ($erreur): ?>
        <p style="color:red"><?= $erreur ?></p>
    <?php endif; ?>

    <form method="POST" action="admin_login.php">
        <input type="email" name="email" placeholder="Email admin" required><br>
        <input type="password" name="password" placeholder="Mot de passe" required><br>
        <button type="submit">Se connecter</button>
    </form>
</body>
</html>