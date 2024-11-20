<?php

require '../controlador/cont.veureCompres.php';

$pdo = obtenirConnexio();
$resultat = obtenirCompres($pdo);

$compres = $resultat['compres'];
$total_pages = $resultat['total_pages'];
$page = $resultat['page'];
//session_start();

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
    <title>Les Meves Compres</title>
    <link rel="stylesheet" href="../css/estils.css">
</head>
<body>
    <!-- Finestra de Logout -->
    <div class="logout-container">
        <h2>Usuari Actiu</h2>
        <p><?php echo htmlspecialchars($_SESSION['nom_usuari']); ?></p>
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

    <div class="veure_compres <?php echo $amagaVeureCompres ? 'amaga' : ''; ?>">    
        <a href="../vista/vista.administrador.php">Administra Usuaris</a>
    </div>
    
    <div class="veure_compres">    
        <a href="../vista/vista.canviContUser.php">Canvi de contrasenya</a>
    </div>
    <div class="veure_compres">    
        <a href="../vista/vista.formulari.php">Torna a comprar</a>
    </div>
    </div>
    
    <div class="veure-compres-container">
    <h1>Les Meves Compres</h1>
        
    <?php if (count($compres) > 0): ?>
        <table class="table" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr>
                    <th>ID Compra</th>
                    <th>Article</th>
                    <th>Quantitat</th>
                    <th>Preu Total</th>
                    <th>Data Compra</th>
                    <th>ID Usuari</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($compres): ?>
                    <?php foreach ($compres as $compra): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($compra['id_compra']); ?></td>
                            <td><?php echo htmlspecialchars($compra['nom_article']); ?></td>
                            <td><?php echo htmlspecialchars($compra['quantitat']); ?></td>
                            <td><?php echo htmlspecialchars($compra['preu_total']); ?> €</td>
                            <td><?php echo htmlspecialchars($compra['data_compra']); ?></td>
                            <td><?php echo htmlspecialchars($_SESSION['usuari_id']); ?></td>
                            <td>
                                <form action="/controlador/cont.eliminarCompra.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="id_compra" value="<?php echo $compra['id_compra']; ?>">
                                    <input type="hidden" name="nom_article" value="<?php echo htmlspecialchars($compra['nom_article']); ?>">
                                    <input type="hidden" name="quantitat" value="<?php echo $compra['quantitat']; ?>">
                                    <input type="hidden" name="preu_total" value="<?php echo $compra['preu_total']; ?>">
                                    <button type="submit" class="delete-button">
                                        <img src="/img/browse.png" alt="Eliminar" style="width: 20px; height: 20px;">
                                    </button>
                                </form>
                            </td>                            
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>       
                    <tr>
                        <td colspan="4">No hi ha compres per mostrar.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Taula amb el total de tots els preus de les compres -->
        <table class="table" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr>
                    <th>Preu Total de les Compres</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo htmlspecialchars($preu_total_global); ?> €</td>
                </tr>
            </tbody>
        </table>

    <?php else: ?>
        <p>No has realitzat cap compra.</p>
    <?php endif; ?>
    <!-- Botons de paginació -->
    <div class="paginacio">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="/vista/vista.veureCompres.php?page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
</body>
</html>

