<?php
session_start();
require_once('../DBConnection.php');
if (!isset($_SESSION['iduser'])) header("Location: login.php");

$postId = intval($_POST['post_id'] ?? 0);
$type   = in_array($_POST['type'] ?? '', ['like','dislike']) ? $_POST['type'] : 'like';
$userId = $_SESSION['iduser'];

if ($postId > 0) {
    // Comprovem si ja existeix
    $chk = $db->prepare("SELECT * FROM reaccio WHERE usuari_id=? AND publicacio_id=?");
    $chk->execute([$userId, $postId]);

    if ($chk->rowCount()) {
        // Actualitzem tipus
        $upd = $db->prepare("UPDATE reaccio SET tipus=?, data_reaccio=NOW() WHERE usuari_id=? AND publicacio_id=?");
        $upd->execute([$type, $userId, $postId]);
    } else {
        // Insert
        $ins = $db->prepare("INSERT INTO reaccio (usuari_id, publicacio_id, tipus) VALUES (?,?,?)");
        $ins->execute([$userId, $postId, $type]);
    }
}

header("Location: home.php");
exit;
