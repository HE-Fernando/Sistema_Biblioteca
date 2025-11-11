<?php
include("../includes/auth.php");
include("../includes/header.php");
include("../config/database.php");

// Validar ID recibido por GET
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
  echo "<p>❌ ID no válido.</p>";
  include("../includes/footer.php");
  exit;
}

// Obtener datos del libro
$stmt = $pdo->prepare("
  SELECT id, titulo, autor, isbn, editorial, anio, categoria, descripcion, estado, created_at, updated_at
  FROM libros WHERE id = :id
");
$stmt->execute([':id' => $id]);
$libro = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$libro) {
  echo "<p>❌ Libro no encontrado.</p>";
  include("../includes/footer.php");
  exit;
}

// Obtener préstamos activos (si existen)
$stmt2 = $pdo->prepare("
  SELECT p.id AS id_prestamo, u.nombre AS usuario, p.fecha_prestamo, p.fecha_devolucion
  FROM prestamos p
  JOIN usuarios u ON u.id = p.id_usuario
  WHERE p.id_libro = :id AND p.fecha_devolucion IS NULL
");
$stmt2->execute([':id' => $id]);
$prestamos_activos = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Obtener historial de préstamos
$stmt3 = $pdo->prepare("
  SELECT p.id, u.nombre AS usuario, p.fecha_prestamo, p.fecha_devolucion
  FROM prestamos p
  JOIN usuarios u ON u.id = p.id_usuario
  WHERE p.id_libro = :id
  ORDER BY p.fecha_prestamo DESC
  LIMIT 10
");
$stmt3->execute([':id' => $id]);
$historial = $stmt3->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
  <h2>📘 Detalle del Libro</h2>

  <div class="card">
    <h3><?= htmlspecialchars($libro['titulo']) ?></h3>
    <p><strong>Autor:</strong> <?= htmlspecialchars($libro['autor']) ?></p>
    <p><strong>ISBN:</strong> <?= htmlspecialchars($libro['isbn']) ?></p>
    <p><strong>Editorial:</strong> <?= htmlspecialchars($libro['editorial'] ?? '-') ?></p>
    <p><strong>Año:</strong> <?= htmlspecialchars($libro['anio'] ?? '-') ?></p>
    <p><strong>Categoría:</strong> <?= htmlspecialchars($libro['categoria'] ?? '-') ?></p>
    <p><strong>Descripción:</strong><br><?= nl2br(htmlspecialchars($libro['descripcion'] ?? '-')) ?></p>
    <p><strong>Estado:</strong>
      <span class="badge <?= $libro['estado'] === 'disponible' ? 'verde' : 'gris' ?>">
        <?= htmlspecialchars($libro['estado']) ?>
      </span>
    </p>
    <p><strong>Creado:</strong> <?= $libro['created_at'] ?> |
       <strong>Actualizado:</strong> <?= $libro['updated_at'] ?></p>

    <div class="acciones">
      <a href="editar.php?id=<?= $libro['id'] ?>" class="btn btn-primary">✏ Editar</a>
      <?php if ($libro['estado'] === 'disponible'): ?>
        <a href="eliminar.php?id=<?= $libro['id'] ?>" class="btn btn-danger">🗑 Eliminar</a>
      <?php endif; ?>
      <a href="index.php" class="btn btn-secondary">⬅ Volver</a>
    </div>
  </div>

  <hr>

  <h3>📗 Préstamos Activos</h3>
  <?php if (count($prestamos_activos) > 0): ?>
    <table class="tabla">
      <thead>
        <tr>
          <th>ID</th>
          <th>Usuario</th>
          <th>Fecha Préstamo</th>
          <th>Fecha Devolución</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($prestamos_activos as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['id_prestamo']) ?></td>
            <td><?= htmlspecialchars($p['usuario']) ?></td>
            <td><?= htmlspecialchars($p['fecha_prestamo']) ?></td>
            <td><?= htmlspecialchars($p['fecha_devolucion'] ?? '-') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>✅ No hay préstamos activos para este libro.</p>
  <?php endif; ?>

  <hr>

  <h3>📘 Historial de Préstamos (últimos 10)</h3>
  <?php if (count($historial) > 0): ?>
    <table class="tabla">
      <thead>
        <tr>
          <th>ID</th>
          <th>Usuario</th>
          <th>Fecha Préstamo</th>
          <th>Fecha Devolución</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($historial as $h): ?>
          <tr>
            <td><?= htmlspecialchars($h['id']) ?></td>
            <td><?= htmlspecialchars($h['usuario']) ?></td>
            <td><?= htmlspecialchars($h['fecha_prestamo']) ?></td>
            <td><?= htmlspecialchars($h['fecha_devolucion'] ?? '-') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>ℹ️ Sin historial registrado.</p>
  <?php endif; ?>
</div>

<?php include("../includes/footer.php"); ?>
