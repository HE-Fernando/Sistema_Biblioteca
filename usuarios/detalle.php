<?php
// usuarios/detalle.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) exit('ID inválido.');

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
$stmt->execute([':id'=>$id]);
$user = $stmt->fetch();
if (!$user) exit('Usuario no encontrado.');

// préstamos activos
$stmt = $pdo->prepare("SELECT p.*, l.titulo AS libro_titulo
    FROM prestamos p
    LEFT JOIN libros l ON l.id = p.libro_id
    WHERE p.usuario_id = :id AND p.fecha_devolucion IS NULL
    ORDER BY p.fecha_prestamo DESC");
$stmt->execute([':id'=>$id]);
$activos = $stmt->fetchAll();

// historial últimos 10
$stmt = $pdo->prepare("SELECT p.*, l.titulo AS libro_titulo
    FROM prestamos p
    LEFT JOIN libros l ON l.id = p.libro_id
    WHERE p.usuario_id = :id
    ORDER BY p.fecha_prestamo DESC
    LIMIT 10");
$stmt->execute([':id'=>$id]);
$historial = $stmt->fetchAll();
?>

<h1>Detalle Usuario: <?= e($user['nombre']) ?></h1>
<p>Email: <?= e($user['email']) ?> | DNI: <?= e($user['dni']) ?> | Estado: <?= e($user['estado']) ?></p>

<h2>Préstamos activos</h2>
<?php if (empty($activos)): ?>
  <p>No tiene préstamos activos.</p>
<?php else: ?>
  <table border="1">
    <thead><tr><th>Libro</th><th>Prestado</th><th>Vencimiento</th><th>Acciones</th></tr></thead>
    <tbody>
    <?php foreach($activos as $p): ?>
      <tr>
        <td><?= e($p['libro_titulo']) ?></td>
        <td><?= e($p['fecha_prestamo']) ?></td>
        <td><?= e($p['fecha_vencimiento']) ?></td>
        <td><a href="/prestamos/devolver.php?id=<?= e($p['id']) ?>">Registrar devolución</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<h2>Historial (últimos 10)</h2>
<?php if (empty($historial)): ?>
  <p>Sin historial.</p>
<?php else: ?>
  <ul>
  <?php foreach($historial as $h): ?>
    <li><?= e($h['fecha_prestamo']) ?> — <?= e($h['libro_titulo']) ?> — <?= e($h['fecha_devolucion'] ?? 'No devuelto') ?></li>
  <?php endforeach; ?>
  </ul>
<?php endif; ?>

<p><a href="index.php">Volver</a> | <a href="editar.php?id=<?= $user['id'] ?>">Editar</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
