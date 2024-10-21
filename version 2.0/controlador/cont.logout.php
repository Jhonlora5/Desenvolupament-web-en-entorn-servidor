<?php
/* Jonathan Lopez Ramos */
//Elimina totes les variables de sessió
session_unset();
//Destrueix la sessió
session_destroy();
//Enviament de l'usuari al formulari de login.
header('Location: ../vista.formulari.php');
exit();
?>