<?php
class Competence {
    public function ajouterCompetence($user_id, $nom_competence) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO competences (user_id, nom) VALUES (?, ?)");
        return $stmt->execute([$user_id, $nom_competence]);
    }

    public function getCompetences($user_id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM competences WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }
}
?>