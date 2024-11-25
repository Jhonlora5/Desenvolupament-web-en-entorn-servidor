<?php

require_once '../model/model.edicioPerfil.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nouNom = $_POST['nom'] ?? '';
    $novaImatge = $_POST['img_perfil'] ?? '';

    if (empty($nouNom) || empty($novaImatge)) {
        $_SESSION['missatgeError'] = "Tots els camps són obligatoris.";
        header("Location: ../vista/vista.edicioPerfil.php");
        exit();
    }

    try {
        actualitzarPerfilUsuari($_SESSION['usuari_id'], $nouNom, $novaImatge);

        $_SESSION['usuari_nom'] = $nouNom;
        $_SESSION['img_perfil'] = $novaImatge;
        $_SESSION['missatgeCorrecte'] = "Perfil actualitzat correctament.";
    } catch (PDOException $e) {
        $_SESSION['missatgeError'] = "Error al guardar els canvis: " . $e->getMessage();
    }

    header("Location: ../vista/vista.edicioPerfil.php");
    exit();
}
?>
