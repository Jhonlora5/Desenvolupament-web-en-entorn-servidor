<?php
require_once '../controlador/cont.connexio.php';

$pdo = obtenirConnexio();

//Aquesta funcio es crida a l'entrar al vista.formulari.php per recollir les dades si existeixen.
//Funció per obtenir articles amb paginació
function obtenirArticles($paginaActual, $articlesPerPagina) {
    global $pdo;
    try{
        //Calcular el límit i el desplaçament per a la consulta.
        $offset = ($paginaActual - 1) * $articlesPerPagina;

        //Preparar la consulta per obtenir els articles amb paginació, depen del numero d'articles mostrats 5, 
        $stmt = $pdo->prepare("SELECT id_article, nom, cos, quantitat_disponible, preu, img_path FROM articles LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $articlesPerPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        //Retornar els articles com a un array
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }catch(PDOException $e){
        return ['error'=> "No s'aconseguit finalitzar correctement la funció obtenirArticles" . $e->getMessage()];
    }    
}

//Funció per obtenir el nombre total d'articles
function obtenirTotalArticles() {
    global $pdo;
    try{
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM articles");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }catch(PDOException $e){
        return ['error'=> "No s'aconseguit finalitzar correctement la funció obtenirTotalArticles" . $e->getMessage()];
    }    
}

//Funcio per mirar el total de pagines que poden haver.
function obtenirTotalPagines($articlesPerPagina) {
    try{
        $totalArticles = obtenirTotalArticles(); // Funció que ja tens creada per obtenir el total d'articles
        //Calcular el nombre total de pàgines, ceil s'encarrega de arrodonir a l'alça.
        return ceil($totalArticles / $articlesPerPagina);
    }catch(PDOException $e){
        return ['error'=> "No s'aconseguit finalitzar correctement la funció obtenirTotalPagines" . $e->getMessage()];
    }    
}

//Aquesta funció comproba si existeix o no un article per el seu nom
function existeixArticle($nom) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE nom = :nom");
        $stmt->bindParam(':nom', $nom);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        return $count > 0; //Retorna true si l'article ja existeix o false si el numero es 0.
    } catch (PDOException $e) {
        //retorna la variable o array amb l'error que s'escriu a continuacio.
        return ['error'=> "No s'aconseguit finalitzar correctement la funció existeixArticle" . $e->getMessage()];
    }
}

//Aquesta funcio s'encarrega d'inserir les dades si son correctes.
function inserirArticle($nom, $cos) {
    global $pdo;
    if (!empty($nom) && !empty($cos)) {
        //Comprovació si l'article ja existeix
        if (existeixArticle($nom)) {
            return [ 'error' => "L'article amb el nom '$nom' ja existeix."];
        }
        try {
            $stmt = $pdo->prepare("INSERT INTO articles (nom, cos) VALUES (:nom, :cos)");
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':cos', $cos);
            $stmt->execute();
            //retorna la variable o array amb l'aconseguit que s'escriu a continuacio.
            return [ 'aconseguit' =>"Article inserit correctament!"];
        } catch (PDOException $e) {
            return ['error'=> "Error a la funció inserirArticle" . $e->getMessage()];
        }
    }
    return [ 'error' =>"Has d'introduïr tots els camps!"];
}

//Funció per comprovar si es troba un article per l'ID
function existeixArticlePerId($id_article) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE id_article = :id_article");
        $stmt->bindParam(':id_article', $id_article);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        return $count > 0; //Retorna true si l'article existeix
    } catch (PDOException $e) {
        return [ 'error' => "Error a la funció existeixArticlePerId" . $e->getMessage()];
    }
}

//Aquesta es la funció encarregada de modificar un article existent.
function modificarArticle($id_article, $nom, $cos) {
    global $pdo;
    if (!empty($id_article) && !empty($nom) && !empty($cos)) {
        //Utilitzem la funcio existeixArticlePerId per tal de comprovar si existeix o no.
        if (!existeixArticlePerId($id_article)) {
            //Retornem l'error amb la ID corresponent de l'article per que no s'ha trobat.
            return [ 'error' => "No existeix cap article amb l'ID '$id_article'."];
        }
        if (!existeixArticle($nom)){
            //Returnem l'error en aquest cas amb el nom corresponent si l'article no existeix.
            return [ 'error' => "No existeix cap article amb el nom '$nom'."];
            
        }
        try {
            $stmt = $pdo->prepare("UPDATE articles SET nom = :nom, cos = :cos WHERE id_article = :id_article");
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':cos', $cos);
            $stmt->bindParam(':id_article', $id_article);
            $stmt->execute();
            //Retornem l'encert per tal de mostrar-ho a pantalla.
            return [ 'aconseguit' => "Article actualitzat correctament!"];
        } catch (PDOException $e) {
            //Retornem l'error de la funció corresponent.
            return [ 'error' => "Error a la funció modificarArticle" . $e->getMessage()];
        }
    }
    //Retornem l'error de que no estan tots els camps emplenats.
    return [ 'error' => "Tots els camps són obligatoris!"];
}

//Funcio encarregada d'esborrar un article.
function esborrarArticle($id_article) {
    global $pdo;
    if (!empty($id_article)) {
        //Comprovació si l'article amb l'ID existeix cridant a la funcio corresponent existeixArticlePerId
        if (!existeixArticlePerId($id_article)) {
            return [ 'error' => "No existeix cap article amb l'ID '$id_article'."];
        }
        try {
            $stmt = $pdo->prepare("DELETE FROM articles WHERE id_article = :id_article");
            $stmt->bindParam(':id_article', $id_article);
            $stmt->execute();
            //Retornem l'encert per tal de mostrar-ho a pantalla.
            return [ 'aconseguit' => "Article eliminat correctament!"];
        } catch (PDOException $e) {
            //Retornem l'error si la funció no funciona amb el seu nom.
            return [ 'error' => "Error a la funció esborrarArticle " . $e->getMessage()];
        }
    }
    //Retornem l'error corresponent si la id de l'article no es troba a la casella.
    return [ 'error' => "L'ID d'article és obligatori!"];
}

?>