<?php
require_once('../DBConnection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recogemos los campos del formulario
    $username = trim($_POST['username']);
    $mail = trim($_POST['mail']);
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $password = $_POST['password'];
    $verifyPassword = $_POST['verifyPassword'];
    $biography = isset($_POST['biography']) ? trim($_POST['biography']) : null;
    $age = isset($_POST['age']) ? intval($_POST['age']) : null;
    $location = isset($_POST['location']) ? trim($_POST['location']) : null;

    if ($password !== $verifyPassword) {
        die("Les contrasenyes no coincideixen.");
    }

    // Comprobamos si ya existe usuario o mail
    $check = $db->prepare("SELECT * FROM users WHERE username = ? OR mail = ?");
    $check->execute([$username, $mail]);

    if ($check->rowCount() > 0) {
        die("Aquest nom d'usuari o mail ja existeix.");
    }

    // Comprobación y subida de archivos
    $profileImageName = null;
    $bannerImageName = null;

    $profileImageName = basename($_FILES['profile_image']['name']);
    $targetPath = "../uploads/" . $profileImageName;
    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
        $profileImagePath = "uploads/" . $profileImageName;  // Esta es la que se guarda en la base de datos
    }


    $bannerImageName = basename($_FILES['banner_image']['name']);
    $targetPath = "../uploads/" . $bannerImageName;
    if (move_uploaded_file($_FILES['banner_image']['tmp_name'], $targetPath)) {
        $bannerImagePath = "uploads/" . $bannerImageName;
    }

    // Hashear la contraseña
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar nuevo usuario
    $insert = $db->prepare("
        INSERT INTO users (
            mail, username, passHash, userFirstName, userLastName,
            creationDate, active, biography, age, profile_image, banner_image, location
        ) VALUES (?, ?, ?, ?, ?, NOW(), 1, ?, ?, ?, ?, ?)
    ");

    $insert->execute([
        $mail, $username, $passwordHash, $firstName, $lastName, $biography, $age, $profileImagePath, 
        $bannerImagePath, $location
    ]);

    header("Location: ../html/index.html");
    exit;
}
