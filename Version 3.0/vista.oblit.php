<?php
/* Jonathan Lopez Ramos */
// Comprovem si hi ha un missatge de confirmació de l'enviament del correu
if (isset($_SESSION['missatge'])) {
    echo '<div>' . $_SESSION['missatge'] . '</div>';
    unset($_SESSION['missatge']);
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oblit de Contrasenya</title>
    <link rel="stylesheet" href="css/estils.css">
</head>
<body>
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