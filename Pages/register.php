```php
<?php
session_start();
require_once '../db.php';
require_once '../classes/User.php';

// Si l'utilisateur est déjà connecté, on le bloque (il n'a rien à faire sur l'inscription)
if (isset($_SESSION['user_id'])) {
    header('Location: feed.php');
    exit();
}

$erreur = "";
$succes = "";

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $filiere = trim($_POST['filiere']);
    $promotion = trim($_POST['promotion']);

    if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
        $erreur = "Tous les champs obligatoires doivent être remplis.";
    } else {
        $user = new User();
        $resultat = $user->inscription($nom, $prenom, $email, $password, $filiere, $promotion);
        
        if ($resultat) {
            $succes = "Compte créé avec succès ! Tu peux maintenant te connecter.";
        } else {
            $erreur = "Cet email est déjà utilisé (ou une erreur serveur est survenue).";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ydate - Inscription</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="landing-body"> <div class="auth-container" style="width: 500px; padding: 30px;">
        <h1 style="font-size: 28px;">Rejoindre Ydate</h1>
        <p style="margin-bottom: 20px; color: var(--text-light);">Crée ton compte étudiant</p>

        <?php if ($erreur): ?>
            <p style="color: #e94560; background: #ffe0e0; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-weight: bold; font-size: 14px;"><?= $erreur ?></p>
        <?php endif; ?>

        <?php if ($succes): ?>
            <p style="color: #10ac84; background: #e0f2eb; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-weight: bold; font-size: 14px;"><?= $succes ?></p>
        <?php endif; ?>

        <form method="POST" action="register.php">
            
            <div style="display: flex; gap: 10px;">
                <div class="form-group" style="flex: 1;">
                    <label>Prénom *</label>
                    <input type="text" name="prenom" placeholder="Prénom" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Nom *</label>
                    <input type="text" name="nom" placeholder="Nom" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>Email Ynov *</label>
                <input type="email" name="email" placeholder="prenom.nom@ynov.com" required>
            </div>
            
            <div class="form-group">
                <label>Mot de passe *</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>

            <div style="display: flex; gap: 10px;">
                <div class="form-group" style="flex: 2;">
                    <label>Filière</label>
                    <input type="text" name="filiere" placeholder="Ex: Cybersécurité, 3D...">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Promotion</label>
                    <select name="promotion">
                        <option value="">-- Choix --</option>
                        <option value="B1">B1</option>
                        <option value="B2">B2</option>
                        <option value="B3">B3</option>
                        <option value="M1">M1</option>
                        <option value="M2">M2</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn-submit" style="margin-top: 20px;">S'inscrire</button>
        </form>

        <a href="login.php" class="toggle-link">Déjà un compte ? Se connecter</a>
    </div>

</body>
</html>