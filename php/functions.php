<?php
function isUserLoggedIn(): bool
{
    return isset($_SESSION['iduser']);
}
?>