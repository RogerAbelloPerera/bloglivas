<?php
require_once('../connexioDB.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $password = $_POST['password'];
    $verifyPassword = $_POST['verifyPassword'];

    if ($password !== $verifyPassword) {
        die("Les contrasenyes no coincideixen.");
    }

    // comprovem si el user o el mail coincideixen

    $check = $db->prepare("SELECT * FROM users WHERE username = ? OR mail = ?");
    $check->execute([$username, $email]);

    if ($check->rowCount() > 0) {
        echo "Aquest nom d'usuari o email ja existeix. Torna enrere i prova amb un altre.";
        exit;
    }

    // hashejem contrasenya

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // insertar usuari nou

    $insert = $db->prepare("
        INSERT INTO users (mail, username, passHash, userFirstName, userLastName, creationDate, active)
        VALUES (?, ?, ?, ?, ?, NOW(), 1)
    ");

    $insert->execute([
        $email,
        $username,
        $passwordHash,
        $firstName,
        $lastName
    ]);

    // Redirigir al login amb missatge (pot ser amb GET o una variable de sessió temporal)
  
    header("Location: ../html/index.html");
    exit;
}
?>