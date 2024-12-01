<?php
require '../model/model.edicioPerfil.php';

if (!isset($_SESSION['usuari_id'])) {
    header("Location: vista.formulariLogin.php");
    exit();
}

$pdo = obtenirConnexio();
// Obtenir imatges del directori
$imatgesDisponibles = obtenirImatgesPerfils();

//CONTROL MISSATGES D'ERRORS
    //Recuperacio de dos missatges, un d'error i un altre de missatges correctes, on es càrregara a la variable el missatge que arriba del fitcher processar.php
    $missatgeError = isset($_SESSION['missatgeError']) ? $_SESSION['missatgeError'] : '';
    $missatgeCorrecte = isset($_SESSION['missatgeCorrecte']) ? $_SESSION['missatgeCorrecte'] : '';

    //Una vegada carregat a la variable esborrem les dades que tenim al $_SESSION per tal de que si recarreguem de nou no carregui una dada anterior.
    unset($_SESSION['missatgeError'], $_SESSION['missatgeCorrecte']);

//Calcular el total de tots els preus de les compres
$preu_total_global = 0;
foreach ($compres as $compra) {
    $preu_total_global += $compra['preu_total'];
}
//creem una variable per tal de mostrar o no els enllaços corresponents a vista.administrador.php
$amagaVeureCompres = !(isset($_SESSION['nivell_administrador']) && $_SESSION['nivell_administrador'] == 1);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/estils.css">
    <title>Edició del Perfil</title>
</head>
<body>
    <div class="logout-container">    
        <h2>Usuari Actiu</h2>
        <p><?php echo htmlspecialchars($_SESSION['nom_usuari']); ?></p>
        <!-- Avatar de l'usuari -->
        <img 
            src="<?php echo htmlspecialchars('../'. $_SESSION['imatge_perfil'] ?? '../imgPerfils/default.jpg'); ?>" 
            alt="Imatge de perfil"        
            class="avatar"
        >
    
        <form method="POST" action="/controlador/cont.logout.php" class="form-logout">
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
    
   
    <div class="dropdown">
        <!-- Títol del desplegable -->
        <div class="dropdown-toggle" onclick="toggleDropdown()">Torna a comprar</div>
            <!-- Opcions del menú -->
            <div class="dropdown-menu">
                <!-- Mostra l'opció Administra Usuaris només si l'usuari és administrador -->
                <a href="../vista/vista.administrador.php" class="<?php echo $amagaVeureCompres ? 'amaga' : ''; ?>">Administra Usuaris</a>
                <a href="../vista/vista.canviContUser.php">Canvi de contrasenya</a>
                <a href="../vista/vista.formulari.php">Torna a comprar</a>
            </div>
        </div>  
</div>

<div class="container">
    <h1>Edició del Perfil</h1>
    <form method="POST" action="../controlador/cont.edicioPerfil.php" style="display: block">
        <label for="nom">Nou Nom:</label>
        <input type="text" name="nom" id="nom" value="<?php echo htmlspecialchars($_SESSION['usuari_nom']); ?>" required>

        <label for="img_perfil">Selecciona una nova imatge:</label>
        <div class="imatges-container">
            <?php foreach ($imatgesDisponibles as $imatge): ?>
                <label>
                    
                    <input 
                        type="radio" 
                        name="img_perfil" 
                        value="<?php echo htmlspecialchars($imatge ['id_imatge']); ?>" 
                        <?php echo ($_SESSION['img_perfil'] ?? '') == $imatge['id_imatge'] ? 'checked' : ''; ?>
                    >
                    <img src="<?php echo htmlspecialchars('../' . $imatge['ruta']); ?>" alt="Opció d'imatge" class="imatge-opcio">
                </label>
            <?php endforeach; ?>
        </div>

        <button type="submit">Guardar Canvis</button>
    </form>
</div>
</body>
</html>
<script>
// Funció per obrir i tancar el menú desplegable
function toggleDropdown() {
    const dropdown = document.querySelector('.dropdown');
    dropdown.classList.toggle('open');
}
</script>

