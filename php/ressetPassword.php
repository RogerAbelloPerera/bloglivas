<?php
require_once('../connexioDB.php');

if (isset($_GET['code']) && isset($_GET['mail'])) {
    $code = $_GET['code'];
    $mail = $_GET['mail'];

    $stmt = $db->prepare('SELECT * FROM usuaris WHERE email=? AND resetPassCode=? AND resetPassExpiry > NOW()');
    $stmt->execute([$mail, $code]);

    if ($stmt->rowCount() > 0) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $newpass1 = $_POST['newpass1'];
            $newpass2 = $_POST['newpass2'];

            if ($newpass1 === $newpass2) {
                $update = $db->prepare('UPDATE usuaris SET contrasenya=?, resetPassCode=NULL, resetPassExpiry=NULL WHERE email=?');
                $update->execute([$newpass1, $mail]);

                echo "Contrasenya actualitzada correctament. Ja pots iniciar sessió.";
                exit;
            } else {
                echo "Les contrasenyes no coincideixen.";
            }
        }
?>
<!DOCTYPE html>
<html lang="ca">
<head><meta charset="UTF-8"><title>Restablir Contrasenya</title></head>
<body>
<h2>Nova contrasenya</h2>
<form method="POST">
    <label>Nova:</label><input type="password" name="newpass1" required><br><br>
    <label>Repeteix:</label><input type="password" name="newpass2" required><br><br>
    <input type="submit" value="Restablir">
</form>
</body>
</html>
<?php
    } else {
        echo "Enllaç invàlid o caducat.";
    }
}
?>
