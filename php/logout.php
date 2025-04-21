<?php
session_start();

// Borrar variables

session_unset();  

// Tancar sessió

session_destroy(); 

// Tornar al login (amb la sessio destruida)

header("Location: ../html/index.html"); 
exit;
