<?php
class Interet {
    public function ajouterInteret($user_id, $nom_interet) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO interets (user_id, nom) VALUES (?, ?)");
        return $stmt->execute([$user_id, $nom_interet]);
    }
    // Supprimer une compétence
    public function supprimerInteret($user_id, $interet_id) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM interets WHERE id = ? AND user_id = ?");
        return $stmt->execute([$interet_id, $user_id]);
    }

    public function getInterets($user_id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM interets WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }
}
?>