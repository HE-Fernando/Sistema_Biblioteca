<?php
include("../includes/header.php");
include("../config/database.php");

$stmt = $pdo->query("
    SELECT p.id, u.nombre AS usuario, l.titulo AS libro,
           p.fecha_prestamo, p.fecha_devolucion, p.estado
    FROM prestamos p
    JOIN usuarios u ON p.usuario_id = u.id
    JOIN libros l ON p.libro_id = l.id
    ORDER BY p.id DESC
");
$prestamos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <h2>📚 Préstamos Activos</h2>
    <a href="nuevo.php" class="btn btn-success mb-3">+ Nuevo préstamo</a>
    <a href="historial.php" class="btn btn-secondary mb-3">📜 Ver historial</a>

    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ID</th><th>Usuario</th><th>Libro</th>
                <th>Fecha préstamo</th><th>Fecha devolución</th>
                <th>Estado</th><th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($prestamos as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['usuario']) ?></td>
                <td><?= htmlspecialchars($p['libro']) ?></td>
                <td><?= $p['fecha_prestamo'] ?></td>
                <td><?= $p['fecha_devolucion'] ?></td>
                <td>
                    <span class="badge bg-<?= $p['estado'] == 'activo' ? 'warning' : 'success' ?>">
                        <?= ucfirst($p['estado']) ?>
                    </span>
                </td>
                <td>
                    <?php if ($p['estado'] == 'activo'): ?>
                        <a href="devolver.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-primary">Devolver</a>
                    <?php else: ?>
                        <span class="text-muted">Devuelto</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="../assets/js/prestamos.js"></script>
<?php include("../includes/footer.php"); ?>
