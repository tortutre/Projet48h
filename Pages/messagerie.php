<?php
session_start();
require_once '../db.php';
require_once '../classes/Message.php';
require_once '../classes/User.php';
require_once '../includes/avatar_helper.php';

// Protection de la page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$message = new Message();
$user = new User();

// On récupère l'ID du destinataire dans l'URL (ex: messagerie.php?user=2)
$receiver_id = isset($_GET['user']) ? (int)$_GET['user'] : null;

// Traitement de l'envoi d'un message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $receiver_id) {
    $contenu = trim($_POST['contenu']);
    if (!empty($contenu)) {
        $message->envoyerMessage($_SESSION['user_id'], $receiver_id, $contenu);
        header("Location: messagerie.php?user=" . $receiver_id);
        exit();
    }
}

// Marquer les messages comme lus si une conversation est ouverte
if ($receiver_id) {
    $message->markAsRead($_SESSION['user_id'], $receiver_id);
}

// On charge l'historique si un contact est sélectionné
$conversation = $receiver_id ? $message->getConversation($_SESSION['user_id'], $receiver_id) : [];

// On récupère la liste des contacts
$contacts = $message->getContacts($_SESSION['user_id']);

// On récupère les infos du destinataire
$receiver_info = null;
if ($receiver_id) {
    $receiver_info = $user->getProfil($receiver_id);
}

// --- DÉBUT DE L'AFFICHAGE ---
$pageTitle = "Ydate - Messagerie";
include '../includes/header.php';
?>

    <?php include '../includes/sidebar.php'; ?>

    <main class="main-content">
        <section class="content-section">
            <div class="chat-container">
                
                <div class="contact-list">
                    <div class="contact-list-header">Messages</div>
                    
                    <a href="search_users.php" class="new-message-link">
                        <span>+</span>
                        Nouveau message
                    </a>
                    
                    <?php if (empty($contacts)): ?>
                        <div class="empty-contacts">
                            <p>Aucune conversation pour le moment</p>
                            <small><a href="search_users.php">Commencer une conversation</a></small>
                        </div>
                    <?php else: ?>
                        <?php foreach ($contacts as $contact): ?>
                            <a href="messagerie.php?user=<?= $contact['id'] ?>" class="contact-item <?= ($receiver_id == $contact['id']) ? 'active' : '' ?>">
                                <div class="contact-avatar" style="background-image: url('<?= getAvatarPath($contact['avatar']) ?>'); background-size: cover; background-position: center; <?= !$contact['avatar'] ? 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;' : '' ?>">
                                    <?= !$contact['avatar'] ? strtoupper(substr($contact['prenom'], 0, 1)) . strtoupper(substr($contact['nom'], 0, 1)) : '' ?>
                                </div>
                                <div class="contact-info">
                                    <strong class="contact-name"><?= htmlspecialchars($contact['prenom'] . ' ' . $contact['nom']) ?></strong>
                                    <span class="contact-preview"><?= htmlspecialchars(substr($contact['last_message'] ?? 'Aucun message', 0, 30)) ?>...</span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="chat-area">
                    <?php if ($receiver_id && $receiver_info): ?>
                        <div class="chat-header">
                            <div class="contact-avatar" style="background-image: url('<?= getAvatarPath($receiver_info['avatar']) ?>'); background-size: cover; background-position: center; <?= !$receiver_info['avatar'] ? 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;' : '' ?>">
                                <?= !$receiver_info['avatar'] ? strtoupper(substr($receiver_info['prenom'], 0, 1)) . strtoupper(substr($receiver_info['nom'], 0, 1)) : '' ?>
                            </div>
                            <div class="chat-header-info">
                                <strong><?= htmlspecialchars($receiver_info['prenom'] . ' ' . $receiver_info['nom']) ?></strong>
                                <small><?= htmlspecialchars($receiver_info['filiere'] ?? 'Filière non renseignée') ?></small>
                            </div>
                        </div>
                        
                        <div class="chat-messages">
                            <?php if (empty($conversation)): ?>
                                <p class="empty-messages">Envoyez le premier message !</p>
                            <?php else: ?>
                                <?php foreach ($conversation as $msg): ?>
                                    <?php $isSentByMe = ($msg['sender_id'] == $_SESSION['user_id']); ?>
                                    <div style="display: flex; gap: 10px; margin-bottom: 15px; <?= $isSentByMe ? 'justify-content: flex-end;' : '' ?>">
                                        <?php if (!$isSentByMe): ?>
                                            <?= getAvatarDiv($msg['avatar'], strtoupper(substr($msg['prenom'], 0, 1)) . strtoupper(substr($msg['nom'], 0, 1)), '', 35) ?>
                                        <?php endif; ?>
                                        <div class="bubble <?= $isSentByMe ? 'bubble-sent' : 'bubble-received' ?>">
                                            <?= nl2br(htmlspecialchars($msg['contenu'])) ?>
                                            <small class="message-time">
                                                <?= date('H:i', strtotime($msg['created_at'])) ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <form method="POST" action="messagerie.php?user=<?= $receiver_id ?>" class="chat-input-area">
                            <input type="text" name="contenu" class="chat-input" placeholder="Écrire un message..." required autocomplete="off">
                            <button type="submit" class="btn btn-send">Envoyer</button>
                        </form>
                    <?php else: ?>
                        <div class="chat-placeholder">
                            <p>Sélectionnez une conversation à gauche pour commencer à discuter.</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </section>
    </main>

<?php include '../includes/footer.php'; ?> ```
