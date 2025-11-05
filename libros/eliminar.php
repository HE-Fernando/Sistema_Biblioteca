<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

// Verificar si se recibió el ID del libro
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// Obtener datos del libro para mostrar antes de eliminar
$sql = "SELECT * FROM libros WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$libro = $stmt->fetch(PDO::FETCH_ASSOC);

// Si el libro no existe, redirigir
if (!$libro) {
    header("Location: index.php");
    exit;
}

// Si se confirma la eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "DELETE FROM libros WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Eliminar Libro</title>
</head>
<body>
<h2>Eliminar Libro</h2>

<p>¿Seguro que desea eliminar el siguiente libro?</p>

<ul>
    <li><strong>Título:</strong> <?= htmlspecialchars($libro['titulo']) ?></li>
    <li><strong>Autor:</strong> <?= htmlspecialchars($libro['autor']) ?></li>
    <li><strong>ISBN:</strong> <?= htmlspecialchars($libro['isbn']) ?></li>
</ul>

<form method="POST">
    <button type="submit">Sí, eliminar</button>
    <a href="index.php">Cancelar</a>
</form>

</body>
</html>
