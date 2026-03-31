<?php
class Post {
    
    public function creerPost($user_id, $contenu) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, contenu) VALUES (?, ?)");
        return $stmt->execute([$user_id, $contenu]);
    }

    public function getTousLesPosts() {
        global $pdo;
        // Jointure avec la table "users" - posts épinglés en premier
        $stmt = $pdo->query("
            SELECT posts.id, posts.contenu, posts.created_at, posts.is_pinned,
                   users.id as user_id, users.nom, users.prenom, users.filiere, users.promotion
            FROM posts 
            JOIN users ON posts.user_id = users.id 
            ORDER BY posts.is_pinned DESC, posts.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public function getPostById($post_id) {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT posts.id, posts.contenu, posts.created_at, posts.is_pinned,
                   users.id as user_id, users.nom, users.prenom, users.filiere, users.promotion
            FROM posts 
            JOIN users ON posts.user_id = users.id 
            WHERE posts.id = ?
        ");
        $stmt->execute([$post_id]);
        return $stmt->fetch();
    }

    public function supprimerPost($post_id, $user_id) {
        global $pdo;
        // Vérifier que l'utilisateur est le propriétaire
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
        return $stmt->execute([$post_id, $user_id]);
    }

    public function modifierPost($post_id, $contenu, $user_id) {
        global $pdo;
        // Vérifier que l'utilisateur est le propriétaire
        $stmt = $pdo->prepare("UPDATE posts SET contenu = ? WHERE id = ? AND user_id = ?");
        return $stmt->execute([$contenu, $post_id, $user_id]);
    }

    public function getPostsByUser($user_id) {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT posts.id, posts.contenu, posts.created_at, posts.is_pinned,
                   users.id as user_id, users.nom, users.prenom, users.filiere, users.promotion
            FROM posts 
            JOIN users ON posts.user_id = users.id 
            WHERE posts.user_id = ?
            ORDER BY posts.created_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    // COMMENTAIRES
    public function ajouterCommentaire($post_id, $user_id, $contenu) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, contenu) VALUES (?, ?, ?)");
        return $stmt->execute([$post_id, $user_id, $contenu]);
    }

    public function getCommentaires($post_id) {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT comments.id, comments.contenu, comments.created_at,
                   users.id as user_id, users.nom, users.prenom, users.filiere
            FROM comments
            JOIN users ON comments.user_id = users.id
            WHERE comments.post_id = ?
            ORDER BY comments.created_at ASC
        ");
        $stmt->execute([$post_id]);
        return $stmt->fetchAll();
    }

    public function supprimerCommentaire($comment_id, $user_id) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
        return $stmt->execute([$comment_id, $user_id]);
    }

    public function getCommentaireCount($post_id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM comments WHERE post_id = ?");
        $stmt->execute([$post_id]);
        return $stmt->fetch()['count'];
    }

    // ÉPINGLAGE (Admin only)
    public function epinglerPost($post_id) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE posts SET is_pinned = 1 WHERE id = ?");
        return $stmt->execute([$post_id]);
    }

    public function desepinglerPost($post_id) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE posts SET is_pinned = 0 WHERE id = ?");
        return $stmt->execute([$post_id]);
    }

    public function isPostPinned($post_id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT is_pinned FROM posts WHERE id = ?");
        $stmt->execute([$post_id]);
        $result = $stmt->fetch();
        return $result && $result['is_pinned'] == 1;
    }
}
?>