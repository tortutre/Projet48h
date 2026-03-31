<?php
/**
 * Script de création de données de test
 * À exécuter une seule fois pour peupler la base de données
 */

require_once 'db.php';
require_once 'classes/User.php';
require_once 'classes/Message.php';

try {
    $user = new User();
    $message = new Message();
    
    // Vérifier que les utilisateurs n'existent pas déjà
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users");
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result['count'] > 0) {
        echo "⚠️  Des utilisateurs existent déjà dans la base de données. Script annulé.\n";
        exit();
    }
    
    echo "Création des utilisateurs de test...\n";
    
    // Créer les utilisateurs de test
    $users_data = [
        ['Jean', 'Dupont', 'jean.dupont@ynov.com', 'password123', 'Informatique', '2024'],
        ['Sarah', 'Martin', 'sarah.martin@ynov.com', 'password123', 'Informatique', '2023'],
        ['Lucas', 'Bernard', 'lucas.bernard@ynov.com', 'password123', 'Informatique', '2024'],
        ['Alice', 'Leclerc', 'alice.leclerc@ynov.com', 'password123', 'Marketing', '2023'],
        ['Thomas', 'Moreau', 'thomas.moreau@ynov.com', 'password123', 'Gestion', '2024'],
    ];
    
    $user_ids = [];
    foreach ($users_data as $userData) {
        if ($user->inscription($userData[0], $userData[1], $userData[2], $userData[3], $userData[4], $userData[5])) {
            echo "✅ Utilisateur créé: {$userData[1]} {$userData[0]}\n";
            // Récupérer l'ID du nouvel utilisateur
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$userData[2]]);
            $result = $stmt->fetch();
            $user_ids[$userData[0]] = $result['id'];
        } else {
            echo "❌ Erreur lors de la création de: {$userData[1]} {$userData[0]}\n";
        }
    }
    
    echo "\nCréation des messages de test...\n";
    
    // Créer des messages de test
    $messages_data = [
        [$user_ids['Jean'], $user_ids['Sarah'], 'Salut Sarah ! Comment ça va ?'],
        [$user_ids['Sarah'], $user_ids['Jean'], 'Ça va bien Jean ! Et toi ?'],
        [$user_ids['Sarah'], $user_ids['Jean'], 'On se voit demain ?'],
        [$user_ids['Jean'], $user_ids['Lucas'], 'Coucou Lucas, tu as fini le projet ?'],
        [$user_ids['Lucas'], $user_ids['Jean'], 'Ouais, c\'est bon !'],
        [$user_ids['Alice'], $user_ids['Thomas'], 'Salut Thomas !'],
        [$user_ids['Thomas'], $user_ids['Alice'], 'Salut Alice ! Ça va ?'],
    ];
    
    foreach ($messages_data as $msgData) {
        if ($message->envoyerMessage($msgData[0], $msgData[1], $msgData[2])) {
            echo "✅ Message créé\n";
        } else {
            echo "❌ Erreur lors de la création du message\n";
        }
    }
    
    echo "\n✅ Données de test créées avec succès !\n";
    echo "\n📧 Utilisateurs de test:\n";
    foreach ($users_data as $userData) {
        echo "  - {$userData[1]} {$userData[0]}: {$userData[2]} (mot de passe: {$userData[3]})\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
?>
