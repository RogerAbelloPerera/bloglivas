<?php
require_once(__DIR__ . '/../DBConnection.php');
require_once(__DIR__ . '/mailer.php');

$showForm = false;
$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $code = $_GET['code'] ?? '';
    $mail = $_GET['mail'] ?? '';

    $stmt = $db->prepare("SELECT * FROM users WHERE mail = ? AND resetPassCode = ? AND resetPassExpiry >= NOW()");
    $stmt->execute([$mail, $code]);

    if ($stmt->rowCount() > 0) {
        $showForm = true;
    } else {
        $errorMessage = "Enllaç invàlid o caducat.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = $_POST['mail'];
    $code = $_POST['code'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if ($password !== $confirm) {
        $errorMessage = "Les contrasenyes no coincideixen.";
        $showForm = true;
    } elseif (strlen($password) < 6) {
        $errorMessage = "La contrasenya ha de tenir almenys 6 caràcters.";
        $showForm = true;
    } else {
        $stmt = $db->prepare("SELECT * FROM users WHERE mail = ? AND resetPassCode = ? AND resetPassExpiry >= NOW()");
        $stmt->execute([$mail, $code]);

        if ($stmt->rowCount() > 0) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $update = $db->prepare("UPDATE users SET passHash = ?, resetPassCode = NULL, resetPassExpiry = NULL WHERE mail = ?");
            $update->execute([$hashed, $mail]);

            sendMail($mail, "Contrasenya canviada", "<p>La teva contrasenya s'ha actualitzat correctament.</p>");
            $successMessage = "Contrasenya actualitzada correctament.";
        } else {
            $errorMessage = "Codi invàlid o caducat.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <title>Restablir Contrasenya</title>
    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
            padding: 50px;
        }

        .container {
            background: white;
            max-width: 400px;
            margin: auto;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
        }

        input {
            width: 100%;
            margin-top: 10px;
            padding: 10px;
        }

        button {
            margin-top: 20px;
            width: 100%;
            padding: 10px;
            background: #0073e6;
            color: white;
            border: none;
            border-radius: 4px;
        }

        .msg {
            text-align: center;
            margin-top: 20px;
            color: green;
        }

        .error {
            color: red;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Restablir Contrasenya</h2>

        <?php if ($successMessage): ?>
            <p class="msg"><?= htmlspecialchars($successMessage) ?></p>
        <?php elseif ($errorMessage): ?>
            <p class="msg error"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>

        <?php if ($showForm): ?>
            <form method="POST">
                <input type="hidden" name="mail" value="<?= htmlspecialchars($mail) ?>">
                <input type="hidden" name="code" value="<?= htmlspecialchars($code) ?>">
                <label>Nova Contrasenya:</label>
                <input type="password" name="password" required>
                <label>Repeteix Contrasenya:</label>
                <input type="password" name="confirm" required>
                <button type="submit">Actualitzar Contrasenya</button>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>