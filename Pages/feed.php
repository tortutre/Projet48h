<?php
session_start();
require_once '../db.php';
require_once '../classes/Post.php';
require_once '../classes/News.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$post = new Post();
$news = new News();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenu = trim($_POST['contenu']);
    if (!empty($contenu)) {
        $post->creerPost($_SESSION['user_id'], $contenu);
    }
}

$tousLesPosts = $post->getTousLesPosts();
$toutesLesNews = $news->getToutesLesNews();
?>