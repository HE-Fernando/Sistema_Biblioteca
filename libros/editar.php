<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

// Obtener el ID del libro desde la URL (GET)
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// Si se envía el formulario (POST), actualiza los datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $autor = $_POST['autor'];
    $isbn = $_POST['isbn'];

    $sql = "UPDATE libros SET titulo = ?, autor = ?, isbn = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$titulo, $autor, $isbn, $id]);

    header("Location: index.php");
    exit;
}

// Obtener los datos actuales del libro
$sql = "SELECT * FROM libros WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$libro = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no se encuentra el libro, redirigir
if (!$libro) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Editar Libro</title>
</head>
<body>
<h2>Editar Libro</h2>

<form method="POST">
    <label>Título:</label>
    <input type="text" name="titulo" value="<?= htmlspecialchars($libro['titulo']) ?>" required><br>

    <label>Autor:</label>
    <input type="text" name="autor" value="<?= htmlspecialchars($libro['autor']) ?>" required><br>

    <label>ISBN:</label>
    <input type="text" name="isbn" value="<?= htmlspecialchars($libro['isbn']) ?>" required><br><br>

    <button type="submit">Actualizar</button>
</form>

<a href="index.php">Volver al listado</a>
</body>
</html>
