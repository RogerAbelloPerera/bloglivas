<?php
session_start();
require_once('../php/functions.php');

if (!isUserLoggedIn()) {
    header("Location: index.html");
    exit;
}

$nom = $_SESSION['userFirstName'] ?? '';
$usuari = $_SESSION['username'] ?? '';
$biography = $_SESSION['biography'] ?? '';
$location = $_SESSION['location'] ?? '';
$age = $_SESSION['age'] ?? '';
$profileImage = $_SESSION['profile_image'] ?? 'uploads/default_profile.jpg';
$bannerImage = $_SESSION['banner_image'] ?? 'uploads/default_banner.jpg';

?>


<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Perfil d'Usuari</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f3f3;
            margin: 0;
        }
        .banner {
            width: 100%;
            height: 200px;
            background: #ccc url("banner_image.jpg") center/cover no-repeat;
        }
        .profile-container {
            max-width: 800px;
            margin: -60px auto 30px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px #bbb;
            position: relative;
        }
        .profile-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid white;
            position: absolute;
            top: -60px;
            left: 20px;
            object-fit: cover;
        }
        .info {
            margin-left: 150px;
        }
        h2 {
            margin: 0;
        }
        .posts {
            margin-top: 30px;
        }
        .post {
            background: #f9f9f9;
            padding: 10px;
            margin-bottom: 10px;
            border-left: 4px solid #0073e6;
        }
    </style>
</head>
<body>

<!-- Banner opcional -->
<a href="../<?php echo htmlspecialchars($bannerImage); ?>" target="_blank">
    <div class="banner" style="background-image: url('../<?php echo htmlspecialchars($bannerImage); ?>');"></div>
</a>

<div class="profile-container">

<a href="../<?php echo htmlspecialchars($_SESSION['profile_image']); ?>" target="_blank">
    <img class="profile-image" src="../<?php echo htmlspecialchars($_SESSION['profile_image']); ?>" alt="Imatge de perfil">
</a>

    <div class="info">
        <h1>Benvingut<?php echo $nom ? ", $nom" : ", $usuari"; ?>!</h1> 
        <p><strong>Biografia:</strong> <?php echo $biography ? $biography : "No disponible"; ?></p>
        <p><strong>Ubicació:</strong> <?php echo $location ? $location : "No disponible"; ?></p>
        <p><strong>Edat:</strong> <?php echo $age ? $age : "No disponible"; ?></p>
        <form action="../php/editarPerfil.php" method="POST">
            <button type="submit" class="boton">Editar Perfil</button>
        </form>
        <form action="../php/home.php" method="POST">
            <button type="submit" class="boton">Volver pantalla principal</button>
        </form>
    </div>

</div>

</body>
</html>
