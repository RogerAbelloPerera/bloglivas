<?php
session_start();
require_once('../connexioDB.php');
require './functions.php';
checksession();

$usuari = $_SESSION['usuari'];

$sql = 'SELECT * FROM usuaris WHERE usuari=?';
$select = $db->prepare($sql);
$select->execute(array($usuari));

$usuaris = $select->fetch(PDO::FETCH_ASSOC);

if ($usuaris) {
    $id = $usuaris['id'];
    $usuari = $usuaris['usuari'];
    $nom = $usuaris['nom'];
    $cognoms = $usuaris['cognoms'];
    $data_naixement = $usuaris['data_naixement'];
    $email = $usuaris['email'];
    $contrasenya = $usuaris['contrasenya'];
    $actiu = $usuaris['actiu'];
    $token_activacio = $usuaris['token_activacio'];
    $data_registre = $usuaris['data_registre'];
} else {
    echo "<p>No s'ha trobat cap usuaris amb aquest nom.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Dades usuari</title>
    <style>
        body {background-color: <?php echo htmlspecialchars($_COOKIE['color']); ?>;}
    </style>
</head>
<body>

<h3>Dades de la usuaris:</h3>
<ul>
    <li><strong>Id:</strong> <?php echo $id; ?></li>
    <li><strong>Usuari:</strong> <?php echo $usuari; ?></li>
    <li><strong>Nom:</strong> <?php echo $nom; ?></li>
    <li><strong>Apellidos:</strong> <?php echo $cognoms; ?></li>
    <li><strong>Data Naixement:</strong> <?php echo $data_naixement; ?></li>
    <li><strong>Email:</strong> <?php echo $email; ?></li>
    <li><strong>Contraseña:</strong> <?php echo $contrasenya; ?></li>
    <li><strong>Fecha Registro:</strong> <?php echo $data_registre; ?></li>
</ul>

<form action="cerrarSesion.php" method="POST">
<input type="submit" value="Cerrar Sesion">
</body>
</html>
