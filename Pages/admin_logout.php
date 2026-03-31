<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>YDate — Admin Déconnexion</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <meta http-equiv="refresh" content="2;url=admin_login.php">
</head>
<body class="admin-login-body">

    <div class="admin-login-box">
        <h1 style="color: var(--admin-danger);">Déconnexion...</h1>
        <p>Fermeture de la session sécurisée.</p>
        <p style="font-size: 13px; color: var(--admin-secondary); margin-top: 20px;">Redirection automatique ⏳</p>
    </div>

</body>
</html>