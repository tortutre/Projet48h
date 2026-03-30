<?php
session_start();
require_once '../db.php';
require_once '../Classe/Admin.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

$admin = new Admin();

// Supprimer un utilisateur
if (isset($_GET['supprimer_user'])) {
    $admin->supprimerUtilisateur((int)$_GET['supprimer_user']);
    header('Location: admin_dashboard.php');
    exit();
}

// Supprimer un post
if (isset($_GET['supprimer_post'])) {
    $admin->supprimerPost((int)$_GET['supprimer_post']);
    header('Location: admin_dashboard.php');
    exit();
}

$utilisateurs = $admin->getTousLesUtilisateurs();
$posts = $admin->getTousLesPosts();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>YDate — Dashboard Admin</title>
</head>
<body>
    <h1>Dashboard Administrateur</h1>
    <a href="admin_logout.php">Se déconnecter</a>

    <hr>

    <h2>Utilisateurs (<?= count($utilisateurs) ?>)</h2>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Filière</th>
            <th>Promotion</th>
            <th>Inscrit le</th>
            <th>Action</th>
        </tr>
        <?php foreach ($utilisateurs as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['nom']) ?></td>
            <td><?= htmlspecialchars($u['prenom']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['filiere']) ?></td>
            <td><?= htmlspecialchars($u['promotion']) ?></td>
            <td><?= $u['created_at'] ?></td>
            <td>
                <a href="admin_dashboard.php?supprimer_user=<?= $u['id'] ?>"
                   onclick="return confirm('Supprimer cet utilisateur ?')">
                   Supprimer
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <hr>

    <h2>Posts (<?= count($posts) ?>)</h2>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Auteur</th>
            <th>Contenu</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
        <?php foreach ($posts as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['prenom']) ?> <?= htmlspecialchars($p['nom']) ?></td>
            <td><?= htmlspecialchars($p['contenu']) ?></td>
            <td><?= $p['created_at'] ?></td>
            <td>
                <a href="admin_dashboard.php?supprimer_post=<?= $p['id'] ?>"
                   onclick="return confirm('Supprimer ce post ?')">
                   Supprimer
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>