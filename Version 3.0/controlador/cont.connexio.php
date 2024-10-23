<?php
/* Jonathan Lopez Ramos */
//Temps de vida de la sessio en 40 min. 40*60=2400 .
ini_set('session.gc_maxlifetime', 2400);

//Configuracio del temps de vida de les cookies.
session_set_cookie_params(2400);

session_start();

function obtenirConnexio(){
    //Variables de connexió a la base de dades
    //IP o localització de la base de dades.
    $host = 'localhost';
    //Nom de la base de dades a la que es vol connectar.
    $dbname = 'pt04_jonathan_lopez';
    //Usuari amb permissos d'edició
    $username = 'root';
    //Password de l'usuari
    $password = '';

    try {
        //Connexió a la base de dades amb les dades anteriors
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        return ['error'=> "Error al connectar a la base de dades: " . $e->getMessage()];
    }
}
?>