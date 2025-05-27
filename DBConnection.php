<?php
$dsn = 'mysql:host=localhost;port=3306;dbname=bloglivas;charset=utf8mb4';
$user = 'root';
$password = '';

try {
    $db = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lanza excepciones si hay errores
        PDO::ATTR_PERSISTENT => true,                 // Conexión persistente
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Resultados como arrays asociativos por defecto
    ]);
} catch (PDOException $e) {
    die('Error en la connexió amb la base de dades: ' . $e->getMessage());
}
