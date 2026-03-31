<?php
session_start();

// Si l'utilisateur est déjà connecté, on le redirige vers le feed
if (isset($_SESSION['user_id'])) {
    header('Location: pages/feed.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur Ysocial</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="landing-body">

    <div class="hero-container">
        <h1>Ydate</h1>
        <p>Le réseau exclusif pour connecter, échanger et matcher entre étudiants de Paris Ynov Campus.</p>
        
        <a href="pages/login.php" class="btn-home">Se connecter / S'inscrire</a>
    </div>

</body>
</html>