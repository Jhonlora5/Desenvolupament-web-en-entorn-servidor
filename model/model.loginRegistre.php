<?php
/* Jonathan Lopez Ramos */
//El session_start es realitza a la funció obtenirConnexio.
require '../controlador/cont.administracioUsuaris.php';
require_once '../controlador/cont.loginRegistre.php';
//Cridem a la funció encarregada de la connexio.
$pdo = obtenirConnexio();
//Afegim la key del server per a recaptcha en una variable
$keyServer = $_SESSION['key-server']?? null;
//Cridem a la funcio encarregada de mirar el nivell de l'usuari a nivell administratiu.
$usuari= obtenirLlistaUsuaris();
$_SESSION['nivell_administrador'] = $usuari['nivell_administrador'];



// Processa les diferents accions segons les dades rebudes
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Si arriben dades de registre
        if (isset($_POST['nom'], $_POST['email'], $_POST['password'], $_POST['confirm-password'])) {
            $nom = trim($_POST['nom']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm-password'];

            manejarRegistre($nom, $email, $password, $confirmPassword, $pdo);
        }

        // Si arriben dades de login
        if (isset($_POST['email'], $_POST['password'])) {
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            manejarLogin($email, $password, $pdo, $keyServer);
        }
    } catch (Exception $e) {
        $_SESSION['missatgeError'] = "S'ha produït un error a la rebuda de les dades de formulari: " . $e->getMessage();
        header('Location: ../vista/vista.formulariLogin.php');
        exit;
    }
}
?>