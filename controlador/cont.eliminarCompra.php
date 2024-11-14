<?php
require 'cont.connexio.php';
//Ens asegurem que l'usuari està logat.
if (!isset($_SESSION['usuari_id'])) {
    header('Location: ../vista.formularilogin.php');
    exit();
}

//Obtenir la connexio pdo a la base de daes
$pdo = obtenirConnexio();

$id_compra = $_POST['id_compra'];

try {
    //Obtenim la quantitat i l'article de la compra abans d'eliminar-la
    $sql_select = "SELECT quantitat, fk_article_articles FROM compres WHERE id_compra = :id_compra";
    $stmt_select = $pdo->prepare($sql_select);
    $stmt_select->bindParam(':id_compra', $id_compra, PDO::PARAM_INT);
    $stmt_select->execute();
    $compra = $stmt_select->fetch(PDO::FETCH_ASSOC);
    //Si la compra
    if ($compra) {
        //Restem la quantitat de la compra a quantitat_disponible de l'article
        $quantitat = $compra['quantitat'];
        $fk_article_articles = $compra['fk_article_articles'];
        
        $sql_update = "UPDATE articles SET quantitat_disponible = quantitat_disponible + :quantitat WHERE id_article = :id_article";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->bindParam(':quantitat', $quantitat, PDO::PARAM_INT);
        $stmt_update->bindParam(':id_article', $fk_article_articles, PDO::PARAM_INT);
        $stmt_update->execute();

        //Eliminar la compra de la taula compres
        $sql_delete = "DELETE FROM compres WHERE id_compra = :id_compra";
        $stmt_delete = $pdo->prepare($sql_delete);
        $stmt_delete->bindParam(':id_compra', $id_compra, PDO::PARAM_INT);
        $stmt_delete->execute();


        //Porte'm l'encert corresponent a la variables de sempre
        $_SESSION['missatgeCorrecte'] = "Compra eliminada correctament.";
    } else {
         //Porte'm l'error corresponent a la variables de sempre 
        $_SESSION['missatgeError'] = "Error en eliminar la compra: " . $e->getMessage();
    }
} catch (PDOException $e) {
    //Porte'm l'error corresponent a la variables de sempre 
    $_SESSION['missatgeError'] = "Error en eliminar la compra: " . $e->getMessage();
}
// Redirigir a vista.veureCompres.php
header("Location: ../vista/vista.veureCompres.php");
exit();
?>