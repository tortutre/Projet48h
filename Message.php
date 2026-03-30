<?php
require_once 'db.php';

class Message {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function envoyerMessage($sender_id, $receiver_id, $contenu) {
        $stmt = $this->pdo->prepare("INSERT INTO messages (sender_id, receiver_id, contenu) VALUES (?, ?, ?)");
        return $stmt->execute([$sender_id, $receiver_id, $contenu]);
    }

    public function getConversation($user1, $user2) {
        $stmt = $this->pdo->prepare("SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY created_at ASC");
        $stmt->execute([$user1, $user2, $user2, $user1]);
        return $stmt->fetchAll();
    }
}
?>