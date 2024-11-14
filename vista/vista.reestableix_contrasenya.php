<?php
/* Jonathan Lopez Ramos */

require '../controlador/cont.tokenRecCon.php';

//CONTROL MISSATGES D'error VERSIO 1.0
    //Missatges d'error o encert guardats a la sessió (des de la manipulació del formulari)
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
    <link rel="stylesheet" href="../css/estils.css">
    <title>Restablir Contrasenya</title>
</head>
<body>
     <!-- Finestra de Logout -->
     <div class="logout-container">
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

    <div class="container">  

        <h1>Restablir Contrasenya</h1>

        <form action="" method="post" class="form-reestablir">
            
            <label for="nova_contrasenya">Nova Contrasenya:</label>
            <input type="password" id="nova_contrasenya" name="nova_contrasenya" placeholder="Mínim de 8 caràcters, incloure lletres, números i caràcters especials" required>
            <br>
            
            <label for="confirmar_contrasenya">Confirmar Contrasenya:</label>
            <input type="password" id="confirmar_contrasenya" name="confirmar_contrasenya" placeholder="Mínim de 8 caràcters, incloure lletres, números i caràcters especials" required>
            <br>
            
            <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token']) ?>">
            <button type="submit" class="enviardades">Canviar Contrasenya</button>
        </form>
    </div>
</body>
</html>

