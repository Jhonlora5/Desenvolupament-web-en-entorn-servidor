<?php
//session_start();
//Cridem al document processar per tal de que en un inici, 
// empleni els articles corresponents(aquest conté la crida a la connexio a la base de dades i a mes conté startsession).
require 'cont.processar.php';
//Cridem a la funcio corresponent per realitzar la connexio a la base de dades.
$pdo = obtenirConnexio();
//Configuracio de l'estat de l'ordenació i cerca amb variables de sessió
$ordre = $_SESSION['ordre'] ?? 'ASC'; //Ascendent per defecte
$paginaActual = $_SESSION['paginaActual'] ?? 1;
$articlesPerPagina = $_SESSION['articlesPerPagina'] ?? 5;
$nomCerca = $_SESSION['nomCerca'] ?? ''; //Cerca buida per defecte


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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && (isset($_POST['nomCerca']) || isset($_POST['ordre']))){
    
    $ordre = $_POST['ordre'];
    $_SESSION['ordre'] = $ordre; //Guarda l'ordre a la sessió
    
    //Altres captures i validacions (p. ex., nomCerca, pàgina)
    $nomCerca = $_POST['nomCerca'] ?? '';
    $paginaActual = isset($_POST['pagina']) ? (int)$_POST['pagina'] : 1;

    //Guarda `nomCerca` a la sessió
    $_SESSION['nomCerca'] = $nomCerca;
    
    //Captura de la pàgina actual.
    $_SESSION['paginaActual'] = $paginaActual;
    $paginaActual = $_SESSION['paginaActual'] ?? 1;
    
    //Obtenir la pàgina actual del formulari
    $paginaActual = isset($_POST['pagina']) ? (int)$_POST['pagina'] : 1;

    //Redirigir de nou a vista.formulari.php el numero de pàgina, que utilitzare'm per fer el calcul de que mostrar a la seguent.
    header("Location: ../vista/vista.formulari.php?pagina=$paginaActual");
    exit();
}

?>