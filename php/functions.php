<?php
function checkuser($usuari, $contrasenya, $select){
    $encontrado = FALSE;
    foreach($select as $usuaris){
        if($usuaris['usuari'] == $usuari && $usuaris['contrasenya'] == $contrasenya){
            setcookie("nomcookie", $usuaris['usuari'], time() + 7+24*60*60);
            $_SESSION['usuari'] = $usuaris['usuari'];
            $encontrado = TRUE;
        }
    }
    return $encontrado;
}

function closeSession(){
    session_unset(); // Esborra totes les variables de sessió
    session_destroy(); // Destrueix la sessió
    header("Location: ../html/login.html");
}


function checksession(){
    if(!isset($_SESSION['usuari'])){
        header ('Location: perfil.php');
    }
}

?>