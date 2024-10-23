<?php
//Jonathan Lopez Ramos
// Inclou la connexió a la base de dades
require 'cont.connexio.php';
$pdo = obtenirConnexio();

session_start();

function processarCompra($id_article, $quantitat, $id_usuari) {
    global $pdo;

    // Missatges d'error o èxit que retornarem
    $resposta = [];

    // Comprovar si totes les dades necessàries estan presents
    if (!$id_article || !$quantitat || !$id_usuari) {
        $resposta['error'] = "Dades incompletes. Assegura't d'introduir l'article i la quantitat.". $id_article ."hola " . $quantitat ."adeu". $id_usuari;
        return $resposta;
    }

    // Comprovar si la quantitat és vàlida
    if ($quantitat <= 0) {
        $resposta['error'] = "La quantitat ha de ser més gran que zero.";
        return $resposta;
    }

    try {
        // Obtenir informació de l'article per comprovar disponibilitat
        $stmt = $pdo->prepare("SELECT preu, quantitat_disponible FROM articles WHERE id_article = :id_article");
        $stmt->bindParam(':id_article', $id_article, PDO::PARAM_INT);
        $stmt->execute();
        $article = $stmt->fetch(PDO::FETCH_ASSOC);

        // Comprovar si l'article existeix i si hi ha suficient quantitat disponible
        if (!$article) {
            $resposta['error'] = "L'article amb ID $id_article no existeix.";
            return $resposta;
        }

        if ($article['quantitat_disponible'] < $quantitat) {
            $resposta['error'] = "No hi ha prou quantitat disponible de l'article.";
            return $resposta;
        }

        // Tot està correcte: procedim a la inserció
        $preu_total = $article['preu'] * $quantitat;

        // Iniciar la transacció
        $pdo->beginTransaction();

        // Inserir la compra a la taula `compres`
        $stmt = $pdo->prepare("INSERT INTO compres (fk_article_articles, fk_usuari_usuaris, quantitat, preu_total, data_compra) VALUES (:id_article, :id_usuari, :quantitat, :preu_total, NOW())");
        $stmt->bindParam(':id_article', $id_article, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuari', $id_usuari, PDO::PARAM_INT);
        $stmt->bindParam(':quantitat', $quantitat, PDO::PARAM_INT);
        $stmt->bindParam(':preu_total', $preu_total, PDO::PARAM_INT);
        $stmt->execute();

        // Actualitzar la quantitat disponible de l'article
        $nova_quantitat_disponible = $article['quantitat_disponible'] - $quantitat;
        $stmt = $pdo->prepare("UPDATE articles SET quantitat_disponible = :nova_quantitat WHERE id_article = :id_article");
        $stmt->bindParam(':nova_quantitat', $nova_quantitat_disponible, PDO::PARAM_INT);
        $stmt->bindParam(':id_article', $id_article, PDO::PARAM_INT);
        $stmt->execute();

        // Confirmar la transacció
        $pdo->commit();

        // Si tot va bé, retornem un missatge d'èxit
        $resposta['aconseguit'] = "Compra realitzada correctament. Preu total: $preu_total €";
    } catch (PDOException $e) {
        // En cas d'error, cancel·lar la transacció
        $pdo->rollBack();
        $resposta['error'] = "Error en processar la compra: " . $e->getMessage();
    }

    return $resposta;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Recull les dades del formulari
    $id_article = isset($_POST['id_article']) ? (int)$_POST['id_article'] : null;
    $quantitat = isset($_POST['quantitat']) ? (int)$_POST['quantitat'] : null;
    $id_usuari = $_SESSION['usuari_id'];  // Suposem que l'ID de l'usuari està a la sessió

    // Crida la funció per processar la compra
    $resultat = processarCompra($id_article, $quantitat, $id_usuari);

    // Si hi ha errors, guarda'ls a la sessió
    if (isset($resultat['error'])) {
        $_SESSION['missatgeError'] = $resultat['error'];
    }

    // Si s'ha aconseguit, guarda el missatge de confirmació
    if (isset($resultat['aconseguit'])) {
        $_SESSION['missatgeCorrecte'] = $resultat['aconseguit'];
    }

    // Redirigir a la pàgina anterior
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>