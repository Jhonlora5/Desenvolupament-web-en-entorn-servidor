<?php
/* Jonathan Lopez Ramos jaaaaaaarrrrrrr*/
session_start();

//CONTROL MISSATGES D'ERRORS
    //Recuperacio de dos missatges, un d'error i un altre de missatges correctes, on es càrregara a la variable el missatge que arriba del fitcher processar.php
    $missatgeError = isset($_SESSION['missatgeError']) ? $_SESSION['missatgeError'] : '';
    $missatgeCorrecte = isset($_SESSION['missatgeCorrecte']) ? $_SESSION['missatgeCorrecte'] : '';

    //Una vegada carregat a la variable esborrem les dades que tenim al $_SESSION per tal de que si recarreguem de nou no carregui una dada anterior.
    unset($_SESSION['missatgeError'], $_SESSION['missatgeCorrecte']);

if (!isset($_SESSION['usuari_id'])) {
    header('Location: ../vista/vista.formularilogin.php');
    $missatgeError = "No pots accedir aquesta ruta si no estas logat.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/estils.css">
    <title>Canvi de Contrasenya</title>
</head>
<body>
    <!-- Finestra de Logout -->
    <div class="logout-container">
        <h2>Usuari Actiu</h2>
        <p><?php echo htmlspecialchars($_SESSION['nom_usuari']); ?></p>       
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
            <a href="/vista/vista.formulari.php">Torna a comprar</a>
        </div>
    </div>

    <div class="container">
        <h1>Canviar Contrasenya</h1>
        <form action="/controlador/cont.canviContUser.php" method="post" class="form-canvi" style="display: block;">
            <label for="contrasenyaAntiga">Contrasenya Antiga:</label>
            <input type="password" id="contrasenyaAntiga" name="contrasenyaAntiga" required placeholder="Introdueix la teva contrasenya antiga">

            <label for="novaContrasenya">Nova Contrasenya:</label>
            <input type="password" id="novaContrasenya" name="novaContrasenya" required placeholder="Mínim de 8 caràcters, incloure lletres, números i caràcters especials">

            <label for="confirmarContrasenya">Confirmar Nova Contrasenya:</label>
            <input type="password" id="confirmarContrasenya" name="confirmarContrasenya" required placeholder="Confirma la nova contrasenya">

            <button type="submit" class="enviardades">Canviar Contrasenya</button>
        </form>
    </div>
</body>
</html>