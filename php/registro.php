<?php
require_once('../connexioDB.php');
require './functions.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {

        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $usuari = isset($_POST['usuari']) ? $_POST['usuari'] : null;
        $nom = isset($_POST['nom']) ? $_POST['nom'] : null;
        $cognoms = isset($_POST['cognoms']) ? $_POST['cognoms'] : null;
        $data_naixement = isset($_POST['data_naixement']) ? $_POST['data_naixement'] : null;
        $email = isset($_POST['email']) ? $_POST['email'] : null;
        $contrasenya = isset($_POST['contrasenya']) ? $_POST['contrasenya'] : null;
        $actiu = isset($_POST['actiu']) ? $_POST['actiu']    : 0;
        $token_activacio = isset($_POST['token_activacio']) ? $_POST['token_activacio'] : TRUE;
        $data_registre = isset($_POST['data_registre']) ? $_POST['data_registre'] : null;


        $sql = 'INSERT INTO usuaris (id, usuari, nom, cognoms, data_naixement, email, contrasenya, actiu, token_activacio, data_registre)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

        $insert = $db->prepare($sql);
        $insert->execute([$id, $usuari, $nom, $cognoms, $data_naixement, $email, $contrasenya, $actiu, $token_activacio, $data_registre]);

        echo "usuari afegit correctament.";

    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    } catch (PDOException $e) {
        echo 'Error amb la BDs: ' . $e->getMessage();
    }
}
?>