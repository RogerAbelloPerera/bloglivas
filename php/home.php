<?php
session_start();
require_once('functions.php');

// si no hi ha sessió, redirect a index (login)

if (!isUserLoggedIn()) {
    header("Location: index.html");
    exit;
}

$nom = $_SESSION['userFirstName'] ?? '';
$usuari = $_SESSION['username'] ?? '';
?>

<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <title>Benvingut</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-image: url("../assets/olive_wallpaper3.jpg");
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-shadow: 1px 1px 4px black;
        }

        .contenidor {
            background-color: rgba(0, 0, 0, 0.6);
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
        }

        .boton-logout {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #ff5555;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
        }

        .boton-logout:hover {
            background-color: #ff2222;
        }
    </style>
</head>

<body>
    <div class="contenidor">
        <img src="../assets/logo.png" alt="bloglivas">
        <h1>Benvingut<?php echo $nom ? ", $nom" : ", $usuari"; ?>!</h1>
        <p>Estàs connectat a bloglivas!!!</p>
        <form action="../php/perfil.php" method="POST">
            <button type="submit" class="boton-logout">Perfil</button>
        </form>
        <form action="logout.php" method="POST">
            <button type="submit" class="boton-logout">Tancar sessió</button>
        </form>

    </div>
</body>

</html>