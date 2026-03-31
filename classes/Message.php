<?php
class Message {
    
    // 1. Envoyer un message dans la base de données
    public function envoyerMessage($sender_id, $receiver_id, $contenu) {
        global $pdo;
        // On insère le message (la date et l'heure se mettent toutes seules grâce à ta BDD)
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, contenu) VALUES (?, ?, ?)");
        return $stmt->execute([$sender_id, $receiver_id, $contenu]);
    }

    // 2. Récupérer toute la discussion entre deux étudiants pour l'afficher
    public function getConversation($user1, $user2) {
        global $pdo;
        // AJOUT : On fait un JOIN avec users pour récupérer prenom, nom et photo_profil de l'expéditeur
        $stmt = $pdo->prepare("
            SELECT m.*, u.prenom, u.nom, u.photo_profil 
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            WHERE (m.sender_id = ? AND m.receiver_id = ?) 
               OR (m.sender_id = ? AND m.receiver_id = ?)
            ORDER BY m.created_at ASC
        ");
        $stmt->execute([$user1, $user2, $user2, $user1]);
        return $stmt->fetchAll();
    }

    // 3. Marquer une conversation comme lue (pour retirer les notifications plus tard)
    public function marquerCommeLu($sender_id, $receiver_id) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE messages SET lu = 1 WHERE sender_id = ? AND receiver_id = ?");
        return $stmt->execute([$sender_id, $receiver_id]);
    }

    // 4. Récupérer la liste des contacts avec les infos de l'utilisateur
    public function getContacts($user_id) {
        global $pdo;
        // AJOUT : On rajoute u.photo_profil dans le SELECT
        $stmt = $pdo->prepare("
            SELECT DISTINCT u.id, u.nom, u.prenom, u.avatar, u.photo_profil, 
                   (SELECT contenu FROM messages 
                    WHERE (sender_id = ? AND receiver_id = u.id) OR (sender_id = u.id AND receiver_id = ?)
                    ORDER BY created_at DESC LIMIT 1) as last_message,
                   (SELECT created_at FROM messages 
                    WHERE (sender_id = ? AND receiver_id = u.id) OR (sender_id = u.id AND receiver_id = ?)
                    ORDER BY created_at DESC LIMIT 1) as last_message_date
            FROM users u
            WHERE u.id IN (
                SELECT DISTINCT sender_id FROM messages WHERE receiver_id = ?
                UNION
                SELECT DISTINCT receiver_id FROM messages WHERE sender_id = ?
            )
            AND u.id != ?
            ORDER BY last_message_date DESC
        ");
        $stmt->execute([$user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id]);
        return $stmt->fetchAll();
    }

    // 5. Obtenir le nombre de messages non lus d'un utilisateur
    public function getUnreadCount($user_id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM messages WHERE receiver_id = ? AND lu = 0");
        $stmt->execute([$user_id]);
        return $stmt->fetch()['count'];
    }

    // 6. Marquer tous les messages d'un expéditeur comme lus
    public function markAsRead($user_id, $sender_id) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE messages SET lu = 1 WHERE receiver_id = ? AND sender_id = ?");
        return $stmt->execute([$user_id, $sender_id]);
    }
}
?>