<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

// Inicializar variables
$buscar = $_GET['buscar'] ?? '';
$resultados = [];

// Si se envió el formulario de búsqueda
if (!empty($buscar)) {
    $sql = "SELECT * FROM libros 
            WHERE titulo LIKE ? 
               OR autor LIKE ? 
               OR isbn LIKE ?";
    $stmt = $pdo->prepare($sql);
    $like = "%" . $buscar . "%";
    $stmt->execute([$like, $like, $like]);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Buscar Libro</title>
</head>
<body>
<h2>Buscar Libro</h2>

<form method="GET">
    <label>Buscar por título, autor o ISBN:</label>
    <input type="text" name="buscar" value="<?= htmlspecialchars($buscar) ?>" required>
    <button type="submit">Buscar</button>
</form>

<?php if (!empty($buscar)): ?>
    <h3>Resultados para: "<?= htmlspecialchars($buscar) ?>"</h3>

    <?php if (count($resultados) > 0): ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th>Título</th>
                <th>Autor</th>
                <th>ISBN</th>
                <th>Acciones</th>
            </tr>
            <?php foreach ($resultados as $libro): ?>
                <tr>
                    <td><?= htmlspecialchars($libro['titulo']) ?></td>
                    <td><?= htmlspecialchars($libro['autor']) ?></td>
                    <td><?= htmlspecialchars($libro['isbn']) ?></td>
                    <td>
                        <a href="editar.php?id=<?= $libro['id'] ?>">Editar</a> |
                        <a href="eliminar.php?id=<?= $libro['id'] ?>">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No se encontraron resultados.</p>
    <?php endif; ?>
<?php endif; ?>

<br>
<a href="index.php">Volver al listado</a>
</body>
</html>
