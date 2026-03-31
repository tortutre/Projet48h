<?php
session_start();
require_once '../db.php';
require_once '../classes/User.php';
require_once '../includes/avatar_helper.php';

// Protection de la page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user = new User();
$search_results = [];
$search_query = '';
$all_users = [];

// Récupérer tous les utilisateurs au démarrage
global $pdo;
$stmt = $pdo->prepare("
    SELECT id, nom, prenom, filiere, promotion, avatar FROM users 
    WHERE id != ?
    LIMIT 50
");
$stmt->execute([$_SESSION['user_id']]);
$all_users = $stmt->fetchAll();

// Traitement de la recherche
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['q'])) {
    $search_query = trim($_GET['q'] ?? $_POST['search'] ?? '');
    
    if (!empty($search_query)) {
        $stmt = $pdo->prepare("
            SELECT id, nom, prenom, filiere, promotion, avatar FROM users 
            WHERE (nom LIKE ? OR prenom LIKE ?) 
            AND id != ?
            LIMIT 20
        ");
        $search_param = '%' . $search_query . '%';
        $stmt->execute([$search_param, $search_param, $_SESSION['user_id']]);
        $search_results = $stmt->fetchAll();
    }
}

$pageTitle = "Ydate - Trouver un utilisateur";
include '../includes/header.php';
?>

    <?php include '../includes/sidebar.php'; ?>

    <main class="main-content">
    <section class="content-section">
        <h2>Chercher un utilisateur</h2>
        
        <form method="GET" action="search_users.php" class="search-container">
            <input type="text" name="q" value="<?= htmlspecialchars($search_query ?? '') ?>" 
                   placeholder="Rechercher par nom ou prénom..." 
                   class="search-input" 
                   autocomplete="off">
            <button type="submit" class="btn btn-send">Rechercher</button>
        </form>

        <?php if (!empty($search_query)): ?>
            <div class="search-results">
                <?php if (empty($search_results)): ?>
                    <div class="empty-state">
                        <p>Aucun utilisateur trouvé pour "<?= htmlspecialchars($search_query) ?>"</p>
                        <small><a href="search_users.php">Voir tous les utilisateurs</a></small>
                    </div>
                <?php else: ?>
                    <h3>Résultats (<?= count($search_results) ?>)</h3>
                    <div class="users-list">
                        <?php foreach ($search_results as $u): ?>
                            <div class="user-search-card">
                                <div class="user-search-avatar" style="background-image: url('<?= getAvatarPath($u['avatar']) ?>'); background-size: cover; background-position: center; <?= !$u['avatar'] ? 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 18px;' : '' ?>">
                                    <?= !$u['avatar'] ? strtoupper(substr($u['prenom'], 0, 1)) . strtoupper(substr($u['nom'], 0, 1)) : '' ?>
                                </div>
                                <div class="user-search-details">
                                    <strong><?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?></strong>
                                    <small><?= htmlspecialchars($u['filiere'] ?? 'Filière inconnue') ?> - <?= htmlspecialchars($u['promotion'] ?? 'N/A') ?></small>
                                </div>
                                <a href="messagerie.php?user=<?= $u['id'] ?>" class="btn btn-send">Discuter</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div>
                <h3>Tous les utilisateurs (<?= count($all_users ?? []) ?>)</h3>
                <?php if (empty($all_users)): ?>
                    <div class="empty-state box">
                        <strong>Aucun utilisateur pour le moment</strong><br>
                        <small>Revenez plus tard ou créez des utilisateurs de test !</small>
                    </div>
                <?php else: ?>
                    <div class="users-list">
                        <?php foreach ($all_users as $u): ?>
                            <div class="user-search-card">
                                <div class="user-search-avatar" style="background-image: url('<?= getAvatarPath($u['avatar']) ?>'); background-size: cover; background-position: center; <?= !$u['avatar'] ? 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 18px;' : '' ?>">
                                    <?= !$u['avatar'] ? strtoupper(substr($u['prenom'], 0, 1)) . strtoupper(substr($u['nom'], 0, 1)) : '' ?>
                                </div>
                                <div class="user-search-details">
                                    <strong><?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?></strong>
                                    <small><?= htmlspecialchars($u['filiere'] ?? 'Filière inconnue') ?> - <?= htmlspecialchars($u['promotion'] ?? 'N/A') ?></small>
                                </div>
                                <a href="messagerie.php?user=<?= $u['id'] ?>" class="btn btn-send">Discuter</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
