<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $autor = $_POST['autor'];
    $isbn = $_POST['isbn'];

    $sql = "INSERT INTO libros (titulo, autor, isbn) VALUES (?,?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$titulo, $autor, $isbn]);
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Nuevo Libro</title></head>
<body>
<h2>Agregar Libro</h2>
<form method="POST">
    <label>Título:</label><input type="text" name="titulo" required><br>
    <label>Autor:</label><input type="text" name="autor" required><br>
    <label>ISBN:</label><input type="text" name="isbn" required><br><br>
    <button type="submit">Guardar</button>
</form>
</body>
</html>
