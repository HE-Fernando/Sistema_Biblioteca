<?php
include("../includes/header.php");
include("../config/database.php");

$id = $_GET['id'] ?? null;
if (!$id) die("Préstamo no encontrado");

$stmt = $pdo->prepare("
    SELECT p.*, u.nombre AS usuario, l.titulo AS libro
    FROM prestamos p
    JOIN usuarios u ON p.usuario_id = u.id
    JOIN libros l ON p.libro_id = l.id
    WHERE p.id = ? AND p.estado = 'activo'
");
$stmt->execute([$id]);
$prestamo = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$prestamo) die("Préstamo no válido o ya devuelto");
?>

<div class="container mt-4">
    <h2>🔄 Registrar Devolución</h2>
    <p><strong>Usuario:</strong> <?= htmlspecialchars($prestamo['usuario']) ?></p>
    <p><strong>Libro:</strong> <?= htmlspecialchars($prestamo['libro']) ?></p>
    <p><strong>Fecha devolución pactada:</strong> <?= $prestamo['fecha_devolucion'] ?></p>

    <form id="formDevolucion">
        <input type="hidden" name="id" value="<?= $prestamo['id'] ?>">
        <button class="btn btn-primary">Confirmar devolución</button>
    </form>

    <div id="resultado" class="mt-3"></div>
</div>

<script src="../assets/js/prestamos.js"></script>
<?php include("../includes/footer.php"); ?>
