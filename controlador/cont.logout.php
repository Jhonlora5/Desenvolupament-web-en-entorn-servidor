<?php
/* Jonathan Lopez Ramos */

session_start();

// Eliminar totes les variables de sessió
session_unset();

// Destruir la sessió
session_destroy();

// Eliminar cookies de sessió, si existeixen
setcookie(session_name(), '', time() - 3600, '/');

// Redirigir a la pàgina de login
$_SESSION['missatgeError'] = "Has sortit correctament.";
header('Location: ../vista/vista.formulariLogin.php');
