<?php
/* Jonathan Lopez Ramos */

//Elimina totes les variables de sessió
session_unset();

//Destrueix la sessió
session_destroy();
$missatgeError = "El teu temps de sessió ha caducat per inactivitat.";

//Defineix el missatge d'error
$_SESSION['missatgeError'] = $missatgeError;  

//Enviament de l'usuari al formulari de login.
header('Location: ../vista/vista.formulariLogin.php');
exit();
?>