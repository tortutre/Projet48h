<?php
session_start();
require_once '../db.php';
require_once '../Classe/User.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$bio_generee = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $competences = trim($_POST['competences']);
    $interets = trim($_POST['interets']);
    $prenom = $_SESSION['user_prenom'];

    $prompt = "Génère une courte bio professionnelle et sympa pour un étudiant en informatique qui s'appelle $prenom. Ses compétences sont : $competences. Ses centres d'intérêt sont : $interets. La bio doit faire maximum 3 phrases, être en français et donner envie de le contacter.";

    $cle_api = "Clé API";

    $data = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $prompt]
                ]
            ]
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=$cle_api");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    $bio_generee = $result['candidates'][0]['content']['parts'][0]['text'] ?? "Erreur de génération.";

    // Sauvegarder la bio dans le profil
    if ($bio_generee !== "Erreur de génération.") {
        $user = new User();
        $user->updateBio($_SESSION['user_id'], $bio_generee);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>YDate — Générer ma bio</title>
</head>
<body>
    <h1>Générer ma bio avec l'IA</h1>

    <form method="POST" action="gemini.php">
        <textarea name="competences" placeholder="Tes compétences (ex: Python, Cybersécurité, PHP...)" rows="3" cols="50" required></textarea><br><br>
        <textarea name="interets" placeholder="Tes centres d'intérêt (ex: Audiovisuel, Gaming, Music...)" rows="3" cols="50" required></textarea><br><br>
        <button type="submit">Générer ma bio ✨</button>
    </form>

    <?php if ($bio_generee): ?>
        <h2>Ta bio générée :</h2>
        <p style="border:1px solid #ccc; padding:15px; border-radius:8px;">
            <?= nl2br(htmlspecialchars($bio_generee)) ?>
        </p>
        <p style="color:green">Bio sauvegardée dans ton profil !</p>
        <a href="profil.php">Voir mon profil</a>
    <?php endif; ?>

    <br><a href="feed.php">Retour au feed</a>
</body>
</html>
```

---

Tu remplaces `TA_CLE_API` par ta vraie clé, tu sauvegardes et tu testes sur :
```
http://localhost/Projet48h/pages/gemini.php