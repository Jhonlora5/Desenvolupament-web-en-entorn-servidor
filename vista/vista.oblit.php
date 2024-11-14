<?php
/* Jonathan Lopez Ramos */
session_start();

// Comprovem si hi ha un missatge de confirmació de l'enviament del correu
if (isset($_SESSION['missatge'])) {
    echo '<div>' . $_SESSION['missatge'] . '</div>';
    unset($_SESSION['missatge']);
}

//CONTROL MISSATGES D'ERRORS
    //Recuperacio de dos missatges, un d'error i un altre de missatges correctes, on es càrregara a la variable el missatge que arriba del fitcher processar.php
    $missatgeError = isset($_SESSION['missatgeError']) ? $_SESSION['missatgeError'] : '';
    $missatgeCorrecte = isset($_SESSION['missatgeCorrecte']) ? $_SESSION['missatgeCorrecte'] : '';

    //Una vegada carregat a la variable esborrem les dades que tenim al $_SESSION per tal de que si recarreguem de nou no carregui una dada anterior.
    unset($_SESSION['missatgeError'], $_SESSION['missatgeCorrecte']);
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oblit de Contrasenya</title>
    <link rel="stylesheet" href="../css/estils.css">
</head>
<body>
    <!-- Finestra de Logout -->
    <div class="oblit-container">
    <h2>Has oblidat la contrasenya no?</h2>
    <div style="text-align: center;">
        <img src="/img/sheldon.gif" alt="Sheldon" class="sheldon">
    </div>
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
    <div class="oblidcorreu">     
    <h1>Oblit de Contrasenya</h1>    
    <form action="/model/model.oblitContra.php" method="post" class="form-logout">
        <label for="email">Correu Electrònic:</label>
        <input type="text" id="email" name="email" required>
        <button type="submit">Enviar Correu</button>
    </form>
    </div>
</body>
</html>