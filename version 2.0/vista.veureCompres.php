<?php
require 'controlador/cont.compres_usuari.php';
session_start();


// Comprovem si l'usuari ha iniciat sessió
if (!isset($_SESSION['id_usuari'])) {
    header('Location: vista.formularilogin.php');
    exit();
}
// Nombre de compres per pàgina
$per_page = 10;

// Determinar la pàgina actual
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $per_page;

// Consulta per recuperar les compres amb límit per paginació
$stmt = $pdo->prepare("SELECT * FROM compres ORDER BY data_compra DESC LIMIT :start, :per_page");
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':per_page', $per_page, PDO::PARAM_INT);
$stmt->execute();
$compres = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Recuperar el nombre total de compres per calcular quantes pàgines calen
$total_stmt = $pdo->query("SELECT COUNT(*) FROM compres");
$total_compres = $total_stmt->fetchColumn();
$total_pages = ceil($total_compres / $per_page);
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
    <h1>Les Meves Compres</h1>
    
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
                            <td><?php echo htmlspecialchars($compra['id_usuari']); ?></td>
                            <td><?php echo htmlspecialchars($compra['data_compra']); ?></td>
                            <td><?php echo htmlspecialchars($compra['total']); ?> €</td>
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
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="vista.veureCompres.php?page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
</body>
</html>
