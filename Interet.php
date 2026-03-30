<?php
require_once 'db.php';

class Interet {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function ajouterInteret($user_id, $nom) {
        $stmt = $this->pdo->prepare("INSERT INTO interets (user_id, nom) VALUES (?, ?)");
        return $stmt->execute([$user_id, $nom]);
    }

    public function getInterets($user_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM interets WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function getEtudiantsParInteret($nom) {
        $stmt = $this->pdo->prepare("SELECT users.* FROM users JOIN interets ON users.id = interets.user_id WHERE interets.nom = ?");
        $stmt->execute([$nom]);
        return $stmt->fetchAll();
    }
}
?>
```

---

## Structure de tes fichiers
```
Projet48h/
├── db.php
├── User.php
├── Post.php
├── Message.php
├── News.php
├── Interet.php
└── ylink.sql