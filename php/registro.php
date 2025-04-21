<?php
require_once('../connexioDB.php');
require './functions.php';
require './mailer.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $id = $_POST['id'] ?? null;
        $usuari = $_POST['usuari'] ?? null;
        $nom = $_POST['nom'] ?? null;
        $cognoms = $_POST['cognoms'] ?? null;
        $data_naixement = $_POST['data_naixement'] ?? null;
        $email = $_POST['email'] ?? null;
        $contrasenya = $_POST['contrasenya'] ?? null;
        $data_registre = $_POST['data_registre'] ?? null;

        $activationCode = hash('sha256', uniqid(mt_rand(), true));
        $activationDate = null;
        $actiu = 0;

        $sql = 'INSERT INTO usuaris (id, usuari, nom, cognoms, data_naixement, email, contrasenya, actiu, token_activacio, data_registre, activationCode, activationDate)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

        $insert = $db->prepare($sql);
        $insert->execute([$id, $usuari, $nom, $cognoms, $data_naixement, $email, $contrasenya, $actiu, 1, $data_registre, $activationCode, $activationDate]);

        $link = "http://localhost/php/mailCheckAccount.php?code=$activationCode&mail=$email";
        $subject = "Activa el teu compte";
        $body = "<h1>Benvingut/da $nom!</h1>
                 <p>Fes clic a l’enllaç per activar el teu compte:</p>
                 <a href='$link'>Activa el teu compte</a>";
        sendEmail($email, $subject, $body);

        echo "Usuari registrat correctament. Revisa el teu correu per activar el compte.";
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    } catch (PDOException $e) {
        echo 'Error amb la BDs: ' . $e->getMessage();
    }
}
?>
