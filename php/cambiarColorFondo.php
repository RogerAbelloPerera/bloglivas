<?php
session_start();
require_once('../connexioDB.php');
require './functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['color'])) {
    setcookie("color", $_POST['color'], time() + (86400 * 30));
    header('Location: ../html/login.html');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar color</title>
    <style>
        body {background-color: <?php echo htmlspecialchars($_COOKIE['color']); ?>;}
    </style>
</head>
<body>
    <form action="cambiarColorFondo.php" method="post">
    <label for="color">Escoge un Color</label>
    <input type="color" name="color">
    <input type="submit" value="Guardar"> 
    </form>
</body>
</html> 