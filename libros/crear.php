<?php
include("../includes/auth.php");
include("../includes/header.php");
include("../config/database.php");

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $titulo = trim($_POST['titulo']);
  $autor = trim($_POST['autor']);
  $isbn = trim($_POST['isbn']);
  $editorial = $_POST['editorial'] ?? null;
  $anio = $_POST['anio'] ?? null;
  $categoria = $_POST['categoria'] ?? null;
  $descripcion = $_POST['descripcion'] ?? null;

  if ($titulo && $autor && $isbn) {
    try {
      $stmt = $pdo->prepare("INSERT INTO libros (titulo, autor, isbn, editorial, anio, categoria, descripcion)
                             VALUES (?, ?, ?, ?, ?, ?, ?)");
      $stmt->execute([$titulo, $autor, $isbn, $editorial, $anio, $categoria, $descripcion]);
      $mensaje = "✅ Libro creado correctamente.";
    } catch (PDOException $e) {
      $mensaje = "⚠️ Error: ISBN duplicado o datos inválidos.";
    }
  } else {
    $mensaje = "⚠️ Complete los campos obligatorios.";
  }
}
?>

<div class="container">
  <h2>📖 Nuevo Libro</h2>
  <p><?= $mensaje ?></p>

  <form method="POST">
    <label>Título*</label><input type="text" name="titulo" required>
    <label>Autor*</label><input type="text" name="autor" required>
    <label>ISBN*</label><input type="text" name="isbn" required>
    <label>Editorial</label><input type="text" name="editorial">
    <label>Año</label><input type="number" name="anio">
    <label>Categoría</label><input type="text" name="categoria">
    <label>Descripción</label><textarea name="descripcion"></textarea>
    <button type="submit" class="btn btn-success">Guardar</button>
  </form>
</div>
<?php include("../includes/footer.php"); ?>


