<?php
session_start();

//Connexió a la base de dades
require '../controlador/cont.connexio.php'; 

//Obtenir els articles corresponents.
$pdo = obtenirConnexio();

//Funció per restablir la contrasenya

function restablirContrasenya($token, $contrasenyaNova) {
    global $pdo;

    try {
        // Comprovar que el token existeix i és vàlid
        $stmt = $pdo->prepare("SELECT id_usuari FROM recuperacio_tokens WHERE token = :token AND expira > NOW()");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $idUsuari = $stmt->fetchColumn();

        if (!$idUsuari) {
            return ['error' => "Token invàlid o caducat."];
        }

        // Si el token és vàlid, actualitzar la contrasenya
        $contrasenyaHashed = password_hash($contrasenyaNova, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuaris SET contrasenya = :contrasenya WHERE id_usuari = :idUsuari");
        $stmt->bindParam(':contrasenya', $contrasenyaHashed);
        $stmt->bindParam(':idUsuari', $idUsuari);
        $stmt->execute();

        // Esborrar el token utilitzat
        $stmt = $pdo->prepare("DELETE FROM recuperacio_tokens WHERE token = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        return ['success' => "Contrasenya actualitzada correctament!"];
    } catch (PDOException $e) {
        return ['error' => "Error en actualitzar la contrasenya: " . $e->getMessage()];
    }
}

// Processar el formulari si es fa POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $contrasenya = $_POST['contrasenya'] ?? '';
    $confirmarContrasenya = $_POST['confirmar_contrasenya'] ?? '';
    $token = $_POST['token'] ?? '';

    // Comprovar si les contrasenyes coincideixen
    if ($contrasenya !== $confirmarContrasenya) {
        $_SESSION['missatgeError'] = "Les contrasenyes no coincideixen.";
        header("Location: ../vista.Reestableix_contrasenya.php?token=$token");
        exit();
    }

    // Si les contrasenyes coincideixen, procedir amb la funció de restabliment
    $resultat = restablirContrasenya($token, $contrasenya);

    if (isset($resultat['error'])) {
        $_SESSION['missatgeError'] = $resultat['error'];
        header("Location: ../vista.Reestableix_contrasenya.php?token=$token");
        exit();
    } else {
        $_SESSION['missatgeCorrecte'] = $resultat['success'];
        header("Location: ../vista.formularilogin.php"); // Redirigir a la vista d'inici de sessió
        exit();
    }
}

