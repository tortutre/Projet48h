<?php
/**
 * Page de démarrage - Aide à l'utilisation de la messagerie
 */

// Vérifier la session pour montrer le bon message
$logged_in = isset($_SESSION['user_id']);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ydate - Messagerie - Démarrage</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background: var(--light-bg); padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; }
        .card { background: white; border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .step { display: flex; gap: 20px; align-items: flex-start; margin-bottom: 25px; }
        .step-number { background: var(--primary-color); color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0; }
        .step-content h3 { margin: 0 0 10px 0; color: var(--dark-bg); }
        .step-content p { margin: 0 0 5px 0; color: var(--text-dark); line-height: 1.6; }
        .step-content small { color: var(--text-light); }
        .success { background: #d4edda; border: 1px solid #28a745; border-radius: 8px; padding: 15px; margin: 20px 0; color: #155724; }
        .warning { background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 15px; margin: 20px 0; color: #856404; }
        .button-group { display: flex; gap: 10px; margin: 20px 0; flex-wrap: wrap; }
        a.btn-large { display: inline-block; background: var(--primary-color); color: white; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: 600; transition: background 0.3s; }
        a.btn-large:hover { background: #ff4b4f; }
        a.btn-secondary { background: #666; }
        a.btn-secondary:hover { background: #555; }
        .emoji { font-size: 24px; margin-right: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1><span class="emoji">💬</span>Bienvenue dans la Messagerie Ydate</h1>
            <p style="color: var(--text-light); font-size: 16px;">
                Cette guide vous aidera à démarrer avec la messagerie et à communiquer avec vos camarades.
            </p>
        </div>

        <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="warning">
                <strong>⚠️ Vous n'êtes pas connecté</strong><br>
                Vous devez d'abord créer un compte ou vous connecter pour utiliser la messagerie.
            </div>
            <div class="button-group">
                <a href="pages/login.php" class="btn-large">Se connecter</a>
                <a href="pages/register.php" class="btn-large btn-secondary">Créer un compte</a>
            </div>
        <?php else: ?>
            <div class="success">
                ✅ Vous êtes connecté et prêt à utiliser la messagerie !
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>📋 Guide rapide</h2>
            
            <div class="step">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h3>Accéder à la messagerie</h3>
                    <p>Cliquez sur "💬 Messagerie" dans la barre de navigation latérale</p>
                    <small>Disponible uniquement si vous êtes connecté</small>
                </div>
            </div>

            <div class="step">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h3>Voir vos contacts</h3>
                    <p>La liste de gauche affiche tous les utilisateurs avec qui vous avez échangé des messages</p>
                    <small>Cliquez sur un contact pour ouvrir la conversation</small>
                </div>
            </div>

            <div class="step">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h3>Envoyer un message</h3>
                    <p>Tapez votre message dans le champ "Écrire un message..." et appuyez sur "Envoyer"</p>
                    <small>Les messages sont envoyés en temps réel</small>
                </div>
            </div>

            <div class="step">
                <div class="step-number">4</div>
                <div class="step-content">
                    <h3>Trouver de nouveaux contacts</h3>
                    <p>Cliquez sur "+ Nouveau message" pour rechercher et discuter avec d'autres utilisateurs</p>
                    <small>Vous pouvez rechercher par nom ou prénom</small>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>🎯 Cas d'usage courants</h2>
            
            <h3>Je viens de créer un compte, comment commencer?</h3>
            <p>
                1. Allez à la messagerie<br>
                2. Cliquez sur "+ Nouveau message"<br>
                3. Recherchez un ami que vous connaissez<br>
                4. Cliquez sur "Discuter" pour ouvrir une conversation<br>
                5. Envoyez votre premier message! 🚀
            </p>

            <h3>Je n'ai pas de contacts, c'est normal?</h3>
            <p>
                Oui, c'est normal! Les conversations apparaissent quand vous échangez des messages. 
                Utilisez la recherche pour trouver quelqu'un et commencer une conversation.
            </p>

            <h3>Comment savoir si quelqu'un m'a répondu?</h3>
            <p>
                Allez à la messagerie et regardez la liste des contacts. Le dernier message s'affiche en préview.
                Les messages non lus sont marqués automatiquement quand vous ouvrez la conversation.
            </p>

            <h3>Puis-je supprimer une conversation?</h3>
            <p>
                Actuellement, les conversations ne peuvent pas être supprimées. 
                Mais vous pouvez simplement ignorer les contacts que vous ne voulez pas voir.
            </p>
        </div>

        <div class="card">
            <h2>🔧 Configuration du système</h2>
            
            <p><strong>État de la configuration:</strong></p>
            
            <?php
                require_once 'db.php';
                try {
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
                    $result = $stmt->fetch();
                    $userCount = $result['count'];
                    
                    if ($userCount > 0) {
                        echo "<div class='success'>✅ Base de données connectée (" . $userCount . " utilisateurs)</div>";
                    } else {
                        echo "<div class='warning'>⚠️ Aucun utilisateur trouvé<br>Vous devez créer des utilisateurs ou importer les données de test</div>";
                        echo "<a href='create_test_data.php' class='btn-large' style='margin-top: 10px;'>Créer les données de test</a>";
                    }
                } catch (Exception $e) {
                    echo "<div class='warning'>❌ Erreur de connexion: " . $e->getMessage() . "</div>";
                    echo "<a href='diagnostic.php' class='btn-large' style='margin-top: 10px;'>Voir le diagnostic complet</a>";
                }
            ?>
        </div>

        <div class="card">
            <h2>📖 Documentation complète</h2>
            <p>Pour plus de détails, consultez les documents suivants:</p>
            <ul style="line-height: 1.8;">
                <li><a href="MESSAGERIE.md" style="color: var(--primary-color); text-decoration: none;">📘 MESSAGERIE.md</a> - Guide d'utilisation détaillé</li>
                <li><a href="CHANGEMENTS.md" style="color: var(--primary-color); text-decoration: none;">📝 CHANGEMENTS.md</a> - Résumé des améliorations</li>
                <li><a href="diagnostic.php" style="color: var(--primary-color); text-decoration: none;">🔍 diagnostic.php</a> - Vérification de la configuration</li>
            </ul>
        </div>

        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="button-group">
                <a href="pages/messagerie.php" class="btn-large">📨 Aller à la messagerie</a>
                <a href="pages/search_users.php" class="btn-large btn-secondary">🔍 Chercher des utilisateurs</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
