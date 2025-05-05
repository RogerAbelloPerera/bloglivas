<?php
require_once('../DBConnection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recogemos los campos del formulario
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
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

    // Comprobamos si ya existe usuario o email
    $check = $db->prepare("SELECT * FROM users WHERE username = ? OR mail = ?");
    $check->execute([$username, $email]);

    if ($check->rowCount() > 0) {
        die("Aquest nom d'usuari o email ja existeix.");
    }

    // Comprobación y subida de archivos
    $profileImagePath = null;
    $bannerImagePath = null;

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $profileImagePath = '../uploads' . basename($_FILES['profile_image']['name']);
        move_uploaded_file($_FILES['profile_image']['tmp_name'], $profileImagePath);
    }

    if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] === UPLOAD_ERR_OK) {
        $bannerImagePath = '../uploads' . basename($_FILES['banner_image']['name']);
        move_uploaded_file($_FILES['banner_image']['tmp_name'], $bannerImagePath);
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
        $email, $username, $passwordHash, $firstName, $lastName, $biography, $age, $profileImagePath, 
        $bannerImagePath, $location
    ]);

    header("Location: ../html/index.html");
    exit;
}
