<?php
session_start();
require_once '../db.php';
require_once '../classes/User.php'; // Fais bien attention à ce que le dossier s'appelle "classes"

// Si l'utilisateur est déjà connecté, on l'envoie sur le feed
if (isset($_SESSION['user_id'])) {
    header('Location: feed.php');
    exit();
}

$erreur = "";

// Traitement du formulaire de connexion
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
    <title>Ydate - Connexion</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="landing-body">

    <div class="auth-container">
        <h1>Ydate</h1>
        <p style="margin-bottom: 20px; color: var(--text-light);">Le réseau exclusif de Paris Ynov Campus</p>
        
        <?php if ($erreur): ?>
            <p style="color: #e94560; background: #ffe0e0; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-weight: bold; font-size: 14px;">
                <?= $erreur ?>
            </p>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label>Email Ynov</label>
                <input type="email" name="email" placeholder="prenom.nom@ynov.com" required>
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-submit">Se connecter</button>
        </form>
        
        <a href="register.php" class="toggle-link">Pas encore de compte ? S'inscrire</a>
    </div>

</body>
</html>