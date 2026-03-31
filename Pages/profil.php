<?php
session_start();
require_once '../db.php';
require_once '../classes/User.php';
require_once '../classes/Interet.php';
require_once '../includes/avatar_helper.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user = new User();
$interet = new Interet();
$erreur_avatar = "";

// --- TRAITEMENT DES FORMULAIRES ---

// 1. Sauvegarder la bio
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bio'])) {
    $user->updateBio($_SESSION['user_id'], trim($_POST['bio']));
    header('Location: profil.php');
    exit();
}

// 2. Ajouter une compétence
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['interet']) && !empty(trim($_POST['interet']))) {
    $interet->ajouterInteret($_SESSION['user_id'], trim($_POST['interet']));
    header('Location: profil.php');
    exit();
}

// 3. Supprimer une compétence
if (isset($_GET['delete_interet'])) {
    $interet->supprimerInteret($_SESSION['user_id'], (int)$_GET['delete_interet']);
    header('Location: profil.php');
    exit();
}

// 4. UPLOAD DE LA PHOTO DE PROFIL
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    // On définit les formats autorisés
    $extensions_autorisees = ['jpg', 'jpeg', 'png', 'gif'];
    $infos_fichier = pathinfo($_FILES['avatar']['name']);
    $extension = strtolower($infos_fichier['extension']);

    // On vérifie que c'est bien une image
    if (in_array($extension, $extensions_autorisees)) {
        // On génère un nom unique (ID étudiant + timestamp) pour éviter les doublons
        $nom_image = $_SESSION['user_id'] . '_' . time() . '.' . $extension;
        $chemin_dossier = '../assets/img/avatars/';
        
        // Magie : Si le dossier n'existe pas, PHP le crée tout seul !
        if (!is_dir($chemin_dossier)) {
            mkdir($chemin_dossier, 0777, true);
        }
        
        $dossier_destination = $chemin_dossier . $nom_image;

        // On déplace le fichier téléchargé vers notre dossier
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dossier_destination)) {
            $user->updateAvatar($_SESSION['user_id'], $nom_image);
            header('Location: profil.php');
            exit();
        } else {
            $erreur_avatar = "Erreur lors de la sauvegarde de l'image.";
        }
    } else {
        $erreur_avatar = "Format non autorisé. Seulement JPG, PNG ou GIF.";
    }
}

// Récupération des données du profil
$profil = $user->getProfil($_SESSION['user_id']);
$interets = $interet->getInterets($_SESSION['user_id']);

// On définit le chemin de l'avatar en utilisant la fonction helper
$avatarPath = getAvatarPath($profil['avatar']);

$pageTitle = "Ydate - Mon Profil";
include '../includes/header.php'; 
?>

    <?php include '../includes/sidebar.php'; ?>

    <main class="main-content">
        <section class="content-section">
            <div class="box no-padding">
                
                <div class="profile-header">
                    <div class="cover-photo"></div>
                    <div class="profile-avatar" style="background-image: url('<?= $avatarPath ?>');"></div>
                    
                    <div class="profile-actions">
                        <form action="profil.php" method="POST" enctype="multipart/form-data" id="form-avatar" style="display:inline;">
                            <input type="file" name="avatar" id="avatar-input" style="display:none;" accept="image/png, image/jpeg, image/jpg, image/gif" onchange="document.getElementById('form-avatar').submit();">
                            <button type="button" class="btn btn-edit" onclick="document.getElementById('avatar-input').click();">📷 Modifier la photo</button>
                        </form>
                    </div>
                </div>

                <div class="profile-body">
                    
                    <?php if ($erreur_avatar): ?>
                        <p style="color: red; font-size: 14px; margin-bottom: 10px;"><?= $erreur_avatar ?></p>
                    <?php endif; ?>

                    <h2 class="profile-name">
                        <?= htmlspecialchars($profil['prenom'] ?? '') ?> <?= htmlspecialchars($profil['nom'] ?? '') ?>
                    </h2>
                    <p class="role profile-role">
                        Étudiant <?= htmlspecialchars($profil['promotion'] ?? '') ?> - <?= htmlspecialchars($profil['filiere'] ?? '') ?>
                    </p>

                    <h3 class="box-title">À propos de moi</h3>
                    
                    <form method="POST" action="profil.php" class="bio-form">
                        <textarea name="bio" id="bio-textarea" class="bio-textarea" rows="3" placeholder="Parle-nous de toi..."><?= htmlspecialchars($profil['bio'] ?? '') ?></textarea>
                        
                        <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 25px;">
                            <button type="submit" class="btn" style="padding: 8px 15px;">💾 Sauvegarder</button>
                            <button type="button" id="btn-reformuler" class="btn btn-ai">✨ Reformuler avec l'IA</button>
                        </div>
                    </form>

                    <h3 class="box-title">Mes Compétences</h3>
                    <div class="interests">
                        <?php if (empty($interets)): ?>
                            <span class="tag">Aucune compétence ajoutée</span>
                        <?php else: ?>
                            <?php foreach ($interets as $int): ?>
                                <span class="tag common">
                                    #<?= htmlspecialchars($int['nom']) ?> 
                                    <a href="profil.php?delete_interet=<?= $int['id'] ?>" style="color: red; text-decoration: none; margin-left: 5px; font-weight: bold;" title="Supprimer">✖</a>
                                </span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <form method="POST" action="profil.php" class="add-skill-form">
                        <input type="text" name="interet" placeholder="Ex: Python, UX Design..." class="skill-input" required>
                        <button type="submit" class="btn btn-add-skill">+</button>
                    </form>

                </div>
            </div>
        </section>
    </main>

    <?php include '../includes/right-panel.php'; ?>

    <script>
    document.getElementById('btn-reformuler').addEventListener('click', async function() {
        const bioTextarea = document.getElementById('bio-textarea');
        const oldBio = bioTextarea.value.trim();
        const btn = this;

        if (!oldBio) {
            alert("Écris d'abord une petite phrase brouillon pour que l'IA puisse travailler !");
            return;
        }

        btn.innerHTML = '⏳ Réflexion en cours...';
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
            alert("Oops, un problème de connexion avec l'IA !");
        }

        btn.innerHTML = '✨ Reformuler avec l\'IA';
        btn.disabled = false;
    });
    </script>

<?php include '../includes/footer.php'; ?>