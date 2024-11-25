<?php
function obtenirCompraPerId($pdo, $id_compra) {
    $sql_select = "SELECT quantitat, fk_article_articles FROM compres WHERE id_compra = :id_compra";
    $stmt_select = $pdo->prepare($sql_select);
    $stmt_select->bindParam(':id_compra', $id_compra, PDO::PARAM_INT);
    $stmt_select->execute();
    return $stmt_select->fetch(PDO::FETCH_ASSOC);
}

function actualitzarQuantitatArticle($pdo, $id_article, $quantitat) {
    $sql_update = "UPDATE articles SET quantitat_disponible = quantitat_disponible + :quantitat WHERE id_article = :id_article";
    $stmt_update = $pdo->prepare($sql_update);
    $stmt_update->bindParam(':quantitat', $quantitat, PDO::PARAM_INT);
    $stmt_update->bindParam(':id_article', $id_article, PDO::PARAM_INT);
    $stmt_update->execute();
}

function eliminarCompra($pdo, $id_compra) {
    $sql_delete = "DELETE FROM compres WHERE id_compra = :id_compra";
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->bindParam(':id_compra', $id_compra, PDO::PARAM_INT);
    $stmt_delete->execute();
}
?>
