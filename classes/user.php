<?php
class User {
    
    // 1. Inscription d'un nouvel étudiant
    public function inscription($nom, $prenom, $email, $password, $filiere, $promotion) {
        global $pdo; // 🚨 LA LIGNE MAGIQUE EST ICI 🚨
        
        // On vérifie si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) return false; // L'email est déjà pris

        // On crypte le mot de passe pour la sécurité
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // On insère le nouvel étudiant dans la table "users"
        $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, email, password, filiere, promotion) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$nom, $prenom, $email, $hashed_password, $filiere, $promotion]);
    }

    // 2. Connexion d'un étudiant
    public function connexion($email, $password) {
        global $pdo; // 🚨 LA LIGNE MAGIQUE EST ICI AUSSI 🚨
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    // 3. Récupérer les infos d'un profil
    public function getProfil($id) {
        global $pdo; // 🚨 ET ICI 🚨
        
        $stmt = $pdo->prepare("SELECT id, nom, prenom, email, filiere, promotion, bio, avatar, created_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // 4. Mettre à jour la bio
    public function updateBio($id, $bio) {
        global $pdo; // 🚨 ET LÀ 🚨
        
        $stmt = $pdo->prepare("UPDATE users SET bio = ? WHERE id = ?");
        return $stmt->execute([$bio, $id]);
    }
    
    // Mettre à jour la photo de profil (Avatar)
    public function updateAvatar($id, $nom_fichier) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
        return $stmt->execute([$nom_fichier, $id]);
    }

    // 6. Vérifier si un utilisateur est administrateur
    public function isAdmin($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result && $result['role'] === 'admin';
    }

    public function updatePhotoProfil($userId, $filename) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE users SET photo_profil = ? WHERE id = ?");
        return $stmt->execute([$filename, $userId]);
    }
}
?>