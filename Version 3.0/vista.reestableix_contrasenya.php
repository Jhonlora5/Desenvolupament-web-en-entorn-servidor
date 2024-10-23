<?php
/* Jonathan Lopez Ramos */
session_start();
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/estils.css">
    <title>Restablir Contrasenya</title>
</head>
<body>
    <div class="container">    
        <h1>Restablir Contrasenya</h1>
         <!-- Control de Missatges d'èxit o error -->
        <?php if (isset($_SESSION['missatge'])): ?>
            <p class="success"><?= htmlspecialchars($_SESSION['missatge']) ?></p>
        <?php unset($_SESSION['missatge']); ?>
        
        <?php elseif (isset($_SESSION['error'])): ?>
            <p class="error"><?= htmlspecialchars($_SESSION['error']) ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="cont.tokenRecCon.php?token=<?= htmlspecialchars($_GET['token']) ?>" method="post" class="form-reestablir">
            
            <label for="nova_contrasenya">Nova Contrasenya:</label>
            <input type="password" id="nova_contrasenya" name="nova_contrasenya" required>
            <br>
            
            <label for="confirmar_contrasenya">Confirmar Contrasenya:</label>
            <input type="password" id="confirmar_contrasenya" name="confirmar_contrasenya" required>
            <br>
            
            <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token']) ?>">
            <button type="submit" class="enviardades">Canviar Contrasenya</button>
        </form>
    </div>
</body>
</html>

