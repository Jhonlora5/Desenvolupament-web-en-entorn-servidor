<?php
require 'controlador/cont.connexio.php';

function obtenirCompres($pdo, $per_page = 10) {
    // Comprovem si l'usuari ha iniciat sessió
    
    if (!isset($_SESSION['usuari_id'])) {
        header('Location: vista.formularilogin.php');
        exit();
    }

    // Determinar la pàgina actual
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $start = ($page - 1) * $per_page;

    // Consulta per recuperar les compres amb unió per obtenir el nom de l'article
    $stmt = $pdo->prepare("SELECT c.id_compra, a.nom AS nom_article, c.quantitat, c.preu_total, c.data_compra 
                           FROM compres c
                           JOIN articles a ON c.fk_article_articles = a.id_article
                           WHERE c.fk_usuari_usuaris = :usuari_id
                           ORDER BY c.data_compra DESC
                           LIMIT :start, :per_page");
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':per_page', $per_page, PDO::PARAM_INT);
    $stmt->bindValue(':usuari_id', $_SESSION['usuari_id'], PDO::PARAM_INT);
    $stmt->execute();
    $compres = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Recuperar el nombre total de compres per calcular quantes pàgines calen
    $total_stmt = $pdo->prepare("SELECT COUNT(*) FROM compres WHERE fk_usuari_usuaris = :usuari_id");
    $total_stmt->bindValue(':usuari_id', $_SESSION['usuari_id'], PDO::PARAM_INT);
    $total_stmt->execute();
    $total_compres = $total_stmt->fetchColumn();
    $total_pages = ceil($total_compres / $per_page);

    return [
        'compres' => $compres,
        'total_pages' => $total_pages,
        'page' => $page
    ];
}
?>