<?php
session_start();
if (!isset($_SESSION["usuario"])){
    header("Location: ../biblioteca/login.php");
    exit();
}
require_once '../config/conexion_db.php';

$id = $_GET['id'] ?? null;
if (!$id) die("ID inválido");

$stmt = $pdo->prepare("SELECT * FROM libros WHERE id=?");
$stmt->execute([$id]);
$libro = $stmt->fetch();

if (!$libro) die("Libro no encontrado");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
<<<<<<< HEAD
  $titulo = $_POST['titulo'];
  $autor = $_POST['autor'];
  $isbn = $_POST['isbn'];
  $editorial = $_POST['editorial'];
  $anio = $_POST['anio'];
  $categoria = $_POST['categoria'];
  $descripcion = $_POST['descripcion'];
  $estado = $_POST['estado'];

  $stmt = $pdo->prepare("UPDATE libros SET titulo=?, autor=?, isbn=?, editorial=?, anio=?, categoria=?, descripcion=?, estado=? WHERE id=?");
  $stmt->execute([$titulo, $autor, $isbn, $editorial, $anio, $categoria, $descripcion, $estado, $id]);

  echo "<p>✅ Libro actualizado.</p>";
=======
    $id = $_POST['id'] ?? 0;
    $titulo = $_POST['titulo'] ?? '';
    $autor = $_POST['autor'] ?? '';
    $isbn = $_POST['isbn'] ?? '';
    $editorial = $_POST["editorial"] ?? "";
    $anio = $_POST["anio"] ?? null;
    $categoria = $_POST['categoria'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';

    if (empty($id) || empty($titulo) || empty($autor) || empty($isbn)) {
        echo json_encode([
            'success' => false,
            'message' => 'Datos incompletos'
        ]);
        exit;
    }

    $sql = "UPDATE libro SET titulo = ?, autor = ?, isbn = ?, editorial = ?, anio = ?, categoria = ?, descripcion = ?
    WHERE id = ?";
    $stm = $pdo->prepare($sql);
    $ok = $stm->execute([$titulo, $autor, $isbn, $editorial, $anio, $categoria, $descripcion, $id]);

    if ($ok){
        echo json_encode([
            "success" => true,
            "message" => "Libro actualizado correctamente"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Error al actualizar libro"
        ]);
    }
    exit;
>>>>>>> origin/Fernando
}
?>

<div class="container">
  <h2>✏ Editar Libro</h2>
  <form method="POST">
    <input type="text" name="titulo" value="<?= htmlspecialchars($libro['titulo']) ?>" required>
    <input type="text" name="autor" value="<?= htmlspecialchars($libro['autor']) ?>" required>
    <input type="text" name="isbn" value="<?= htmlspecialchars($libro['isbn']) ?>" required>
    <input type="text" name="editorial" value="<?= htmlspecialchars($libro['editorial']) ?>">
    <input type="number" name="anio" value="<?= htmlspecialchars($libro['anio']) ?>">
    <input type="text" name="categoria" value="<?= htmlspecialchars($libro['categoria']) ?>">
    <textarea name="descripcion"><?= htmlspecialchars($libro['descripcion']) ?></textarea>
    <select name="estado">
      <option value="disponible" <?= $libro['estado']=='disponible'?'selected':'' ?>>Disponible</option>
      <option value="prestado" <?= $libro['estado']=='prestado'?'selected':'' ?>>Prestado</option>
    </select>
    <button type="submit" class="btn btn-primary">Actualizar</button>
  </form>
</div>
<?php include("../includes/footer.php"); ?>

<<<<<<< HEAD
=======
echo json_encode([
    'success' => false,
    'message' => 'Método no permitido'
]);
?>
>>>>>>> origin/Fernando
