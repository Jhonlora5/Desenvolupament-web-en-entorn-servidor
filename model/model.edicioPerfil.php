<?php
require_once '../controlador/cont.connexio.php';
$pdo = obtenirConnexio();

//Actualitza el path de la imatge i nom de l'usuari
function actualitzarPerfilUsuari($usuariId, $nouNom, $novaImatge) {
    global $pdo;

    $sql = "UPDATE usuaris SET nom = :nom, id_imatge = :idImatge WHERE id_usuari = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nom' => $nouNom,
        ':idImatge' => $novaImatge,
        ':id' => $usuariId
    ]);
}

//Obté les imatges del perfil
function obtenirImatgesPerfils() {
    global $pdo;

    $sql = "SELECT id_imatge, ruta FROM imatges_perfil";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


//Obté la ruta de la imatge a partir del seu ID. 
function obtenirRutaImatge($idImatge) {
    global $pdo;
    $sql = "SELECT ruta FROM imatges_perfil WHERE id_imatge = :idImatge";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':idImatge' => $idImatge]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['ruta'];
}
?>