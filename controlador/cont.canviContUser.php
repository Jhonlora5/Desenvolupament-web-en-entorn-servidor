<?php
/* Jonathan Lopez Ramos jaaaaaaarrrrrrr*/
//Connexió a la base de dades
require 'cont.connexio.php';
$pdo = obtenirConnexio();

function canviarContrasenya($idUsuari, $contrasenyaAntiga, $novaContrasenya) {
    global $pdo;

    try {
        //Comprovar que la contrasenya antiga és correcta
        $stmt = $pdo->prepare("SELECT contrasenya FROM usuaris WHERE id_usuari = :idUsuari");
        $stmt->bindParam(':idUsuari', $idUsuari);
        $stmt->execute();
        $contrasenyaActual = $stmt->fetchColumn();

        if (!$contrasenyaActual || !password_verify($contrasenyaAntiga, $contrasenyaActual)) {
            return ['error' => "La contrasenya antiga no és correcta."];
        }

        //Validar que la nova contrasenya té un mínim de 8 caràcters amb lletres, números i caràcters especials
        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $novaContrasenya)) {
            return ['error' => "La nova contrasenya ha de tenir almenys 8 caràcters, incloent lletres, números i caràcters especials."];
        }

        //Hash de la nova contrasenya
        $contrasenyaHashed = password_hash($novaContrasenya, PASSWORD_DEFAULT);

        //Actualitzar la contrasenya a la base de dades
        $stmt = $pdo->prepare("UPDATE usuaris SET contrasenya = :novaContrasenya WHERE id_usuari = :idUsuari");
        $stmt->bindParam(':novaContrasenya', $contrasenyaHashed);
        $stmt->bindParam(':idUsuari', $idUsuari);
        $stmt->execute();

        return ['success' => "Contrasenya actualitzada correctament!"];
    } catch (PDOException $e) {
        return ['error' => "Error en actualitzar la contrasenya: " . $e->getMessage()];
    }
}

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

    $resultat = canviarContrasenya($idUsuari, $contrasenyaAntiga, $novaContrasenya);

    if (isset($resultat['error'])) {
        $_SESSION['missatgeError'] = $resultat['error'];
    } else {
        $_SESSION['missatgeCorrecte'] = $resultat['success'];
    }

    header("Location: ../vista/vista.formulariLogin.php");
    exit();
}
