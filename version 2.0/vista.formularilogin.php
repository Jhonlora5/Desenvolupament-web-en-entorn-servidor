<?php
/* Jonathan Lopez Ramos */
//Cridem a cont.processar.php que conté la connexió a la base de dades.
//Aquesta crida és necessaria per tal de mostrar els articles amb les mateixes funcions que ja tenim a l'arxiu.
require 'controlador/cont.processar.php';


//Verificar si hi ha algun error en la connexió per la crida a cont.processar.php que ja conté la crida a cont.connexio.php
if (is_array($pdo) && isset($pdo['error'])) {
    //Mostrar error en cas de fallada de connexió(Rectificacio d'errors).
    die($pdo['error']);  
}
//COMENÇAMENT DELS ARTICLES A MOSTRAR. 
    //Realitzar la paginació: definim els articles per pàgina
    $articlesPerPagina = 5;
    $paginaActual = max(1, isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1);

    //Obtenir els articles de la pàgina actual i el total d'articles
    $articles = obtenirArticles($paginaActual, $articlesPerPagina);
    $totalArticles = obtenirTotalArticles();
    $totalPagines = obtenirTotalPagines($articlesPerPagina);
//Control missatges d'error versió 1.0
    //Missatges d'error o encert guardats a la sessió (des de la manipulació del formulari)
    $missatgeError = $_SESSION['missatgeError'] ?? '';
    $missatgeCorrecte = $_SESSION['missatgeCorrecte'] ?? '';
    //Tornar a portar les dades corresponents al formulari(si cal).
    $nom = $_SESSION['dadesForm']['nom'] ?? '';
    $email = $_SESSION['dadesForm']['email'] ?? '';
    //Elimina els missatges de la sessió perquè no es mostrin repetidament
    unset($_SESSION['missatgeError']);
    unset($_SESSION['missatgeCorrecte']);
    unset($_SESSION['dadesForm']);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login i Registre</title>
    <link rel="stylesheet" href="css/estils.css"> <!-- Assegura't que estils.css estigui disponible -->
</head>
    <body>
        <!-- Creació del contenidor que mostrarà els missatges-->
        <div class="container simple">
            <!-- Missatge d'error (si n'hi ha) -->
        <?php if (!empty($missatgeError)): ?>
            <p style="color:red;"><?= htmlspecialchars($missatgeError) ?></p>
        <?php endif; ?>

        <!-- Missatge d'èxit (si n'hi ha en aquest cas no els mostraría mai a aquesta pàgina, en tot cas es mostrarà a vista.formulari.php) -->
        <?php if (!empty($missatgeCorrecte)): ?>
            <p style="color:green;"><?= htmlspecialchars($missatgeCorrecte) ?></p>
        <?php endif; ?>
        <h1>Benvingut/da!</h1>
        <!-- Creació dels botons corresponents per tal de fer login o registre-->
        <div class="modal-header">
            <button id="loginBtn" class="active">Inicia Sessió</button>
            <button id="registerBtn">Registra't</button>
        </div>

        <!-- Formulari de Login -->
        <form id="loginForm" class="active" action="model/model.login.php" method="POST">
            <label for="emailLogin">Correu electrònic</label>
            <!--La linia corresponent a php es l'encarregada d'agafar les dades que s'havien introduït anteriorment-->
            <input type="email" id="emailLogin" name="email" required value="<?= isset($_SESSION['dadesForm']['email']) ? htmlspecialchars($_SESSION['dadesForm']['email']) : ''; ?>">
            <!--Creació de l'input per el password del login-->
            <label for="passwordLogin">Contrasenya</label>
            <input type="password" id="passwordLogin" name="password" required>

            <button type="submit" class="enviardades">Inicia sessió</button>
            <p class="forgot-password"><a href="vista.oblit.php">He oblidat la meva contrasenya</a></p>
        </form>

        <!-- Formulari de Registre -->
        <form id="registerForm" action="/model/model.register.php" method="POST">
            <label for="nom">Nom</label>
            <!--La linia corresponent a php es l'encarregada d'agafar les dades que s'havien introduït anteriorment-->
            <input type="text" id="nom" name="nom" required value="<?= isset($_SESSION['dadesForm']['nom']) ? htmlspecialchars($_SESSION['dadesForm']['nom']) : ''; ?>">
            <!--La linia corresponent a php es l'encarregada d'agafar les dades que s'havien introduït anteriorment-->
            <label for="email">Correu electrònic</label>
            <input type="email" id="email" name="email" required value="<?= isset($_SESSION['dadesForm']['email']) ? htmlspecialchars($_SESSION['dadesForm']['email']) : ''; ?>">

            <label for="password">Contrasenya</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm-password">Confirmar Contrasenya</label>
            <input type="password" id="confirm-password" name="confirm-password" required>

            <button type="submit" class="enviardades">Registra't</button>
        </form>
    </div>
    <!-- Llista d'Articles sense iniciar sessio -->
    <div class="container">
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
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hi ha articles disponibles.</p>
        <?php endif; ?>

        <!-- Paginació sense realitzar login -->
        <div class="paginacio">
            <?php if ($paginaActual > 1): ?>
                <a href="?pagina=<?php echo $paginaActual - 1; ?>">&laquo; Anterior</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPagines; $i++): ?>
                <a href="?pagina=<?php echo $i; ?>" <?php echo $i == $paginaActual ? 'class="active"' : ''; ?>>
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($paginaActual < $totalPagines): ?>
                <a href="?pagina=<?php echo $paginaActual + 1; ?>">Següent &raquo;</a>
            <?php endif; ?>
        </div>
    </div>
        <!-- Creació de l'escript corresponent per tal de mostrar en un mateix formulari 2 login i registre-->
        <script>
            // Variables per als botons i formularis
            const loginBtn = document.getElementById('loginBtn');
            const registerBtn = document.getElementById('registerBtn');
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');

            //Funció per alternar entre formularis
            loginBtn.addEventListener('click', function() {
                loginForm.classList.add('active');
                registerForm.classList.remove('active');
                loginBtn.classList.add('active');
                registerBtn.classList.remove('active');
            });

            registerBtn.addEventListener('click', function() {
                registerForm.classList.add('active');
                loginForm.classList.remove('active');
                registerBtn.classList.add('active');
                loginBtn.classList.remove('active');
            });
        </script>
    </body>
</html>
