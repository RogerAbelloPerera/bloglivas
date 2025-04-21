<?php
require_once('../connexioDB.php');
require './mailer.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';

    $stmt = $db->prepare('SELECT * FROM usuaris WHERE email=?');
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $resetPassCode = hash('sha256', uniqid(mt_rand(), true));
        $resetPassExpiry = date('Y-m-d H:i:s', strtotime('+30 minutes'));

        $update = $db->prepare('UPDATE usuaris SET resetPassCode=?, resetPassExpiry=? WHERE email=?');
        $update->execute([$resetPassCode, $resetPassExpiry, $email]);

        $link = "http://localhost/php/resetPassword.php?code=$resetPassCode&mail=$email";
        $subject = "Restabliment de contrasenya";
        $body = "<p>Has sol·licitat restablir la contrasenya. Clica aquí abans de 30 minuts:</p>
                 <a href='$link'>Restablir contrasenya</a>";

        sendEmail($email, $subject, $body);
        echo "Correu enviat. Comprova la teva bústia.";
    } else {
        echo "Aquest email no existeix.";
    }
}
?>
