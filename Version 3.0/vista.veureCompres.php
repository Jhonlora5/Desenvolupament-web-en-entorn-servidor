<?php
require 'controlador/cont.veureCompres.php';

$pdo = obtenirConnexio();
$resultat = obtenirCompres($pdo);

$compres = $resultat['compres'];
$total_pages = $resultat['total_pages'];
$page = $resultat['page'];
//session_start();
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Les Meves Compres</title>
    <link rel="stylesheet" href="/css/estils.css">
</head>
<body>
    <div class="container">
    <h1>Les Meves Compres</h1>
        <div class="veure_compres">    
            <a href="vista.formulari.php">Torna a comprar</a>
        </div>
    <?php if (count($compres) > 0): ?>
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>ID Compra</th>
                    <th>Article</th>
                    <th>Quantitat</th>
                    <th>Preu Total</th>
                    <th>Data Compra</th>
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
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No hi ha compres per mostrar.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No has realitzat cap compra.</p>
    <?php endif; ?>
    <!-- Botons de paginació -->
    <div class="paginacio">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="vista.veureCompres.php?page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
    </div>
</body>
</html>
