<?php
/**
 * Script d'ajout des commentaires et épinglage
 * À exécuter pour ajouter les tables commentaires et la colonne pinned
 */

require_once 'db.php';

try {
    echo "🔧 Mise à jour de la base de données...\n\n";
    
    // 1. Ajouter la colonne pinned à la table posts
    echo "1. Ajout de la colonne 'pinned' à la table posts...\n";
    try {
        $stmt = $pdo->query("ALTER TABLE posts ADD COLUMN is_pinned TINYINT(1) DEFAULT 0");
        echo "✅ Colonne 'is_pinned' ajoutée\n";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "⚠️  Colonne 'is_pinned' existe déjà\n";
        } else {
            echo "❌ Erreur: " . $e->getMessage() . "\n";
        }
    }
    
    // 2. Créer la table comments
    echo "\n2. Création de la table 'comments'...\n";
    try {
        $sql = "CREATE TABLE IF NOT EXISTS comments (
            id INT(11) NOT NULL AUTO_INCREMENT,
            post_id INT(11) NOT NULL,
            user_id INT(11) NOT NULL,
            contenu TEXT NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY user_id (user_id),
            CONSTRAINT comments_ibfk_1 FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE,
            CONSTRAINT comments_ibfk_2 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        
        $pdo->exec($sql);
        echo "✅ Table 'comments' créée\n";
    } catch (Exception $e) {
        echo "❌ Erreur: " . $e->getMessage() . "\n";
    }
    
    // 3. Ajouter une colonne role à la table users
    echo "\n3. Ajout de la colonne 'role' à la table users...\n";
    try {
        $stmt = $pdo->query("ALTER TABLE users ADD COLUMN role ENUM('user', 'admin') DEFAULT 'user'");
        echo " Colonne 'role' ajoutée\n";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "  Colonne 'role' existe déjà\n";
        } else {
            echo " Erreur: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n✅ Mise à jour terminée avec succès!\n";
    echo "\n📝 Prochaines étapes:\n";
    echo "  1. Vous pouvez maintenant utiliser les commentaires\n";
    echo "  2. Pour épingler un post, vous devez être admin\n";
    echo "  3. Utilisez l'interface pour ajouter des commentaires\n";
    
} catch (Exception $e) {
    echo " Erreur: " . $e->getMessage() . "\n";
}
?>
