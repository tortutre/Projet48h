<?php
session_start();
require_once '../db.php';
require_once '../Classe/Message.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$message = new Message();
$receiver_id = isset($_GET['user']) ? (int)$_GET['user'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $receiver_id) {
    $contenu = trim($_POST['contenu']);
    if (!empty($contenu)) {
        $message->envoyerMessage($_SESSION['user_id'], $receiver_id, $contenu);
    }
}

$conversation = $receiver_id ? $message->getConversation($_SESSION['user_id'], $receiver_id) : [];
?>