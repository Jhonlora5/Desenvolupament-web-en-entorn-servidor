<?php
//Funció per obtenir articles en ordre ascendent o descendent, amb paginació
function obtenirArticlesOrdenats($ordre, $paginaActual, $articlesPerPagina) {
    global $pdo;
    try {
        $offset = ($paginaActual - 1) * $articlesPerPagina;
        $ordreSQL = ($ordre === 'DESC') ? 'DESC' : 'ASC';
        $stmt = $pdo->prepare("SELECT id_article, nom, cos, quantitat_disponible, preu, img_path FROM articles ORDER BY nom $ordreSQL LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $articlesPerPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $resultat;
    
    } catch (PDOException $e) {
        //Enviem l'error corresponent al nom de la funció
        return ['error' => "Error a la funció obtenirArticlesOrdenats: " . $e->getMessage()];
    }
}

//Funció per buscar un article pel nom
function buscarArticles($nomCerca) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT id_article, nom, cos, quantitat_disponible, preu, img_path FROM articles WHERE nom LIKE :nom");
        $nomCerca = "%$nomCerca%";  //Cerca articles que continguin el text
        $stmt->bindParam(':nom', $nomCerca);
        $stmt->execute();
        // Comprovació dels resultats
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        

    } catch (PDOException $e) {
        //Enviem l'error corresponent al nom de la funcio
        return ['error' => "Error a la funció buscarArticles: " . $e->getMessage()];
    }
    
}
?>