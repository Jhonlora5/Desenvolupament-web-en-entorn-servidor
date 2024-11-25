<?php
function actualitzarContrasenya($idUsuari, $contrasenyaAntiga, $novaContrasenya) {
    global $pdo;

    try {
        // Comprovar si la contrasenya antiga és correcta
        $stmt = $pdo->prepare("SELECT contrasenya FROM usuaris WHERE id_usuari = :idUsuari");
        $stmt->bindParam(':idUsuari', $idUsuari, PDO::PARAM_INT);
        $stmt->execute();
        $contrasenyaActual = $stmt->fetchColumn();

        if (!$contrasenyaActual || !password_verify($contrasenyaAntiga, $contrasenyaActual)) {
            return ['error' => "La contrasenya antiga no és correcta."];
        }

        // Generar hash de la nova contrasenya
        $contrasenyaHashed = password_hash($novaContrasenya, PASSWORD_DEFAULT);

        // Actualitzar la contrasenya a la base de dades
        $stmt = $pdo->prepare("UPDATE usuaris SET contrasenya = :novaContrasenya WHERE id_usuari = :idUsuari");
        $stmt->bindParam(':novaContrasenya', $contrasenyaHashed, PDO::PARAM_STR);
        $stmt->bindParam(':idUsuari', $idUsuari, PDO::PARAM_INT);
        $stmt->execute();

        return ['success' => "Contrasenya actualitzada correctament!"];
    } catch (PDOException $e) {
        return ['error' => "Error en actualitzar la contrasenya: " . $e->getMessage()];
    }
}
?>