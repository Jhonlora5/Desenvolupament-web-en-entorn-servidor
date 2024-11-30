<!--Jonathan Lopez Ramos-->
<?php
require_once '../model/model.processar.php';
//Processar el formulari depenent de l'acció seleccionada
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['form_insercio_modificacio'])) {
    $accio = $_POST['accio'] ?? '';
    $missatgeError = '';
    $missatgeCorrecte = '';
    $buscar = $_POST['buscar'];

    //Guardar les dades del formulari
    $nom = $_POST['nom'] ?? '';
    $cos = $_POST['cos'] ?? '';
    $id_article = $_POST['id_article'] ?? '';

    //Validar que almenys un camp no estigui buit depenent de l'acció
    //l'accio update correspon a modificar.
    //l'accio delete a esborrar
    //tant per modificar com per a esborrar es necessari si o si la id de l'article, els altres poden o no estar.
    if (($accio == 'update' || $accio == 'delete') && empty($id_article)) {
        $missatgeError = "L'ID d'article és obligatori per actualitzar o esborrar.";
    }

    //Validacions específiques per 'insert' o 'update'
    //Si els missatges d'error no hi ha res i si o si tens una accio insert o delete.
    if (empty($missatgeError) && ($accio == 'insert' || $accio == 'update')) {
        //Revisa la variable nom per tal de trobar si te o no un numero, pero deixa passar caracters com · ' espais...
        if (!preg_match('/^[\p{L}·\'\s\d,.!?]+$/u', $nom)) {
            $missatgeError = "El nom només pot contenir lletres.";
            //Revisa la variable cos per tal de trobar si te o no un numero, pero deixa passar caracters com · ' espais...
        } elseif (!preg_match('/^[\p{L}·\'\s\d,.!?]+$/u', $cos)) {
            $missatgeError = "El cos només pot contenir lletres.";
        }
    }

    //Comprovem si l'ID és numèric només si estem modificar o esborrar (update or delete)
    //Si la variable $missatgeError i si o si tens l'acció update or delte i no falta l'article
    if (empty($missatgeError) && ($accio == 'update' || $accio == 'delete') && !empty($id_article)) {
        //Mira si l'article no es numeric.
        if (!is_numeric($id_article)) {
            $missatgeError = "L'ID d'article ha de ser un número.";
            //per verificar si l'article existeix tornem a cridar a la funcio existeixArticlePerId.
        } elseif (!existeixArticlePerId($id_article)) {
            $missatgeError = "No existeix cap article amb l'ID '$id_article'.";
        }
    }

    //Aquesta part s'ecarrega d'agafar els errors o encerts corresponents a les funcions i la variable missatgeError.
    //Si troba errors
    if (empty($missatgeError)) {
        //Si l'accio es insert
        if ($accio == 'insert') {
            //Agafa el resultat de la funcio que en realitat agafa les dades de l'errors o encerts que tenim a la funció
            $resultat = inserirArticle($nom, $cos);
            if (is_array($resultat) && isset($resultat['error'])) {
                //Fica dins de la variable que tornem a principi del vista.formulari.php
                $missatgeError = $resultat['error'];
            } else {
                //Si no exiteix error fica l'encert a la variable missatgecorrecte.
                $missatgeCorrecte = $resultat;
            }
            //Si l'accio es update
        } elseif ($accio == 'update') {
            //Agafa el resultat de la funcio que en realitat agafa les dades de l'errors o encerts que tenim a la funció.
            $resultat = modificarArticle($id_article, $nom, $cos);
            //Mira si existeix error i afegeix-lo a la variable
            if (isset($resultat['error'])) {
                $missatgeError = $resultat['error'];
            } else {
                //Si es correcte la variable amb la posició aconseguit guada la dada a la variable o array missatgeCorrecte.
                $missatgeCorrecte = $resultat['aconseguit'];
            }
            //Si l'acció es delete
        } elseif ($accio == 'delete') {
            //Agafa el resultat de la funcio que en realitat agafa les dades de l'errors o encerts que tenim a la funció.
            $resultat = esborrarArticle($id_article);
            //Mira si existeix "error" i afegeix-lo a la variable
            if (isset($resultat['error'])) {
                $missatgeError = $resultat['error'];
            } else {
                //Si no troba res, afegeix "aconseguit" guada la dada a la variable o array missatgeCorrecte.
                $missatgeCorrecte = $resultat['aconseguit'];
            }
        }
    }
    //Obtenir la pàgina actual del formulari
    $paginaActual = isset($_POST['pagina']) ? (int)$_POST['pagina'] : 1;

    //Guardar els missatges d'error o "correctes" a la sessión per tal de recollir-ho des de vista.formulari.php
    $_SESSION['missatgeError'] = $missatgeError;
    $_SESSION['missatgeCorrecte'] = $missatgeCorrecte;
    //Redirigir de nou a vista.formulari.php el numero de pàgina, que utilitzare'm per fer el calcul de que mostrar a la seguent.
    header("Location: ../vista/vista.formulari.php?pagina=$paginaActual");
    exit();
}
?>