<?php
session_start();
require_once('../DBConnection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuari = trim($_POST['usuari'] ?? '');
    $contrasenya = $_POST['contrasenya'] ?? '';

    if (empty($usuari) || empty($contrasenya)) {
        die("Error: no es possible iniciar sessió amb les dades facilitades.");
    }

    try {

        // Busquem per nom o mail i actiu=1 (active = 1)

        $consulta = $db->prepare("SELECT * FROM users WHERE (username = ? OR mail = ?) AND active = 1");
        $consulta->execute([$usuari, $usuari]);

        $usuariTrobat = $consulta->fetch(PDO::FETCH_ASSOC);

        if ($usuariTrobat && password_verify($contrasenya, $usuariTrobat['passHash'])) {

            // creem nova sessió

            session_regenerate_id(true);

            $_SESSION['iduser'] = $usuariTrobat['iduser'];
            $_SESSION['username'] = $usuariTrobat['username'];
            $_SESSION['mail'] = $usuariTrobat['mail'];
            $_SESSION['userFirstName'] = $usuariTrobat['userFirstName'];
            $_SESSION['userLastName'] = $usuariTrobat['userLastName'];

            // guardem el lastSignIn

            $update = $db->prepare("UPDATE users SET lastSignIn = NOW() WHERE iduser = ?");
            $update->execute([$usuariTrobat['iduser']]);

            header("Location: home.php");
            exit;
        } else {
            echo "Error: no es possible iniciar sessió amb les dades facilitades.";
        }

    } catch (PDOException $e) {
        error_log("Error login: " . $e->getMessage());
        echo "Error intern del servidor.";
    }
}
?>