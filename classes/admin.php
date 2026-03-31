<?php
class Admin {
    
    public function connexion($email, $password) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        
        // Vérification standard du hash PHP
        if ($admin && password_verify($password, $admin['password'])) {
            return $admin;
        }
        return false;
    }

    public function getTousLesUtilisateurs() {
        global $pdo;
        return $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
    }

    public function getTousLesPosts() {
        global $pdo;
        return $pdo->query("SELECT posts.*, users.nom, users.prenom FROM posts JOIN users ON posts.user_id = users.id ORDER BY created_at DESC")->fetchAll();
    }

    public function supprimerUtilisateur($id) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function supprimerPost($id) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Gestion du panneau droit
    public function getToutesLesNewsEtOffres() {
        global $pdo;
        return $pdo->query("SELECT * FROM news_offers ORDER BY created_at DESC")->fetchAll();
    }

    public function ajouterNewsOuOffre($type, $title, $content) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO news_offers (type, title, content) VALUES (?, ?, ?)");
        return $stmt->execute([$type, $title, $content]);
    }

    public function supprimerNewsOuOffre($id) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM news_offers WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>