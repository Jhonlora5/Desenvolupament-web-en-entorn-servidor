<?php
require 'cont.connexio.php';
require '../model/model.eliminarCompra.php';


if (!isset($_SESSION['usuari_id'])) {
    header('Location: ../vista.formularilogin.php');
    exit();
}

$pdo = obtenirConnexio();
$id_compra = $_POST['id_compra'];

try {
    // Obtenim la compra
    $compra = obtenirCompraPerId($pdo, $id_compra);

    if ($compra) {
        // Actualitzem la quantitat de l'article
        actualitzarQuantitatArticle($pdo, $compra['fk_article_articles'], $compra['quantitat']);

        // Eliminem la compra
        eliminarCompra($pdo, $id_compra);

        // Missatge d'èxit
        $_SESSION['missatgeCorrecte'] = "L'article amb la ID {$compra['fk_article_articles']} s'ha eliminat correctament.";
    } else {
        // Missatge d'error
        $_SESSION['missatgeError'] = "No s'ha trobat cap compra amb la ID proporcionada.";
    }
} catch (PDOException $e) {
    $_SESSION['missatgeError'] = "Error en eliminar la compra: " . $e->getMessage();
}

// Redirigim a vista.veureCompres.php
header("Location: ../vista/vista.veureCompres.php");
exit();
?>
