<?php
session_start();
require_once('../connexioDB.php');
require './functions.php';



if ($_SERVER['REQUEST_METHOD'] == 'POST'){

    $sql = 'SELECT usuari, contrasenya FROM usuaris';
    $select = $db->query($sql);
    $encontrado = checkuser($_POST["usuari"], $_POST["contrasenya"], $select);
    if(!$encontrado){
        echo"Usuario o contraseña incorrectos";
    }else{
        header('Location: ../php/perfil.php');
    }

}
?>

