<?php
/*Jonathan Lopez Ramos jaaaaaaarrrrrrr*/

//Cridem a model.processar.php que conté la connexió a la base de dades.
//Aquesta crida és necessaria per tal de mostrar els articles amb les mateixes funcions que ja tenim a l'arxiu.
require '../controlador/cont.processar.php';
//echo file_exists('controlador/model.processar.php') ? 'Arxiu trobat' : 'Arxiu no trobat';
require_once '../reCaptchaKeys/keys.autoload.php';
session_start();
//Verificar si hi ha algun error en la connexió per la crida a model.processar.php que ja conté la crida a cont.connexio.php
if (is_array($pdo) && isset($pdo['error'])) {
    //Mostrar error en cas de fallada de connexió(Rectificacio d'errors).
    die($pdo['error']);
}

//COMENÇAMENT DELS ARTICLES A MOSTRAR. 
//Realitzar la paginació: definim els articles per pàgina
$articlesPerPagina = 5;
$paginaActual = max(1, isset($_POST['pagina']) ? (int)$_POST['pagina'] : (isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1));

//Obtenir els articles de la pàgina actual i el total d'articles
$articles = obtenirArticles($paginaActual, $articlesPerPagina);
$totalArticles = obtenirTotalArticles();
$totalPagines = obtenirTotalPagines($articlesPerPagina);

//CONTROL MISSATGES D'error VERSIO 1.0
//Missatges d'error o encert guardats a la sessió (des de la manipulació del formulari)
$missatgeError = isset($_SESSION['missatgeError']) ? $_SESSION['missatgeError'] : '';
$missatgeCorrecte = isset($_SESSION['missatgeCorrecte']) ? $_SESSION['missatgeCorrecte'] : '';

//Una vegada carregat a la variable esborrem les dades que tenim al $_SESSION per tal de que si recarreguem de nou no carregui una dada anterior.
unset($_SESSION['missatgeError'], $_SESSION['missatgeCorrecte']);

//Tornar a portar les dades corresponents al formulari(si cal).
$nom = $_SESSION['dadesForm']['nom'] ?? '';
$email = $_SESSION['dadesForm']['email'] ?? '';
//Elimina els missatges de la sessió perquè no es mostrin repetidament
unset($_SESSION['dadesForm']);


//POLÍTICA DE COOKIES
//Verifica si l'usuari ha acceptat les cookies
$cookiesAcceptades = isset($_COOKIE['acceptaCookies']) ? true : false;

// Si l'usuari ha acceptat les cookies, crear la cookie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acceptaCookies'])) {
    setcookie('acceptaCookies', 'true', time() + (30 * 24 * 60 * 60), '/'); // Cookie per 30 dies
    $cookiesAcceptades = true;
    header("Location: vista.formulariLogin.php");
    exit();
}
$mostrarRecaptcha = isset($_SESSION['intents_fallits']) && $_SESSION['intents_fallits'] > 2;

//Càrrega de variables amb el codi Recaptcha
$keyRecaptcha = $_SESSION['key-client'];

// Verificar si l'usuari ja està autenticat
if (isset($_SESSION['nom_usuari'])) {
    // Si ja està autenticat, carreguem les dades de l'usuari
    $usuari = isset($_SESSION['email']) ? verificarUsuariPerEmail($_SESSION['email'], $pdo) : null;  // Aquí passen les dades del correu i un password buit
    if ($usuari) {
        $_SESSION['nom_usuari'] = $usuari['nom'];
    } else {
        // Si no es pot carregar l'usuari per algun motiu, desconnectem
        unset($_SESSION['nom_usuari']);
    }
}
// Controlar si l'usuari no ha iniciat sessió
$usuariAutenticat = isset($_SESSION['usuari_id']);
if ($usuariAutenticat) {
    // Aquí pots incloure la vista/logica que mostra el contingut per a usuaris autenticats
    header('Location: vista.formulari.php');
    exit;
}

//creem una variable per tal de mostrar o no els enllaços corresponents a vista.administrador.php
$amagaVeureCompres = !(isset($_SESSION['nivell_administrador']) && $_SESSION['nivell_administrador'] == 1);
?>
<script>
    // Funció per obrir i tancar el menú desplegable d'acces a les diverses pàgines.
    function toggleDropdown() {
        const dropdown = document.querySelector('.dropdown');
        dropdown.classList.toggle('open');
    }
</script>
<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login i Registre</title>
    <link rel="stylesheet" href="../css/estils.css">
</head>

<body>
    <!-- Banner de Cookies -->
    <?php if (!$cookiesAcceptades): ?>
        <div id="cookieBanner" class="cookie-banner" style="display: block;">
            <p>Aquesta pàgina utilitza cookies per millorar l'experiència de l'usuari. Si us plau, accepta l'ús de cookies per continuar.</p>
            <form method="POST" action="" style="display: block">
                <!-- Camp ocult que envia "true" quan s'accepten les cookies -->
                <input type="hidden" name="acceptaCookies" value="true">
                <button type="submit">Accepta Cookies</button>
            </form>
        </div>
    <?php endif; ?>

    <!-- Finestra de Logout -->
    <div class="logout-container">
        <h2>Benvingut/da!</h2>

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
    </div>

    <!-- Contingut principal només visible si les cookies estan acceptades -->
    <div id="mainContent" class="<?= !$cookiesAcceptades ? 'hidden' : ''; ?>"> <!-- Amaga el contingut fins que s'acceptin les cookies -->

        <!-- Creació del contenidor que mostrarà els missatges-->
        <div class="container simple">



            <!-- Creació dels botons corresponents per tal de fer login o registre-->
            <div class="modal-header">
                <button id="loginBtn" class="active">Inicia Sessió</button>
                <button id="registerBtn">Registra't</button>
            </div>

            <!-- Formulari de Login -->
            <form id="loginForm" class="active" action="/controlador/cont.loginRegistre.php" method="POST">
                <label for="emailLogin">Correu electrònic</label>
                <!--La linia corresponent a php es l'encarregada d'agafar les dades que s'havien introduït anteriorment-->
                <input type="email" id="emailLogin" name="email" required value="<?= isset($_SESSION['dadesForm']['email']) ? htmlspecialchars($_SESSION['dadesForm']['email']) : ''; ?>">
                <!--Creació de l'input per el password del login-->
                <label for="passwordLogin">Contrasenya</label>
                <input type="password" id="passwordLogin" name="password" required>
                <!-- Checkbox de "Recorda'm" -->
                <label>
                    <input type="checkbox" name="recorda"> Recorda la contrasenya
                </label>

                <!-- Afegir reCAPTCHA si supera els 3 intents -->
                <?php if ($mostrarRecaptcha): ?>
                    <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($keyRecaptcha); ?>"></div>
                <?php endif; ?>
                <button type="submit" class="enviardades">Inicia sessió</button>
                <p class="forgot-password"><a href="/vista/vista.oblit.php">He oblidat la meva contrasenya</a></p>
            </form>

            <!-- Afegir el script de reCAPTCHA -->
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>

            <div class="social-login">
                <p>O inicia sessió amb:</p>
                <a href="../controlador/cont.googleAuth.php">
                    <img src="../img/google_icon.png" alt="Login amb Google" style="width: 50px; height: auto;">
                </a>
                <a href="../controlador/cont.socialAuth.php?provider=GitHub">
                    <img src="../img/github_icon.png" alt="Login amb GitHub" style="width: 100px; height: auto;">
                </a>
            </div>

            
            <a href="http://localhost/vista/vista.politica-privadesa.php">Política de Privadesa</a>
            <!-- Formulari de Registre -->
            <form id="registerForm" action="/controlador/cont.loginRegistre.php" method="POST">
                <label for="nom">Nom</label>
                <!--La linia corresponent a php es l'encarregada d'agafar les dades que s'havien introduït anteriorment-->
                <input type="text" id="nom" name="nom" placeholder="Escriu el teu nom" required value="<?= isset($_SESSION['dadesForm']['nom']) ? htmlspecialchars($_SESSION['dadesForm']['nom']) : ''; ?>">
                <!--La linia corresponent a php es l'encarregada d'agafar les dades que s'havien introduït anteriorment-->
                <label for="email">Correu electrònic</label>
                <input type="email" id="email" name="email" required value="<?= isset($_SESSION['dadesForm']['email']) ? htmlspecialchars($_SESSION['dadesForm']['email']) : ''; ?>">

                <label for="password">Contrasenya</label>
                <input type="password" id="password" name="password" placeholder="Mínim de 8 caràcters, incloure lletres, números i caràcters especials" required>

                <label for="confirm-password">Confirmar Contrasenya</label>
                <input type="password" id="confirm-password" name="confirm-password" placeholder="Mínim de 8 caràcters, incloure lletres, números i caràcters especials" required>

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
                <form method="POST" action="vista.formulariLogin.php">
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
            <!-- Creació de l'escript corresponent per tal de mostrar en un mateix formulari 2 login i registre-->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('cookieBanner').querySelector('button').addEventListener('click', function() {
                        document.cookie = "acceptaCookies=true; max-age=" + (30 * 24 * 60 * 60); // 30 dies
                        document.getElementById('cookieBanner').style.display = 'none';
                        document.getElementById('mainContent').classList.remove('hidden');
                    })
                });

                //Variables per als botons i formularis
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
                document.addEventListener('DOMContentLoaded', function() {
                    let mostrarRecaptcha = <?= json_encode($mostrarRecaptcha); ?>;
                    if (mostrarRecaptcha) {
                        document.querySelector('.g-recaptcha').style.display = 'block';
                    }
                });
            </script>
        </div>
</body>

</html>