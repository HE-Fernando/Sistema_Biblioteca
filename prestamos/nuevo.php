<?php
include("../includes/header.php");
include("../config/database.php");

$usuarios = $pdo->query("SELECT id, nombre FROM usuarios")->fetchAll();
$libros = $pdo->query("SELECT id, titulo FROM libros WHERE estado = 'disponible'")->fetchAll();
?>

<div class="container mt-4">
    <h2>➕ Nuevo Préstamo</h2>

    <form id="formPrestamo">
        <div class="mb-3">
            <label>Usuario</label>
            <select name="usuario_id" class="form-select" required>
                <option value="">Seleccione...</option>
                <?php foreach ($usuarios as $u): ?>
                    <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Libro</label>
            <select name="libro_id" class="form-select" required>
                <option value="">Seleccione...</option>
                <?php foreach ($libros as $l): ?>
                    <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['titulo']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="row">
            <div class="col">
                <label>Fecha préstamo</label>
                <input type="date" name="fecha_prestamo" id="fecha_prestamo" class="form-control" required>
            </div>
            <div class="col">
                <label>Fecha devolución</label>
                <input type="date" name="fecha_devolucion" id="fecha_devolucion" class="form-control" required>
            </div>
        </div>

        <button class="btn btn-success mt-3">Confirmar Préstamo</button>
    </form>

    <div id="resultado" class="mt-3"></div>
</div>

<script src="../assets/js/prestamos.js"></script>
<?php include("../includes/footer.php"); ?>

