<?php
session_start();
session_destroy(); // On casse la session en toute sécurité
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ydate - Déconnexion</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <meta http-equiv="refresh" content="3;url=login.php">
</head>
<body class="landing-body">

    <div class="auth-container" style="text-align: center; padding: 50px 40px;">
        <h1 style="font-size: 40px; margin-bottom: 10px;">À bientôt ! 👋</h1>
        
        <p style="color: var(--text-light); font-size: 16px; margin-bottom: 30px;">
            Tu as bien été déconnecté de Ydate.<br>
            On espère te revoir très vite sur le campus !
        </p>
        
        <p style="font-size: 14px; color: var(--primary-color); font-weight: bold; margin-bottom: 20px;">
            Redirection en cours... ⏳
        </p>

        <a href="login.php" class="btn-submit" style="text-decoration: none; display: inline-block;">
            Retour à l'accueil
        </a>
    </div>

</body>
</html>