<?php
require 'cont.connexio.php';

$pdo = obtenirConnexio();

// Funció per obtenir la llista d'usuaris
function obtenirLlistaUsuaris() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT id_usuari, nom, email, data_registre, nivell_administrador FROM usuaris");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return ['error' => "No s'ha pogut obtenir la llista d'usuaris: " . $e->getMessage()];
    }
}

// Funció per esborrar un usuari
function esborrarUsuari($id_usuari) {
    global $pdo;
    if (!empty($id_usuari)) {
        try {
            $stmt = $pdo->prepare("DELETE FROM usuaris WHERE id_usuari = :id_usuari");
            $stmt->bindParam(':id_usuari', $id_usuari, PDO::PARAM_INT);
            $stmt->execute();
            return ['aconseguit' => "Usuari eliminat correctament!"];
        } catch (PDOException $e) {
            return ['error' => "Error a la funció esborrarUsuari: " . $e->getMessage()];
        }
    }
    return ['error' => "L'ID d'usuari és obligatori!"];
}

// Funció per actualitzar el nivell d'un usuari
function actualitzarNivellUsuari($id_usuari, $nivell_administrador) {
    global $pdo;
    if (!empty($id_usuari) && !empty($nivell_administrador)) {
        try {
            $stmt = $pdo->prepare("UPDATE usuaris SET nivell_administrador = :nivell_administrador WHERE id_usuari = :id_usuari");
            $stmt->bindParam(':id_usuari', $id_usuari, PDO::PARAM_INT);
            $stmt->bindParam(':nivell_administrador', $nivell_administrador, PDO::PARAM_INT);
            $stmt->execute();
            return ['aconseguit' => "Nivell d'usuari actualitzat correctament!"];
        } catch (PDOException $e) {
            return ['error' => "Error a la funció actualitzarNivellUsuari: " . $e->getMessage()];
        }
    }
    return ['error' => "L'ID d'usuari i el nivell són obligatoris!"];
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accio'])) {
    $accio = $_POST['accio'];
    $id_usuari = $_POST['id_usuari'] ?? '';
    $nivell_administrador = $_POST['nivell_administrador'] ?? '';
    $missatgeError = '';
    $missatgeCorrecte = '';

    if (empty($id_usuari) || !is_numeric($id_usuari)) {
        $missatgeError = "L'ID d'usuari ha de ser un número vàlid.";
    }

    // Processar accions
    if (empty($missatgeError)) {
        if ($accio === 'delete') {
            $resultat = esborrarUsuari($id_usuari);
            if (isset($resultat['error'])) {
                $missatgeError = $resultat['error'];
            } else {
                $missatgeCorrecte = $resultat['aconseguit'];
            }
        } elseif ($accio === 'actualitzar_nivell') {
            $resultat = actualitzarNivellUsuari($id_usuari, $nivell_administrador);
            if (isset($resultat['error'])) {
                $missatgeError = $resultat['error'];
            } else {
                $missatgeCorrecte = $resultat['aconseguit'];
            }
        } else {
            $missatgeError = "Acció desconeguda.";
        }
    }

    // Guardar missatges a la sessió
    $_SESSION['missatgeError'] = $missatgeError;
    $_SESSION['missatgeCorrecte'] = $missatgeCorrecte;

    // Redirigir a la pàgina de vista d'administració
    header("Location: ../vista/vista.administrador.php");
    exit();
}