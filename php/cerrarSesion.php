<?php
session_start();
require './functions.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    closeSession();
}else{
    header('Location: ../html/login.html');
}
?>