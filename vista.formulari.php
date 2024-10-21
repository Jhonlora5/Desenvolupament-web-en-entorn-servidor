<!--Jonathan Lopez Ramos-->
<?php
// cridem al document que s'encarrega de processar les dades(Funcions i de més)
require 'controlador/cont.processar.php';
session_start();


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
    //Carrega els articles per pàgina
    $articles = obtenirArticles($paginaActual, $articlesPerPagina);
    //Depuració temporal per veure què s'està retornant
    //var_dump($articles); // Aquest codi imprimeix el resultat de la consulta a la pantalla
    //exit; // Afegeix exit per veure només la informació i evitar que es carregui el formulari



//CONTROL MISSATGES D'ERRORS
    //Recuperacio de dos missatges, un d'error i un altre de missatges correctes, on es càrregara a la variable el missatge que arriba del fitcher processar.php
    $missatgeError = isset($_SESSION['missatgeError']) ? $_SESSION['missatgeError'] : '';
    $missatgeCorrecte = isset($_SESSION['missatgeCorrecte']) ? $_SESSION['missatgeCorrecte'] : '';

    //Una vegada carregat a la variable esborrem les dades que tenim al $_SESSION per tal de que si recarreguem de nou no carregui una dada anterior.
    unset($_SESSION['missatgeError'], $_SESSION['missatgeCorrecte']);

    print_r($_SESSION['nom_usuari']);
   
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestió d'Articles</title>
    <link rel="stylesheet" href="/css/estils.css">
    
    <script>
        //Funció per canviar dinàmicament els camps del formulari segons l'opció seleccionada amb inputs de tipe radio.
        function mostrarFormulari() {                       
            //Creem la variable que accio despprés farem les equivalencies per tal d'esborrar o no la part del formulari corresponenet.
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
        <form method="POST" action="model/cont.logout.php"class="form-logout">
            <button type="submit" class="logout-button">Logout</button>
            <div class="veure_compres">    
                <a href="vista.veureCompres.php">Compres realitzades</a>
            </div>
        </form>
    </div> 
<div class="container">       
    <h1>Gestió d'Articles</h1>
    <!-- Afegim els missatges d'errors amb la variale creada anteriorment, que ens porta l'error desde el document processar.php-->
    <?php if (!empty($missatgeError)): ?>        
        <h1 class="message" style="color: red;">
            <!--Si el missatge rebut es un array mostra els diferents errors, si no, mostra el missatge-->
            <?php echo is_array($missatgeError) ? htmlspecialchars($missatgeError['error']) : htmlspecialchars($missatgeError); ?>
        </h1>
    <?php endif; ?>
    <!--Mateix funcionament que l'anterior pero per els missatges "correctes"-->
    <?php if (!empty($missatgeCorrecte)): ?>
        <h1 class="message" style="color: blue;">
            <?php echo is_array($missatgeCorrecte) ? htmlspecialchars($missatgeCorrecte['aconseguit']) : htmlspecialchars($missatgeCorrecte); ?>
        </h1>
        
    <?php endif; ?>

    <!--Creacio dels botons per el formulari dinamic-->
    <form method="POST" action="controlador/cont.processar.php" style="display: block">     
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

            <button class="enviardades" type="submit">Enviar</button>
        </form>

            <!--Generació de la taula d'articles existents-->
            <h2>Llista d'Articles Existents</h2>
            <?php if (count($articles) > 0): ?>
                <table class="table" style="width: 100%; border-collapse: collapse;">
                    <tbody>
                        <?php foreach ($articles as $article): ?>
                            <tr>
                                <!-- Primera columna: ID, Nom i Descripció -->
                                <td style="padding: 10px; border: 1px solid #ccc; width: 70%;">
                                    <strong>ID:</strong> <?php echo htmlspecialchars($article['id_article']); ?><br>
                                    <strong>Nom:</strong> <?php echo htmlspecialchars($article['nom']); ?><br>
                                    <strong>Descripció:</strong> <?php echo htmlspecialchars($article['cos']); ?>
                                </td>
                                <!-- Segona columna: Imatge -->
                                <td style="padding: 10px; border: 1px solid #ccc; width: 30%; text-align: center;">
                                    <img src="<?php echo htmlspecialchars($article['img_path']); ?>" alt="Imatge de l'article" style="max-width: 150px; height: auto;">
                                </td>
                                <td style="padding: 10px; border: 1px solid #ccc; width: 30%;">
                                    <form method="POST" action="controlador/cont.compres_usuari.php" style="display: block">
                                        <input type="hidden" name="id_article" value="<?php echo htmlspecialchars($article['id_article']); ?>">
                                        <input type="hidden" name="id_usuari" value="<?php echo htmlspecialchars($_SESSION['id_usuari']); ?>">
                                        <?php print_r($article['id_article']."idarticulo")?>
                                        <?php print_r($_SESSION['id_usuari']."id_usuario")?>
                                        <?php print_r($_SESSION['nom_usuari']."nombre_usuario")?>
                                        <label for="quantitat">Quantitat:</label>                                        
                                        <input type="number" name="quantitat" min="1" max="<?php echo htmlspecialchars($article['quantitat_disponible']); ?>" required>
                                        <button type="submit">Comprar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
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








