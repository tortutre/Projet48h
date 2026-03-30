<?php
require_once 'db.php';

class User {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function inscription($nom, $prenom, $email, $password, $filiere, $promotion) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare("INSERT INTO users (nom, prenom, email, password, filiere, promotion) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$nom, $prenom, $email, $hash, $filiere, $promotion]);
    }

    public function connexion($email, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function getProfil($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updateBio($id, $bio) {
        $stmt = $this->pdo->prepare("UPDATE users SET bio = ? WHERE id = ?");
        return $stmt->execute([$bio, $id]);
    }
}
?>