<?php
require_once(__DIR__ . '/../DBConnection.php');
require_once(__DIR__ . '/mailer.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = $_POST['mail'] ?? '';
    $mail = trim($mail);

    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        echo "Format de correu electrònic no vàlid.";
        exit;
    }

    $stmt = $db->prepare('SELECT * FROM users WHERE mail = ?');
    $stmt->execute([$mail]);

    if ($stmt->rowCount() === 0) {
        echo "Aquest correu electrònic no existeix.";
        exit;
    }

    $resetPassCode = hash('sha256', uniqid(mt_rand(), true));
    $resetPassExpiry = date('Y-m-d H:i:s', strtotime('+30 minutes'));

    $update = $db->prepare('UPDATE users SET resetPassCode = ?, resetPassExpiry = ? WHERE mail = ?');
    $update->execute([$resetPassCode, $resetPassExpiry, $mail]);

    $link = 'http://localhost/bloglivas/php/resetPassword.php?code=' . urlencode($resetPassCode) . '&mail=' . urlencode($mail);
    
    $subject = "Restabliment de contrasenya";
    $body = "
        <h2>Recuperació de contrasenya</h2>
        <p>Has sol·licitat restablir la contrasenya del teu compte.</p>
        <p>Clica el següent enllaç abans de 30 minuts per continuar:</p>
        <a href='" . htmlspecialchars($link) . "'>Restablir contrasenya</a>
        <p>Si no has sol·licitat aquest canvi, pots ignorar aquest missatge.</p>
    ";

    if (sendMail($mail, $subject, $body)) {
        echo "Correu de recuperació enviat correctament. Comprova la teva bústia.";
    } else {
        echo "Error en enviar el correu electrònic.";
    }

    exit;
}
?>
