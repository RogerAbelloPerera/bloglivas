<?php
session_start();
require_once('../DBConnection.php');
if (!isset($_SESSION['iduser'])) header("Location: login.php");

$postId = intval($_POST['post_id'] ?? 0);
$text   = trim($_POST['comment_text'] ?? '');
$userId = $_SESSION['iduser'];

if ($postId > 0 && $text !== '') {
    $ins = $db->prepare("INSERT INTO comentari (usuari_id, publicacio_id, text) VALUES (?,?,?)");
    $ins->execute([$userId, $postId, $text]);
}

header("Location: home.php");
exit;
