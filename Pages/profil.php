<?php
session_start();
// J'ai remis l'affichage des erreurs au cas où on doive débugger
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../db.php';
require_once '../classes/User.php';
require_once '../classes/Interet.php';

// 👉 ERREUR CORRIGÉE : J'ai remis ton fichier helper !
require_once '../includes/avatar_helper.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user = new User();
$interet = new Interet();
$erreur_upload = "";

// --- GESTION DE LA NOUVELLE PHOTO DE PROFIL ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo_profil'])) {
    $dossier_cible = "../assets/uploads/"; 
    
    // Créer le dossier s'il n'existe pas
    if (!is_dir($dossier_cible)) {
        mkdir($dossier_cible, 0777, true);
    }
    
    $fichier_nom = basename($_FILES["photo_profil"]["name"]);
    $extension = strtolower(pathinfo($fichier_nom, PATHINFO_EXTENSION));
    $extensions_autorisees = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (in_array($extension, $extensions_autorisees)) {
        $nouveau_nom = "photo_user_" . $_SESSION['user_id'] . "." . $extension;
        $chemin_complet = $dossier_cible . $nouveau_nom;
        
        if (move_uploaded_file($_FILES["photo_profil"]["tmp_name"], $chemin_complet)) {
            $user->updatePhotoProfil($_SESSION['user_id'], $nouveau_nom);
            header("Location: profil.php");
            exit();
        } else {
            $erreur_upload = "Erreur lors de la sauvegarde de l'image.";
        }
    } else {
        $erreur_upload = "Seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.";
    }
}

// --- AUTRES ACTIONS DU PROFIL ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bio'])) {
    $user->updateBio($_SESSION['user_id'], trim($_POST['bio']));
    header('Location: profil.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['interet']) && !empty(trim($_POST['interet']))) {
    $interet->ajouterInteret($_SESSION['user_id'], trim($_POST['interet']));
    header('Location: profil.php');
    exit();
}

if (isset($_GET['delete_interet'])) {
    $interet->supprimerInteret($_SESSION['user_id'], (int)$_GET['delete_interet']);
    header('Location: profil.php');
    exit();
}

// Récupération des données du profil
$profil = $user->getProfil($_SESSION['user_id']);
$interets = $interet->getInterets($_SESSION['user_id']);

// 👉 ERREUR CORRIGÉE : J'ai remis la variable dont ton menu a besoin !
$avatarPath = getAvatarPath($profil['avatar'] ?? '');

$pageTitle = "Ysocial - Mon Profil";
include '../includes/header.php'; 
?>

<style>
/* 👉 NOUVEAU : J'ai ajouté le CSS plein écran comme pour ton Feed */
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
.box { background: #151521; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 18px; padding: 20px; }
</style>

<div class="feed-layout-container">
    
    <?php include '../includes/sidebar.php'; ?>

    <main class="main-content">
        <section class="content-section">
            
            <div class="box">
                <div style="display: flex; gap: 20px; align-items: center;">
                    
                    <div class="post-avatar" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-red) 100%); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 24px; overflow: hidden; flex-shrink: 0;">
    <?php 
    // 1. On force la récupération de la photo directement en base de données pour être sûr à 100%
    global $pdo;
    $stmt_photo = $pdo->prepare("SELECT photo_profil FROM users WHERE id = ?");
    $stmt_photo->execute([$_SESSION['user_id']]);
    $photo_actuelle = $stmt_photo->fetchColumn();

    // 2. On vérifie si la photo existe en BDD ET dans le dossier
    if (!empty($photo_actuelle) && file_exists('../assets/uploads/' . $photo_actuelle)): 
    ?>
        <img src="../assets/uploads/<?= htmlspecialchars($photo_actuelle) ?>?v=<?= time() ?>" alt="Photo de profil" style="width: 100%; height: 100%; object-fit: cover;">
    <?php else: ?>
        <?= strtoupper(substr($profil['prenom'] ?? 'U', 0, 1)) . strtoupper(substr($profil['nom'] ?? '', 0, 1)) ?>
    <?php endif; ?>
</div>
                    
                    <div style="flex: 1;">
                        <h3 style="margin-top: 0; color: white;">Ma Photo de Profil</h3>
                        <?php if(!empty($erreur_upload)) echo "<p style='color:#ff4b4f;'>$erreur_upload</p>"; ?>
                        <form method="POST" action="profil.php" enctype="multipart/form-data" style="display: flex; gap: 10px; align-items: center;">
                            <input type="file" name="photo_profil" accept="image/png, image/jpeg, image/jpg, image/gif" required style="color: var(--text-muted); font-size: 14px;">
                            <button type="submit" class="btn btn-send" style="padding: 8px 15px;">Modifier</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="box">
                <div class="profile-body">
                    <h2 class="profile-name" style="color: white; margin-bottom: 5px;">
                        <?= htmlspecialchars($profil['prenom'] ?? '') ?> <?= htmlspecialchars($profil['nom'] ?? '') ?>
                    </h2>
                    <p class="role profile-role" style="color: var(--text-muted); margin-bottom: 25px;">
                        Étudiant <?= htmlspecialchars($profil['promotion'] ?? '') ?> - <?= htmlspecialchars($profil['filiere'] ?? '') ?>
                    </p>

                    <h3 class="box-title" style="color: white;">À propos de moi</h3>
                    <form method="POST" action="profil.php" class="bio-form">
                        <textarea name="bio" id="bio-textarea" class="post-textarea" rows="3" placeholder="Parle-nous de toi..." style="width: 100%; background: rgba(255,255,255,0.05); color: white; padding: 10px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); margin-bottom: 15px; resize: vertical;"><?= htmlspecialchars($profil['bio'] ?? '') ?></textarea>
                        
                        <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 25px;">
                            <button type="submit" class="btn btn-send" style="padding: 8px 15px;">💾 Sauvegarder</button>
                            <button type="button" id="btn-reformuler" class="btn" style="background: rgba(255,255,255,0.1); color: white; border: none; padding: 8px 15px; border-radius: 20px; cursor: pointer;">✨ Reformuler avec l'IA</button>
                        </div>
                    </form>

                    <h3 class="box-title" style="margin-top: 30px; color: white;">Mes Compétences</h3>
                    <div class="interests" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 15px;">
                        <?php if (empty($interets)): ?>
                            <span style="color: var(--text-muted); font-size: 14px;">Aucune compétence ajoutée</span>
                        <?php else: ?>
                            <?php foreach ($interets as $int): ?>
                                <span style="background: rgba(255,255,255,0.1); color: white; padding: 5px 12px; border-radius: 15px; font-size: 13px; display: flex; align-items: center; gap: 8px;">
                                    #<?= htmlspecialchars($int['nom']) ?> 
                                    <a href="profil.php?delete_interet=<?= $int['id'] ?>" style="color: var(--accent-red); text-decoration: none; font-weight: bold; cursor: pointer;" title="Supprimer">✖</a>
                                </span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <form method="POST" action="profil.php" style="display: flex; gap: 10px; margin-top: 20px;">
                        <input type="text" name="interet" placeholder="Ex: PHP, Design..." required style="flex: 1; background: rgba(255,255,255,0.05); color: white; padding: 10px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
                        <button type="submit" class="btn btn-send" style="padding: 10px 20px;">Ajouter</button>
                    </form>

                </div>
            </div>
        </section>
    </main>

    <?php include '../includes/right-panel.php'; ?>

</div>

<script>
document.getElementById('btn-reformuler').addEventListener('click', async function() {
    const bioTextarea = document.getElementById('bio-textarea');
    const oldBio = bioTextarea.value.trim();
    const btn = this;

    if (!oldBio) {
        alert("Écris d'abord une petite phrase brouillon pour que l'IA puisse travailler !");
        return;
    }

    btn.innerHTML = '⏳ Réflexion...';
    btn.disabled = true;

    try {
        const formData = new FormData();
        formData.append('bio', oldBio);

        const response = await fetch('ajax_ia.php', {
            method: 'POST',
            body: formData
        });

        const newBio = await response.text();
        bioTextarea.value = newBio;
    } catch (error) {
        alert("Oops, un problème avec l'IA !");
    }

    btn.innerHTML = '✨ Reformuler avec l\'IA';
    btn.disabled = false;
});
</script>

<?php include '../includes/footer.php'; ?>