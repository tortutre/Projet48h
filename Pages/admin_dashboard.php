<?php
session_start();
require_once '../db.php';
require_once '../classes/Admin.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

$admin = new Admin();

// --- ACTIONS DE SUPPRESSION ---
if (isset($_GET['supprimer_user'])) {
    $admin->supprimerUtilisateur((int)$_GET['supprimer_user']);
    header('Location: admin_dashboard.php');
    exit();
}

if (isset($_GET['supprimer_post'])) {
    $admin->supprimerPost((int)$_GET['supprimer_post']);
    header('Location: admin_dashboard.php');
    exit();
}

if (isset($_GET['supprimer_news'])) {
    $admin->supprimerNewsOuOffre((int)$_GET['supprimer_news']);
    header('Location: admin_dashboard.php');
    exit();
}

// --- ACTION D'AJOUT D'UNE NEWS/OFFRE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_news') {
    $type = $_POST['type']; // 'news' ou 'offer'
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    
    if (!empty($title) && !empty($content)) {
        $admin->ajouterNewsOuOffre($type, $title, $content);
        header('Location: admin_dashboard.php');
        exit();
    }
}

// On récupère toutes les données
$utilisateurs = $admin->getTousLesUtilisateurs();
$posts = $admin->getTousLesPosts();
$newsAndOffers = $admin->getToutesLesNewsEtOffres(); // Nouvelle requête
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>YDate — Dashboard Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

    <div class="admin-layout">
        
        <aside class="admin-sidebar">
            <h2>YDate Admin</h2>
            <a href="admin_dashboard.php">📊 Vue d'ensemble</a>
            <a href="#news">📰 News & Offres</a>
            <a href="#utilisateurs">👥 Utilisateurs</a>
            <a href="#posts">📝 Publications</a>
            <a href="admin_logout.php" class="logout-link">🚪 Déconnexion</a>
        </aside>

        <main class="admin-content">
            
            <div class="admin-header">
                <h1>Tableau de bord</h1>
                <p>Connecté en tant que : <strong><?= htmlspecialchars($_SESSION['admin_email']) ?></strong></p>
            </div>

            <div class="admin-card" id="news">
                <h3>Gestion du Panneau Droit (News & Offres)</h3>
                
                <form method="POST" action="admin_dashboard.php" style="background: rgba(0,0,0,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px; display: flex; gap: 15px; align-items: flex-start;">
                    <input type="hidden" name="action" value="add_news">
                    
                    <select name="type" required style="padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                        <option value="news">📰 Actualité (News)</option>
                        <option value="offer">💼 Offre (Stage/Alt)</option>
                    </select>
                    
                    <input type="text" name="title" placeholder="Titre (ex: 🔥 Challenge 48h)" required style="padding: 10px; border-radius: 5px; border: 1px solid #ccc; flex: 1;">
                    
                    <input type="text" name="content" placeholder="Description courte" required style="padding: 10px; border-radius: 5px; border: 1px solid #ccc; flex: 2;">
                    
                    <button type="submit" style="padding: 10px 20px; background: #8E2DE2; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Ajouter</button>
                </form>

                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Titre</th>
                            <th>Contenu</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($newsAndOffers)): ?>
                            <tr><td colspan="5" style="text-align: center;">Aucun élément affiché dans le panneau droit.</td></tr>
                        <?php else: ?>
                            <?php foreach ($newsAndOffers as $item): ?>
                            <tr>
                                <td>
                                    <?php if($item['type'] == 'news') echo '<span style="background: #3498db; color: white; padding: 3px 8px; border-radius: 10px; font-size: 12px;">News</span>'; ?>
                                    <?php if($item['type'] == 'offer') echo '<span style="background: #e67e22; color: white; padding: 3px 8px; border-radius: 10px; font-size: 12px;">Offre</span>'; ?>
                                </td>
                                <td><strong><?= htmlspecialchars($item['title']) ?></strong></td>
                                <td><?= htmlspecialchars($item['content']) ?></td>
                                <td><?= date('d/m/Y', strtotime($item['created_at'])) ?></td>
                                <td>
                                    <a href="admin_dashboard.php?supprimer_news=<?= $item['id'] ?>" 
                                       class="btn-danger" 
                                       style="color: #e74c3c; text-decoration: none; font-weight: bold;"
                                       onclick="return confirm('Supprimer cet élément du panneau droit ?')">
                                       🗑️ Supprimer
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="admin-card" id="utilisateurs">
                <h3>Gestion des Utilisateurs (<?= count($utilisateurs) ?>)</h3>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom & Prénom</th>
                            <th>Email</th>
                            <th>Filière / Promo</th>
                            <th>Inscription</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($utilisateurs as $u): ?>
                        <tr>
                            <td>#<?= $u['id'] ?></td>
                            <td><strong><?= htmlspecialchars($u['prenom']) ?> <?= htmlspecialchars($u['nom']) ?></strong></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= htmlspecialchars($u['filiere']) ?> (<?= htmlspecialchars($u['promotion']) ?>)</td>
                            <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                            <td>
                                <a href="admin_dashboard.php?supprimer_user=<?= $u['id'] ?>" 
                                   class="btn-danger" 
                                   onclick="return confirm('Attention : Supprimer définitivement cet utilisateur ?')">
                                   Supprimer
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="admin-card" id="posts">
                <h3>Gestion des Publications (<?= count($posts) ?>)</h3>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Auteur</th>
                            <th>Contenu du message</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $p): ?>
                        <tr>
                            <td>#<?= $p['id'] ?></td>
                            <td><strong><?= htmlspecialchars($p['prenom']) ?> <?= htmlspecialchars($p['nom']) ?></strong></td>
                            <td style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?= htmlspecialchars($p['contenu']) ?>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></td>
                            <td>
                                <a href="admin_dashboard.php?supprimer_post=<?= $p['id'] ?>" 
                                   class="btn-danger" 
                                   onclick="return confirm('Supprimer ce post ?')">
                                   Supprimer
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </main>
    </div>

</body>
</html>