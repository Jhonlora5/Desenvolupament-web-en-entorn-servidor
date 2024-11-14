<?php
//session_start();

//Connexió a la base de dades
require 'cont.connexio.php'; 

//Obtenir els articles corresponents.
$pdo = obtenirConnexio();

//Funció per restablir la contrasenya

function restablirContrasenya($token, $contrasenyaNova) {
    global $pdo;

    try {
        //Comprovar que el token existeix i és vàlid
        $stmt = $pdo->prepare("SELECT email FROM recuperacio_contrasenya WHERE token = :token AND expiracio >= NOW()");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $email = $stmt->fetchColumn();

        if (!$email) {
            return ['error' => "Token invàlid o caducat."];
        }

        //Si el token és vàlid, actualitzar la contrasenya
        $contrasenyaHashed = password_hash($contrasenyaNova, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuaris SET contrasenya = :contrasenya WHERE email = :email");
        $stmt->bindParam(':contrasenya', $contrasenyaHashed);
        $stmt->bindParam(':email', $email);
        $stmt->execute();        

        //Esborrar el token utilitzat
        $stmt = $pdo->prepare("DELETE FROM recuperacio_contrasenya WHERE token = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        return ['success' => "Contrasenya actualitzada correctament!"];
    } catch (PDOException $e) {
        return ['error' => "Error en actualitzar la contrasenya: " . $e->getMessage()];
    }
}

//Processar el formulari si es fa POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $contrasenya = $_POST['nova_contrasenya'] ?? '';
    $confirmarContrasenya = $_POST['confirmar_contrasenya'] ?? '';
    $token = $_POST['token'] ?? '';

    
   /* var_dump("Token rebut: ", $token);
    var_dump("Contingut taula recuperacio_contrasenya: ", $pdo->query("SELECT * FROM recuperacio_contrasenya")->fetchAll());
    exit; */

    //Comprova el hash desat
    //var_dump("Hash desat: " . $contrasenyaHashed);

    //Comprovar si les contrasenyes coincideixen
    if ($contrasenya !== $confirmarContrasenya) {
        $_SESSION['missatgeError'] = "Les contrasenyes no coincideixen.";
        header("Location: ../vista/vista.reestableix_contrasenya.php?token=". urlencode($token));
        exit();
    }

    //Validar la contrasenya
    if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $contrasenya)) {
        $_SESSION['missatgeError'] = "La contrasenya ha de tenir un mínim de 8 caràcters, incloure lletres, números i caràcters especials.";
        header("Location: ../vista/vista.reestableix_contrasenya.php?token=" . urlencode($token));
        exit();
    }

    //Si les contrasenyes coincideixen, procedir amb la funció de restabliment
    $resultat = restablirContrasenya($token, $contrasenya);

    if (isset($resultat['error'])) {
        $_SESSION['missatgeError'] = $resultat['error'];
        header("Location: ../vista/vista.reestableix_contrasenya.php?token=$token");
        exit();
    } else {
        $_SESSION['missatgeCorrecte'] = $resultat['success'];
        header("Location: ../vista/vista.formulariLogin.php"); // Redirigir a la vista d'inici de sessió
        exit();
    }
}

