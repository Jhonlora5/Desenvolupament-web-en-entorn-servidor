<?php
/* Jonathan Lopez Ramos jaaaaaaarrrrrrr*/
//Connexió a la base de dades
require 'cont.connexio.php';
require_once '../model/model.canviContUser.php';
$pdo = obtenirConnexio();

//Processar el formulari
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $contrasenyaAntiga = $_POST['contrasenyaAntiga'] ?? '';
    $novaContrasenya = $_POST['novaContrasenya'] ?? '';
    $confirmarContrasenya = $_POST['confirmarContrasenya'] ?? '';
    $idUsuari = $_SESSION['usuari_id'] ?? null;//Guardem el numero de l'usuari a traves de la sessio.

    if (!$idUsuari) {
        $_SESSION['missatgeError'] = "No s'ha pogut identificar l'usuari.";
        header("Location: ../vista/vista.canviContUser.php");
        exit();
    }

    if ($novaContrasenya !== $confirmarContrasenya) {
        $_SESSION['missatgeError'] = "Les noves contrasenyes no coincideixen.";
        header("Location: ../vista/vista.canviContUser.php");
        exit();
    }

    // Validar que la nova contrasenya compleix els requisits de seguretat
    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $novaContrasenya)) {
        $_SESSION['missatgeError'] = "La nova contrasenya ha de tenir com a mínim 8 caràcters, incloent majúscules, minúscules, números i caràcters especials.";
        header("Location: ../vista/vista.canviContUser.php");
        exit();
    }

    $resultat = actualitzarContrasenya($idUsuari, $contrasenyaAntiga, $novaContrasenya);

    if (isset($resultat['error'])) {
        $_SESSION['missatgeError'] = $resultat['error'];
    } else {
        $_SESSION['missatgeCorrecte'] = $resultat['success'];
    }
    header("Location: ../vista/vista.formulariLogin.php");
    exit();
}
?>