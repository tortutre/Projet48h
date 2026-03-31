<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once '../db.php';

if (isset($_GET['action']) && isset($_GET['profile_id'])) {
    $profile_id = (int)$_GET['profile_id'];
    if ($_GET['action'] === 'like') {
        $stmt = $pdo->prepare("INSERT IGNORE INTO matches (user_id, liked_user_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $profile_id]);
    } else if ($_GET['action'] === 'nope') {
        $stmt = $pdo->prepare("INSERT IGNORE INTO nopes (user_id, noped_user_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $profile_id]);
    }
    header('Location: ydate.php');
    exit();
}

// Récupérer un profil aléatoire (pas soi-même, pas déjà liké, pas déjà nopé)
$stmt = $pdo->prepare("
    SELECT * FROM users 
    WHERE id != ? 
    AND id NOT IN (
        SELECT liked_user_id FROM matches WHERE user_id = ?
    )
    AND id NOT IN (
        SELECT noped_user_id FROM nopes WHERE user_id = ?
    )
    ORDER BY RAND() 
    LIMIT 1
");
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']]);
$profil = $stmt->fetch();

$pageTitle = "Ydate - Rencontrer";
include '../includes/header.php';
?>

<?php include '../includes/sidebar.php'; ?>
<main class="main-content">
    <section class="swipe-section">
        <?php if ($profil): ?>
        <div class="profile-card">
            <div class="profile-pic" style="background-image: url('<?= htmlspecialchars($profil['avatar'] ?? 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=400') ?>');"></div>
            <div class="profile-info">
                <p class="role">Étudiant <?= htmlspecialchars($profil['promotion']) ?> <?= htmlspecialchars($profil['filiere']) ?></p>
                <h2><?= htmlspecialchars($profil['prenom']) ?> <span>22</span></h2>
                <p class="bio-text"><?= htmlspecialchars($profil['bio'] ?? 'Pas de bio pour le moment.') ?></p>
            </div>
        </div>
        <div class="swipe-actions">
            <a href="ydate.php?action=nope&profile_id=<?= $profil['id'] ?>" class="btn-swipe btn-nope" title="Passer">✖</a>
            <a href="ydate.php?action=like&profile_id=<?= $profil['id'] ?>" class="btn-swipe btn-like" title="Match !">♥</a>
        </div>
        <?php else: ?>
            <p style="text-align:center; margin-top:50px;">Plus de profils disponibles pour le moment !</p>
        <?php endif; ?>
    </section>
</main>

<?php include '../includes/right-panel.php'; ?>
<?php include '../includes/footer.php'; ?>