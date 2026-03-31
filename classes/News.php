<?php
class News {
    
    // Récupérer les actualités (par exemple les 5 prochaines)
    public function getToutesLesNews() {
        global $pdo;
        
        // On récupère les news triées par date d'événement
        $stmt = $pdo->query("SELECT * FROM news ORDER BY date_event ASC LIMIT 5");
        return $stmt->fetchAll();
    }
}
?>