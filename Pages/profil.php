<?php
session_start();
require_once '../db.php';
require_once '../Classe/User.php';
require_once '../Classe/Interet.php';
require_once '../classes/Competence.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user = new User();
$interet = new Interet();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['bio'])) {
        $user->updateBio($_SESSION['user_id'], trim($_POST['bio']));
    }
    if (isset($_POST['interet'])) {
        $interet->ajouterInteret($_SESSION['user_id'], trim($_POST['interet']));
    }
}

$profil = $user->getProfil($_SESSION['user_id']);
$interets = $interet->getInterets($_SESSION['user_id']);
?>