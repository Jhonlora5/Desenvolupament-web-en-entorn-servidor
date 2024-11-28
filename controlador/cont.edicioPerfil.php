<?php
require_once '../model/model.edicioPerfil.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nouNom = trim($_POST['nom'] ?? '');
    $novaImatgeId = trim($_POST['img_perfil'] ?? '');

    // Validar que els camps no estiguin buits
    if (empty($nouNom) || empty($novaImatgeId)) {
        $_SESSION['missatgeError'] = "Tots els camps són obligatoris.";
        header("Location: ../vista/vista.edicioPerfil.php");
        exit();
    }

    try {
        // Obtenir imatges disponibles i verificar si la imatge seleccionada és vàlida
        $imatgesDisponibles = obtenirImatgesPerfils();
        $imatgeSeleccionada = array_filter($imatgesDisponibles, function($imatge) use ($novaImatgeId) {
            return $imatge['id_imatge'] == $novaImatgeId;
        });

        if (empty($imatgeSeleccionada)) {
            throw new Exception("Imatge seleccionada no vàlida.");
        }

        // Actualitzar a la base de dades
        actualitzarPerfilUsuari($_SESSION['usuari_id'], $nouNom, $novaImatgeId);

        // Actualitzar la sessió amb els nous valors
        $_SESSION['nom_usuari'] = $nouNom;
        $_SESSION['imatge_perfil'] = obtenirRutaImatge($novaImatgeId); // Ruta de la nova imatge
        $_SESSION['img_perfil_id'] = $novaImatgeId;

        $_SESSION['missatgeCorrecte'] = "Perfil actualitzat correctament.";
    } catch (Exception $e) {
        $_SESSION['missatgeError'] = "Error al guardar els canvis: " . $e->getMessage();
    }

    // Tornar a la vista d'edició del perfil
    header("Location: ../vista/vista.edicioPerfil.php");
    exit();
}
?>