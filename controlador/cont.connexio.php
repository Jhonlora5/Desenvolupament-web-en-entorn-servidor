<?php
/* Jonathan Lopez Ramos */
//Temps de vida de la sessio en 40 min. 40*60=2400 .
//ini_set('session.gc_maxlifetime', 2400);

//Configuracio del temps de vida de les cookies.
session_set_cookie_params(2400);

session_start();

function obtenirConnexio(){
    //Variables de connexió a la base de dades
    //IP o localització de la base de dades.
    $host = 'localhost';
    //Nom de la base de dades a la que es vol connectar.
    $dbname = 'pt05_jonathan_lopez';
    //Usuari amb permissos d'edició
    $username = 'phpmyadmin';
    //Password de l'usuari
    $password = 'Abcd1234';

    try {
        //Connexió a la base de dades amb les dades anteriors
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        return ['error'=> "Error al connectar a la base de dades: " . $e->getMessage()];
    }
}
// Funció per verificar si la sessió ha expirat
function verificarInactivitat() {
    $tempsInactivitatMaxim = 2400; // 40 minuts en segons

    //Si la sessió existeix, verifiquem el temps d'inactivitat
    if (isset($_SESSION['temps_ultima_activitat'])) {
        $tempsInactiu = time() - $_SESSION['temps_ultima_activitat'];

        //Si el temps inactiu és superior al màxim, tanca la sessió i redirigeix
        if ($tempsInactiu > $tempsInactivitatMaxim) {
            $missatgeError="El teu temps de sessió ha caducat per inactivitat.";  //Defineix la variable amb el missatge d'error
            $_SESSION['missatgeError'] = $missatgeError;  //Defineix el missatge d'error
            session_unset();  // Elimina totes les variables de sessió
            session_destroy();  // Destrueix la sessió
            header("Location: ../vista/vista.formulari.php");  // Redirigeix a la pàgina de login
            exit();
        }
    }

    // Actualitzem el temps de l'última activitat
    $_SESSION['temps_ultima_activitat'] = time();
}
//Crida a la funció.
verificarInactivitat();

?>