<?php
require_once('../connexioDB.php');

if (isset($_GET['code']) && isset($_GET['mail'])) {
    $code = $_GET['code'];
    $mail = $_GET['mail'];

    $sql = 'SELECT * FROM usuaris WHERE email=? AND activationCode=?';
    $stmt = $db->prepare($sql);
    $stmt->execute([$mail, $code]);

    if ($stmt->rowCount() > 0) {
        $update = $db->prepare('UPDATE usuaris SET actiu=1, activationCode=NULL, activationDate=NOW() WHERE email=?');
        $update->execute([$mail]);
        header("Location: ../html/login.html?activation=success");
    } else {
        echo "Verificació fallida. El codi no és vàlid.";
    }
}
?>
