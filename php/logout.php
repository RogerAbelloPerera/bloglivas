<?php
session_start();
session_unset();  // Borrar variables
session_destroy(); // Cerrar sesión
header("Location: ../html/index.html"); // Volver al login
exit;
