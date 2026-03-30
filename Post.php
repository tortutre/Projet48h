<?php
require_once 'db.php';

class Post {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function creerPost($user_id, $contenu) {
        $stmt = $this->pdo->prepare("INSERT INTO posts (user_id, contenu) VALUES (?, ?)");
        return $stmt->execute([$user_id, $contenu]);
    }

    public function getTousLesPosts() {
        $stmt = $this->pdo->query("SELECT posts.*, users.nom, users.prenom FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC");
        return $stmt->fetchAll();
    }

    public function supprimerPost($id) {
        $stmt = $this->pdo->prepare("DELETE FROM posts WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>