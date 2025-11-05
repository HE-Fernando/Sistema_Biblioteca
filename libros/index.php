<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

// Listar libros
$libros = $pdo->query("SELECT * FROM libros")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Libros</title></head>
<body>
<h2>Listado de Libros</h2>
<a href="crear.php">➕ Agregar Libro</a>
<table border="1" cellpadding="5">
<tr><th>ID</th><th>Título</th><th>Autor</th><th>Estado</th><th>Acciones</th></tr>
<?php foreach ($libros as $libro): ?>
<tr>
    <td><?= $libro['id'] ?></td>
    <td><?= htmlspecialchars($libro['titulo']) ?></td>
    <td><?= htmlspecialchars($libro['autor']) ?></td>
    <td><?= $libro['estado'] ?></td>
    <td>
        <a href="editar.php?id=<?= $libro['id'] ?>">Editar</a> |
        <a href="eliminar.php?id=<?= $libro['id'] ?>">Eliminar</a>
    </td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>
