<!--Jonathan Lopez Ramos-->
<?php

// cridem al document que s'encarrega de processar les dades(Funcions i la connexio a la base de dades)
require '../controlador/cont.articles.php';

$articles = [];
//PAGINACIÓ
    //Variables de control de la paginació
    //Quants articles vols mostrar per pàgina
    $articlesPerPagina = 5; 
    //Capturar la pàgina actual del formulari o de la URL
    $paginaActual = max(1, isset($_POST['pagina']) ? (int)$_POST['pagina'] : (isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1));
    //Depuració temporal
    //echo "Pàgina actual: $paginaActual"; 
    //Capturar l'última ID mostrada
    $ultimaIdMostrada = isset($_POST['ultima_id']) ? (int)$_POST['ultima_id'] : 0;
    //Calcula el total de pàgines
    $totalPagines = obtenirTotalPagines($articlesPerPagina);
    //Creació de la variable de sessio encarregada de la cerc per article
    $nomCerca = $_SESSION['nomCerca'] ?? '';
    //Elimina `nomCerca` de la sessió després de recuperar-lo
    unset($_SESSION['nomCerca']); 
    //Carrega els articles per pàgina
    //Decideix si es fa una cerca d'articles per ordre o s'obtenen articles ordenats
if (!empty($nomCerca)) {
    $articles = buscarArticles($nomCerca);
} else {
    $articles = obtenirArticlesOrdenats($ordre, $paginaActual, $articlesPerPagina);
}
    //Depuració temporal per veure què s'està retornant
    //var_dump($articles); // Aquest codi imprimeix el resultat de la consulta a la pantalla
    //exit; // Afegeix exit per veure només la informació i evitar que es carregui el formulari
   
//CONTROL MISSATGES D'ERRORS
    //Recuperacio de dos missatges, un d'error i un altre de missatges correctes, on es càrregara a la variable el missatge que arriba del fitcher processar.php
    $missatgeError = isset($_SESSION['missatgeError']) ? $_SESSION['missatgeError'] : '';
    $missatgeCorrecte = isset($_SESSION['missatgeCorrecte']) ? $_SESSION['missatgeCorrecte'] : '';

    //Una vegada carregat a la variable esborrem les dades que tenim al $_SESSION per tal de que si recarreguem de nou no carregui una dada anterior.
    unset($_SESSION['missatgeError'], $_SESSION['missatgeCorrecte']);

    //print_r($_SESSION['nom_usuari']);
    if (!isset($_SESSION['usuari_id'])) {
        header('Location: vista.formulariLogin.php');
        $missatgeError = "No pots accedir aquest arxiu si no estas logat.";
        exit();
    }
    //print_r($_SESSION['usuari_id']);
    //Si es vol revisar la variable que controla l'administracio de dades.
    //if (isset($_SESSION['nivell_administrador'])) {
    //    echo "Nivell d'administrador: " . htmlspecialchars($_SESSION['nivell_administrador']);
    //} else {
    //    echo "La variable 'nivell_administrador' no està definida.";
    //}
// Mostrar la cookie associada al "recorda'm"
//echo '<pre>Cookie recorda_token: ';
//print_r($_COOKIE['recorda_token'] ?? 'No existeix');
//echo '</pre>';

//Mostrar el contingut de la sessió
//echo '<pre>Sessió actual: ';
//print_r($_SESSION);
//echo '</pre>';
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestió d'Articles</title>
    <link rel="stylesheet" href="../css/estils.css">
    
    <script>
        //Funció per canviar dinàmicament els camps del formulari segons l'opció seleccionada amb inputs de tipe radio.
        function mostrarFormulari() {                       
            //Creem la variable accio que despprés farem les equivalencies per tal d'esborrar o no la part del formulari corresponenet.
            //Això arriba depenguent de l'acció radio que tenim clicada.
            //El valor chequed pot ser: insert, update o delete.
            let accio = document.querySelector('input[name="accio"]:checked').value;                
            //Mostrar o ocultar camps segons l'acció seleccionada
            //Agafem la ID encarregada de generar la capça de radio
            //Establim un display:none on apagar o no cada capça de les dades
            //Si la opcio es Modificar(update) o eliminar(delete) la capça del id es mostrarà, del contrari s'ocultarà.
            document.getElementById('id_article_group').style.display = (accio === 'update' || accio === 'delete') ? 'block' : 'none';
            //Aquest dos cams es mostraran quan l'accio del radio sigui insertar o modificar
            document.getElementById('nom_group').style.display = (accio === 'insert' || accio === 'update') ? 'block' : 'none';
            document.getElementById('cos_group').style.display = (accio === 'insert' || accio === 'update') ? 'block' : 'none';                  
        }
    </script>
</head>
<body>
    <!-- Finestra de Logout -->
    <div class="logout-container">
        <h2>Usuari Actiu</h2>
        <p><?php echo htmlspecialchars($_SESSION['nom_usuari']); ?></p>
        <form method="POST" action="/controlador/cont.logout.php"class="form-logout">
            <button type="submit" class="logout-button">Logout</button>            
        </form>
        
        <!-- Afegim els missatges d'errors amb la variale creada anteriorment, que ens porta l'error desde el document processar.php-->
        <?php if (!empty($missatgeError)): ?>        
            <h4 class="message" style="color: red;">
                <!--Si el missatge rebut es un array mostra els diferents errors, si no, mostra el missatge-->
                <?php echo is_array($missatgeError) ? htmlspecialchars($missatgeError['error']) : htmlspecialchars($missatgeError); ?>
            </h4>
        <?php endif; ?>
        <!--Mateix funcionament que l'anterior pero per els missatges "correctes"-->
        <?php if (!empty($missatgeCorrecte)): ?>
            <h4 class="message" style="color: blue;">
                <?php echo is_array($missatgeCorrecte) ? htmlspecialchars($missatgeCorrecte['aconseguit']) : htmlspecialchars($missatgeCorrecte); ?>
            </h4>       
        <?php endif; ?>
        
        <div class="veure_compres">    
            <a href="/vista/vista.veureCompres.php">Compres realitzades</a>
        </div>       
    </div>

<div class="container">       
    <h1>Gestió d'Articles</h1>    
    <!--Creacio dels botons per el formulari dinamic-->
    <form method="POST" action="/controlador/cont.processar.php" style="display: block">     
        <h2>Seleccioneu l'acció</h2>
            <!--Realitzem la crida de la funció on enviem a més totes les dades, cheked final es per posar per defecte al entrar a la web-->
            <label><input title="Aquest camp serveix per inserir articles" type="radio" name="accio" value="insert" onclick="mostrarFormulari()" checked> Inserir</label>
            <label><input title="Aquest camp serveix per modificar articles"type="radio" name="accio" value="update" onclick="mostrarFormulari()"> Modificar</label>
            <label><input title="Aquest camp serveix per esborrar articles" type="radio" name="accio" value="delete" onclick="mostrarFormulari()"> Esborrar</label>
            
            <!--Camp ID d'article (només per modificar i esborrar)-->
            <div id="id_article_group" style="display:none;">
                <label for="id_article">ID de l'article:</label>
                <input type="text" name="id_article" id="id_article" placeholder="Escriu l'identificador de l'article">
            </div>

            <!--Camp nom(només per inserir i modificar)-->
            <div id="nom_group">
                <label for="nom">Nom:</label>
                <input type="text" name="nom" id="nom" placeholder="Escriu el nom de l'article">
            </div>

            <!--Camp Cos (només per inserir i modificar)-->
            <div id="cos_group">
                <label for="cos">Descripció:</label>
                <textarea name="cos" id="cos" rows="4" placeholder="Escriu la descripció de l'article"></textarea>
            </div>
            <button class="enviardades" type="submit" name="form_insercio_modificacio">Enviar</button>
            <!--Establecer nombre a los botones i en sus paginas correspondientes processar i articles hacer un or or algo para que entre o no.-->
    </form>
    
    <!--Formulari encarregat de cercar articles per nom o ordre ascendent/descendent-->
    <form method="POST" action="/controlador/cont.articles.php" style="display: block">
        <label for="nomCerca">Buscar article:</label>
        <input type="text" id="nomCerca" name="nomCerca" placeholder="Nom de l'article" value="<?php echo $_SESSION['nomCerca'] ?? ''; ?>">
        <button type="submit">Buscar</button>

        <label for="ordre">Ordenar:</label>
        <button type="submit" name="ordre" value="ASC">Ascendent</button>
        <button type="submit" name="ordre" value="DESC">Descendent</button>
    </form>    
            <!--Mirar l'ordre si l'ordre es carrega de forma adecuada-->
        <!--Generació de la taula d'articles existents-->
        <h2>Llista d'Articles Existents</h2>
        <?php if (count($articles) > 0): ?>
            <table class="table" style="width: 100%; border-collapse: collapse;">               
                <?php foreach ($articles as $article): ?>
                    <tr>
                        <!-- Primera columna: ID, Nom i Descripció -->
                        <td style="padding: 10px; border: 1px solid #ccc; width: 70%;">
                            <strong>ID:</strong> <?php echo htmlspecialchars($article['id_article']); ?><br>
                            <strong>Nom:</strong> <?php echo htmlspecialchars($article['nom']); ?><br>
                            <strong>Descripció:</strong> <?php echo htmlspecialchars($article['cos']); ?><br>
                            <strong>Unitats a Magatzem:</strong> <?php echo htmlspecialchars($article['quantitat_disponible']);?><br>
                            <strong>Preu:</strong> <?php echo htmlspecialchars($article['preu']);?> 
                        </td>
                        <!-- Segona columna: Imatge -->
                        <td style="padding: 10px; border: 1px solid #ccc; width: 30%; text-align: center;">
                            <img src="<?php echo htmlspecialchars($article['img_path']); ?>" alt="Imatge de l'article" style="max-width: 150px; height: auto;">
                        </td>
                        <td style="padding: 10px; border: 1px solid #ccc; width: 30%;">
                            <form method="POST" action="/controlador/cont.compres_usuari.php" style="display: block">

                                <input type="hidden" name="id_article" value="<?php echo htmlspecialchars($article['id_article']); ?>">
                                <input type="hidden" name="id_usuari" value="<?php echo htmlspecialchars($_SESSION['usuari_id']); ?>">
                                            
                                <label for="quantitat">Comprar/Unitats:</label>                                        
                                <input type="number" name="quantitat" min="1" max="<?php echo htmlspecialchars($article['quantitat_disponible']); ?>" required>
                                <button type="submit">Comprar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>                
            </table>
            <?php else: ?>
                <p>No hi ha articles disponibles.</p>
            <?php endif; ?>
                <!-- Paginació a veure que surt...-->
                <div class="paginacio">
                    <form method="POST" action="vista.formulari.php" >
                        <?php
                        //Obtenir la ID de l'últim article mostrat
                        $ultimaIdMostrada = end($articles)['id_article'];
                        ?>
                        <!-- Botó de pàgina anterior (amb desactivació si estàs a la primera pàgina) -->
                        <button class="anterior" type="submit" name="pagina" value="<?php echo max(1, $paginaActual - 1); ?>" <?php echo $paginaActual == 1 ? 'disabled' : ''; ?>>&laquo; Anterior</button>
                        <!-- Botons per a cada pàgina -->
                        <input type="hidden" name="ultima_id" value="<?php echo $ultimaIdMostrada; ?>">
                        <!-- Començem el bucle encarregat de mostrar els articles a cada pàgina-->
                        <?php for ($i = 1; $i <= $totalPagines; $i++): ?>
                            <button class="numero" type="submit" name="pagina" value="<?php echo $i; ?>" <?php echo $i == $paginaActual ? 'class="active"' : ''; ?>>
                        <?php echo $i; ?>
                            </button>
                        <?php endfor; ?>

                        <!-- Botó de pàgina següent -->
                    <button class="seguent" type="submit" name="pagina" value="<?php echo min($totalPagines, $paginaActual + 1); ?>">Següent &raquo;</button>
                </form>
            </div>     
        </div>        
    </body>
</html>