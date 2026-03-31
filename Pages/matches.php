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

$pageTitle = "Ysocial - Mes Matches";
include '../includes/header.php';
?>

<div class="feed-layout-container">
    
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="main-content">
        <section class="content-section" style="text-align: center;">
            <h2 style="color: white; font-size: 24px; margin-bottom: 10px;">Mes Matches ❤️</h2>

            <a href="matches.php?reset=1" 
               onclick="return confirm('Réinitialiser tous tes matches ?')"
               style="color: var(--accent-red); display: inline-block; margin-bottom: 30px; text-decoration: none; font-size: 14px;">
               🔄 Réinitialiser mes matches
            </a>

            <?php if (empty($profils_likes)): ?>
                <div class="box">
                    <p style="color: var(--text-muted);">Tu n'as pas encore liké de profil ! 😢</p>
                </div>
            <?php else: ?>
                <div class="matches-grid">
                    <?php foreach ($profils_likes as $p): ?>
                        <div class="match-card box">
                            
                            <div class="post-avatar" style="background: linear-gradient(135deg, #f02fc2 0%, #6094ea 100%); width: 80px; height: 80px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 24px; margin: 0 auto 15px;">
                                <?php if (!empty($p['photo_profil']) && file_exists('../assets/uploads/' . $p['photo_profil'])): ?>
                                    <img src="../assets/uploads/<?= htmlspecialchars($p['photo_profil']) ?>?v=<?= time() ?>" alt="Photo" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <?= strtoupper(substr($p['prenom'], 0, 1)) . strtoupper(substr($p['nom'], 0, 1)) ?>
                                <?php endif; ?>
                            </div>

                            <strong style="color: white; display: block; font-size: 16px; margin-bottom: 5px;">
                                <?= htmlspecialchars($p['prenom']) ?> <?= htmlspecialchars($p['nom']) ?>
                            </strong>
                            <p style="margin: 0 0 10px 0; color: #aaa; font-size: 13px;">
                                <?= htmlspecialchars($p['promotion']) ?> — <?= htmlspecialchars($p['filiere']) ?>
                            </p>
                            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 15px; height: 40px; overflow: hidden;">
                                <?= htmlspecialchars($p['bio'] ?? 'Pas de bio') ?>
                            </p>
                            <a href="messagerie.php?user=<?= $p['id'] ?>" class="btn btn-send" style="display: block; text-decoration: none; text-align: center; padding: 10px;">
                                💬 Envoyer un message
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <?php include '../includes/right-panel.php'; ?>

</div>

<style>
/* --- CONFIGURATION PLEIN ÉCRAN (Idem que le Feed) --- */
.feed-layout-container {
    display: flex; 
    justify-content: space-between; 
    align-items: flex-start; 
    gap: 20px; 
    width: 100%; 
    max-width: 100%; 
    margin: 20px 0; 
    padding: 0; 
}

.sidebar { width: 260px; flex-shrink: 0; position: sticky; top: 20px; }
.main-content { flex: 1; max-width: 750px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px; }
.right-sidebar { width: 320px; flex-shrink: 0; position: sticky; top: 20px; }

/* Style des cartes */
.box {
    background: #151521; 
    border: 1px solid rgba(255, 255, 255, 0.08); 
    border-radius: 18px;
    padding: 20px;
}

/* Grille spécifique pour les matches */
.matches-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    text-align: center;
}

.match-card {
    transition: transform 0.3s, border-color 0.3s;
}

.match-card:hover {
    transform: translateY(-5px);
    border-color: #f02fc2;
}
</style>

<?php include '../includes/footer.php'; ?>