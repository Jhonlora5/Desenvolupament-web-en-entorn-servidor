<?php
require '../controlador/cont.processar.php'; // Connexió a la base de dades

// Obtenir la llista d'usuaris
$usuaris = $pdo->query("SELECT id_usuari, nom, email, data_registre, nivell_administrador FROM usuaris")->fetchAll(PDO::FETCH_ASSOC);

//CONTROL MISSATGES D'ERRORS
    //Recuperacio de dos missatges, un d'error i un altre de missatges correctes, on es càrregara a la variable el missatge que arriba del fitcher processar.php
    $missatgeError = isset($_SESSION['missatgeError']) ? $_SESSION['missatgeError'] : '';
    $missatgeCorrecte = isset($_SESSION['missatgeCorrecte']) ? $_SESSION['missatgeCorrecte'] : '';

    //Una vegada carregat a la variable esborrem les dades que tenim al $_SESSION per tal de que si recarreguem de nou no carregui una dada anterior.
    unset($_SESSION['missatgeError'], $_SESSION['missatgeCorrecte']);

//Nomes els usuaris administradors poden entrar
if ($_SESSION['nivell_administrador'] != 1) {
    $_SESSION['missatgeError'] = "Accés denegat: no tens permisos suficients.";
    header("Location: ../vista/vista.formulari.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestió d'Usuaris</title>
    <link rel="stylesheet" href="../css/estils.css">
</head>
<body>
    <!-- Finestra de Logout -->
    <div class="logout-container">
        <h2>Administració d'usuaris</h2>
        <p><?php echo htmlspecialchars($_SESSION['nom_usuari']); ?></p>
        <!-- Avatar de l'usuari -->
        <img 
            src="<?php echo htmlspecialchars('../'. $_SESSION['imatge_perfil'] ?? '../imgPerfils/default.jpg'); ?>" 
            alt="Imatge de perfil"        
            class="avatar"
        >
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

        
    <div class="dropdown">
        <!-- Títol del desplegable -->
        <div class="dropdown-toggle" onclick="toggleDropdown()">Edita el teu perfil</div>
            <!-- Opcions del menú -->
            <div class="dropdown-menu">
                <a href="../vista/vista.edicioPerfil.php">Edita el teu perfil</a>
                <a href="../vista/vista.canviContUser.php">Canvi de contrasenya</a>
                <a href="../vista/vista.formulari.php">Torna a comprar</a>
            </div>
        </div>
    </div>    


    <div class="veure-compres-container">
    <h1>Gestió d'Usuaris</h1>
        <!-- Mostrar la llista d'usuaris -->
    <table class="table" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
        <tr>
            <th>ID Usuari</th>
            <th>Nom</th>
            <th>Email</th>
            <th>Data de registre</th>
            <th>Nivell Administrador</th>
            <th>Accions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($usuaris as $usuari): ?>
            <tr>
                <td><?= htmlspecialchars($usuari['id_usuari']); ?></td>
                <td><?= htmlspecialchars($usuari['nom']); ?></td>
                <td><?= htmlspecialchars($usuari['email']); ?></td>
                <td><?= htmlspecialchars($usuari['data_registre']); ?></td>
                <td>
                    <!-- Formulari per actualitzar el nivell d'administrador -->
                    <form action="../controlador/cont.administracioUsuaris.php" method="post" style="display: block">
                        <input type="hidden" name="accio" value="actualitzar_nivell">
                        <input type="hidden" name="id_usuari" value="<?= $usuari['id_usuari']; ?>">
                        <select name="nivell_administrador" class="form-select">
                            <option value="1" <?= $usuari['nivell_administrador'] == 1 ? 'selected' : ''; ?>>Administrador</option>
                            <option value="2" <?= $usuari['nivell_administrador'] == 2 ? 'selected' : ''; ?>>Usuari</option>
                        </select>
                        <button type="submit" class="btn btn-primary mt-2">Canviar</button>
                    </form>
                </td>
                <td>
                    <!-- Formulari per eliminar l'usuari -->
                    <form action="../controlador/cont.administracioUsuaris.php" method="post" style="display: block">
                        <input type="hidden" name="accio" value="delete">
                        <input type="hidden" name="id_usuari" value="<?= $usuari['id_usuari']; ?>">
                        <button type="submit" class="btn btn-danger">
                            <img src="../img/browse.png" alt="Eliminar" style="width: 32px; height: 32px;">
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
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
