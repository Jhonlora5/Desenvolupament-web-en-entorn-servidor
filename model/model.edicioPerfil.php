<?php
require_once '../controlador/cont.connexio.php';
$pdo = obtenirConnexio();

function actualitzarPerfilUsuari($usuariId, $nouNom, $novaImatge) {
    global $pdo;

    $sql = "UPDATE usuaris SET nom = :nom, img_perfil_path = :img WHERE id_usuari = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nom' => $nouNom,
        ':img' => $novaImatge,
        ':id' => $usuariId
    ]);
}

function obtenirImatgesPerfils() {
    $directori = '../imgPerfils/';
    return glob($directori . "*.{jpg,png,gif}", GLOB_BRACE);
}
?>