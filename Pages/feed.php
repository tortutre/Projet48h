<?php
session_start();
// Activation des erreurs pour le développement
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../db.php';
require_once '../classes/Post.php';
require_once '../classes/User.php';

// Protection de la page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$post = new Post();
$user = new User();

// Récupérer les infos de l'utilisateur connecté
$current_user = $user->getProfil($_SESSION['user_id']);
$is_admin = method_exists($user, 'isAdmin') ? $user->isAdmin($_SESSION['user_id']) : false;

// --- ACTIONS (POST/COMMENT/DELETE) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'create' && !empty(trim($_POST['contenu']))) {
        $post->creerPost($_SESSION['user_id'], trim($_POST['contenu']));
        header('Location: feed.php'); exit();
    }
    
    if ($action === 'delete') {
        $post->supprimerPost((int)$_POST['post_id'], $_SESSION['user_id']);
        header('Location: feed.php'); exit();
    }

    if ($action === 'comment' && !empty(trim($_POST['comment_contenu']))) {
        $post_id = (int)$_POST['post_id'];
        $post->ajouterCommentaire($post_id, $_SESSION['user_id'], trim($_POST['comment_contenu']));
        header('Location: feed.php#post-' . $post_id); exit();
    }

    if ($action === 'toggle_pin' && $is_admin) {
        $post_id = (int)$_POST['post_id'];
        if (method_exists($post, 'isPostPinned')) {
            $post->isPostPinned($post_id) ? $post->desepinglerPost($post_id) : $post->epinglerPost($post_id);
        }
        header('Location: feed.php'); exit();
    }
}

// Récupération des posts
try {
    $tousLesPosts = $post->getTousLesPosts();
} catch (Exception $e) {
    $tousLesPosts = [];
    $error_msg = "Erreur base de données : " . $e->getMessage();
}

$pageTitle = "Ysocial - Fil d'actualité";
include '../includes/header.php';
?>

<div class="feed-layout-container">
    
    <?php include '../includes/sidebar.php'; ?>

    <main class="main-content">
        <section class="content-section">
            
            <?php if (isset($error_msg)): ?>
                <div class="box" style="border: 1px solid var(--accent-red); color: var(--accent-red);">
                    ⚠️ <?= $error_msg ?>
                </div>
            <?php endif; ?>

            <div class="box">
                <form method="POST" action="feed.php">
                    <input type="hidden" name="action" value="create">
                    <div class="post-input-container">
                        <div class="post-avatar" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-red) 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; width: 50px; height: 50px; border-radius: 50%; overflow: hidden; flex-shrink: 0;">
                            <?php if (!empty($current_user['photo_profil']) && file_exists('../assets/uploads/' . $current_user['photo_profil'])): ?>
                                <img src="../assets/uploads/<?= htmlspecialchars($current_user['photo_profil']) ?>?v=<?= time() ?>" alt="Photo" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <?= strtoupper(substr($current_user['prenom'] ?? 'U', 0, 1)) . strtoupper(substr($current_user['nom'] ?? '', 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                        <textarea name="contenu" class="post-textarea" rows="3" placeholder="Quoi de neuf sur le campus ? 🤔" required></textarea>
                    </div>
                    <div class="post-actions" style="text-align: right; margin-top: 10px;">
                        <button type="submit" class="btn btn-send">📤 Publier</button>
                    </div>
                </form>
            </div>

            <?php if (empty($tousLesPosts)): ?>
                <div class="box" style="text-align: center; padding: 40px;">
                    <p style="color: var(--text-muted);">Aucun message pour le moment. Soyez le premier ! 🚀</p>
                </div>
            <?php else: ?>
                <?php foreach ($tousLesPosts as $p): ?>
                    <div class="box post-card" id="post-<?= $p['id'] ?>" style="<?= ($p['is_pinned'] ?? false) ? 'border-left: 4px solid #ffc107;' : '' ?>">
                        <div class="post-header" style="display: flex; gap: 15px; margin-bottom: 15px;">
                            <div class="post-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; overflow: hidden; flex-shrink: 0;">
                                <?php if (!empty($p['photo_profil']) && file_exists('../assets/uploads/' . $p['photo_profil'])): ?>
                                    <img src="../assets/uploads/<?= htmlspecialchars($p['photo_profil']) ?>?v=<?= time() ?>" alt="Photo" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <?= strtoupper(substr($p['prenom'], 0, 1)) . strtoupper(substr($p['nom'], 0, 1)) ?>
                                <?php endif; ?>
                            </div>
                            <div style="flex: 1;">
                                <strong style="display: block; color: var(--text-main);"><?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?></strong>
                                <small style="color: var(--text-muted);"><?= htmlspecialchars($p['filiere'] ?? 'Étudiant') ?> • <?= date('d/m H:i', strtotime($p['created_at'])) ?></small>
                            </div>
                            <?php if ($is_admin || $p['user_id'] == $_SESSION['user_id']): ?>
                                <form method="POST" action="feed.php" onsubmit="return confirm('Supprimer ce post ?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="post_id" value="<?= $p['id'] ?>">
                                    <button type="submit" style="background: none; border: none; cursor: pointer;">🗑️</button>
                                </form>
                            <?php endif; ?>
                        </div>
                        <p style="color: var(--text-main); line-height: 1.6; margin-bottom: 15px;"><?= nl2br(htmlspecialchars($p['contenu'])) ?></p>
                        <div class="post-footer" style="display: flex; gap: 20px; border-top: 1px solid var(--border-color); padding-top: 10px;">
                            <button class="action-btn like-btn" data-post-id="<?= $p['id'] ?>">
                                <span class="like-icon">🤍</span>
                                <span class="like-count" style="display: none; margin-left: 5px;">0</span>
                            </button>
                            <button class="action-btn comment-toggle-btn" data-post-id="<?= $p['id'] ?>">💬 <?= method_exists($post, 'getCommentaireCount') ? $post->getCommentaireCount($p['id']) : '0' ?></button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>

    <?php include '../includes/right-panel.php'; ?>

</div>

<style>
/* --- CONFIGURATION PLEIN ÉCRAN --- */
.feed-layout-container {
    display: flex; 
    justify-content: space-between; /* Pousse les colonnes vers les bords de l'écran */
    align-items: flex-start; 
    gap: 20px; 
    width: 100%; /* Prend toute la largeur disponible */
    max-width: 100%; /* Supprime la limite de pixels */
    margin: 20px 0; 
    padding: 0; /* Supprime le padding pour toucher les bords */
}

/* Sidebar Gauche (Collée à gauche) */
.sidebar {
    width: 260px; 
    flex-shrink: 0; 
    position: sticky; 
    top: 20px;
}

/* Flux Central (Au milieu, aéré) */
.main-content {
    flex: 1; 
    max-width: 750px; /* On garde une largeur lisible pour les posts, mais centrée */
    margin: 0 auto; 
    display: flex;
    flex-direction: column;
    gap: 20px; 
}

/* Sidebar Droite (Collée à droite) */
.right-sidebar {
    width: 320px; 
    flex-shrink: 0; 
    position: sticky; 
    top: 20px;
}

/* Style des cartes */
.box {
    background: #151521; 
    border: 1px solid rgba(255, 255, 255, 0.08); 
    border-radius: 18px;
    padding: 20px;
}
</style>

    <script>
        // Toggle commentaires
        document.querySelectorAll('.comment-toggle-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const postId = btn.dataset.postId;
                const form = document.getElementById('comment-form-' + postId);
                const comments = document.getElementById('comments-' + postId);
                
                if (form && comments) {
                    if (form.style.display === 'none') {
                        form.style.display = 'flex';
                        comments.style.display = 'block';
                    } else {
                        form.style.display = 'none';
                        comments.style.display = 'none';
                    }
                }
            });
        });

        // Système de likes cosmétique
        class LikeSystem {
            constructor() {
                this.storageKey = 'ydate_likes';
                this.likes = this.loadLikes();
                this.init();
            }

            loadLikes() {
                const stored = localStorage.getItem(this.storageKey);
                return stored ? JSON.parse(stored) : {};
            }

            saveLikes() {
                localStorage.setItem(this.storageKey, JSON.stringify(this.likes));
            }

            init() {
                document.querySelectorAll('.like-btn').forEach(btn => {
                    const postId = btn.dataset.postId;
                    const likeCount = this.getLikeCount(postId);
                    const isLiked = this.isLiked(postId);
                    
                    this.updateButtonUI(btn, likeCount, isLiked);
                    
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        this.toggleLike(postId, btn);
                    });
                });
            }

            toggleLike(postId, btn) {
                if (this.isLiked(postId)) {
                    this.unlike(postId);
                } else {
                    this.like(postId);
                }
                
                const likeCount = this.getLikeCount(postId);
                const isLiked = this.isLiked(postId);
                this.updateButtonUI(btn, likeCount, isLiked);
            }

            like(postId) {
                if (!this.likes[postId]) {
                    this.likes[postId] = 0;
                }
                this.likes[postId]++;
                this.saveLikes();
            }

            unlike(postId) {
                if (this.likes[postId] && this.likes[postId] > 0) {
                    this.likes[postId]--;
                    if (this.likes[postId] === 0) {
                        delete this.likes[postId];
                    }
                    this.saveLikes();
                }
            }

            isLiked(postId) {
                return this.likes[postId] && this.likes[postId] > 0;
            }

            getLikeCount(postId) {
                return this.likes[postId] || 0;
            }

            updateButtonUI(btn, likeCount, isLiked) {
                const icon = btn.querySelector('.like-icon');
                const count = btn.querySelector('.like-count');
                
                if (isLiked) {
                    icon.textContent = '❤️';
                    btn.style.color = '#ff4b4f';
                } else {
                    icon.textContent = '🤍';
                    btn.style.color = 'var(--text-light)';
                }
                
                if (likeCount > 0) {
                    count.textContent = likeCount;
                    count.style.display = 'inline';
                } else {
                    count.style.display = 'none';
                }
            }
        }

        // Initialiser le système de likes
        new LikeSystem();
    </script>

<?php include '../includes/footer.php'; ?>