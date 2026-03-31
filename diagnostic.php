<?php
/**
 * Page de vérification et de diagnostic
 * Utile pour déboguer les problèmes de configuration
 */

require_once 'db.php';

echo "<h1 style='font-family: Arial; padding: 20px;'>🔍 Diagnostic du système Ydate</h1>";

echo "<div style='font-family: Arial; padding: 20px; max-width: 800px;'>";

// 1. Vérifier la connexion à la base de données
echo "<h2>1. Connexion à la base de données</h2>";
try {
    $stmt = $pdo->query("SELECT VERSION() as version");
    $result = $stmt->fetch();
    echo "✅ Connecté à: " . $result['version'] . "<br>";
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "<br>";
}

// 2. Vérifier les tables
echo "<h2>2. Tables de la base de données</h2>";
$tables = ['users', 'messages', 'posts', 'news', 'competences', 'interets', 'admins'];
foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
        $result = $stmt->fetch();
        echo "✅ Table '$table': " . $result['count'] . " lignes<br>";
    } catch (Exception $e) {
        echo "❌ Table '$table': Erreur - " . $e->getMessage() . "<br>";
    }
}

// 3. Vérifier les utilisateurs
echo "<h2>3. Utilisateurs</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    $userCount = $result['count'];
    echo "📊 Nombre d'utilisateurs: $userCount<br>";
    
    if ($userCount == 0) {
        echo "<strong>  Aucun utilisateur trouvé!</strong><br>";
    } else {
        $stmt = $pdo->query("SELECT id, nom, prenom, email FROM users LIMIT 5");
        echo "<ul>";
        while ($user = $stmt->fetch()) {
            echo "<li>{$user['prenom']} {$user['nom']} ({$user['email']})</li>";
        }
        echo "</ul>";
    }
} catch (Exception $e) {
    echo " Erreur: " . $e->getMessage() . "<br>";
}

// 4. Vérifier les messages
echo "<h2>4. Messages</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM messages");
    $result = $stmt->fetch();
    echo "📊 Nombre de messages: " . $result['count'] . "<br>";
} catch (Exception $e) {
    echo " Erreur: " . $e->getMessage() . "<br>";
}

// 5. Vérifier les fichiers requis
echo "<h2>5. Fichiers PHP</h2>";
$files = [
    'db.php',
    'classes/User.php',
    'classes/Message.php',
    'pages/messagerie.php',
    'pages/search_users.php',
    'includes/header.php',
    'includes/sidebar.php',
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file existe<br>";
    } else {
        echo "❌ $file manquant<br>";
    }
}

// 6. Résumé
echo "<h2>6. Résumé et actions</h2>";
if ($userCount == 0) {
    echo "<div style='background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px;'>";
    echo "<strong>  Prêt pour tester?</strong><br>";
    echo "Vous devez créer des données de test d'abord.<br>";
    echo "<a href='create_test_data.php' style='display: inline-block; background: #ffc107; padding: 10px 15px; text-decoration: none; border-radius: 3px; color: black; font-weight: bold; margin-top: 10px;'>Créer les données de test</a>";
    echo "</div>";
} else {
    echo "<div style='background: #d4edda; border: 1px solid #28a745; padding: 15px; border-radius: 5px;'>";
    echo "<strong>✅ Système prêt!</strong><br>";
    echo "Vous pouvez maintenant utiliser la messagerie à: <a href='pages/messagerie.php'>pages/messagerie.php</a>";
    echo "</div>";
}

echo "</div>";
?>
