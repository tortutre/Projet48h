<?php
require_once __DIR__ . '/../db.php';

class News {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getToutesLesNews() {
        $stmt = $this->pdo->query("SELECT * FROM news ORDER BY date_event ASC");
        return $stmt->fetchAll();
    }

    public function ajouterNews($titre, $description, $type, $date_event) {
        $stmt = $this->pdo->prepare("INSERT INTO news (titre, description, type, date_event) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$titre, $description, $type, $date_event]);
    }
}
?>