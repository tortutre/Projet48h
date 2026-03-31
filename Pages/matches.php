<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once '../db.php';

// Réinitialiser les matches
if (isset($_GET['reset'])) {
    $stmt = $pdo->prepare("DELETE FROM matches WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $stmt2 = $pdo->prepare("DELETE FROM nopes WHERE user_id = ?");
    $stmt2->execute([$_SESSION['user_id']]);
    header('Location: matches.php');
    exit();
}

// Récupérer tous les profils likés
$stmt = $pdo->prepare("
    SELECT users.* FROM users 
    JOIN matches ON users.id = matches.liked_user_id 
    WHERE matches.user_id = ?
    ORDER BY matches.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$profils_likes = $stmt->fetchAll();

$pageTitle = "Ydate - Mes Matches";
include '../includes/header.php';
?>

<?php include '../includes/sidebar.php'; ?>
<main class="main-content">
    <h2>Mes Matches ❤️</h2>

    <a href="matches.php?reset=1" 
       onclick="return confirm('Réinitialiser tous tes matches ?')"
       style="color:red; display:inline-block; margin-bottom:20px;">
       🔄 Réinitialiser mes matches
    </a>

    <?php if (empty($profils_likes)): ?>
        <p>Tu n'as pas encore liké de profil !</p>
    <?php else: ?>
        <div style="display:flex; flex-wrap:wrap; gap:20px;">
            <?php foreach ($profils_likes as $p): ?>
                <div style="border:1px solid #ccc; border-radius:12px; padding:15px; width:200px; text-align:center;">
                    
                    <!-- Photo du profil -->
                    <div style="width:100px; height:100px; border-radius:50%; margin:0 auto 10px; background-image:url('<?= htmlspecialchars($p['avatar'] ?? 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=400') ?>'); background-size:cover; background-position:center;"></div>

                    <strong><?= htmlspecialchars($p['prenom']) ?> <?= htmlspecialchars($p['nom']) ?></strong>
                    <p style="margin:5px 0;"><?= htmlspecialchars($p['promotion']) ?> — <?= htmlspecialchars($p['filiere']) ?></p>
                    <p style="font-size:13px; color:#888;"><?= htmlspecialchars($p['bio'] ?? 'Pas de bio') ?></p>
                    <a href="messagerie.php?user=<?= $p['id'] ?>" style="display:inline-block; margin-top:8px; padding:6px 12px; background:#e91e63; color:white; border-radius:8px; text-decoration:none;">💬 Envoyer un message</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php include '../includes/right-panel.php'; ?>
<?php include '../includes/footer.php'; ?>