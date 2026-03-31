<?php
session_start();
require_once '../db.php';
require_once '../classes/Admin.php';

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
    <title>YDate — Admin Connexion</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-login-body">

    <div class="admin-login-box">
        <h1>🔒 Accès Sécurisé</h1>
        
        <?php if ($erreur): ?>
            <p style="color: white; background: #e74c3c; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px;"><?= $erreur ?></p>
        <?php endif; ?>

        <form method="POST" action="admin_login.php">
            <input type="email" name="email" class="admin-input" placeholder="Email Administrateur" required>
            <input type="password" name="password" class="admin-input" placeholder="Mot de passe" required>
            <button type="submit" class="btn-admin">Se connecter</button>
        </form>
    </div>

</body>
</html>