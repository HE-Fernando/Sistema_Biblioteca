<?php
include("../config/database.php");

$accion = $_POST['accion'] ?? '';

if ($accion === 'crear') {
    $usuario = $_POST['usuario_id'];
    $libro = $_POST['libro_id'];
    $fecha_prestamo = $_POST['fecha_prestamo'];
    $fecha_devolucion = $_POST['fecha_devolucion'];

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("
            INSERT INTO prestamos (usuario_id, libro_id, fecha_prestamo, fecha_devolucion, estado)
            VALUES (?, ?, ?, ?, 'activo')
        ");
        $stmt->execute([$usuario, $libro, $fecha_prestamo, $fecha_devolucion]);

        $pdo->prepare("UPDATE libros SET estado = 'prestado' WHERE id = ?")->execute([$libro]);
        $pdo->commit();
        echo "✅ Préstamo registrado correctamente.";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "❌ Error: " . $e->getMessage();
    }
}

if ($accion === 'devolver') {
    $id = $_POST['id'];
    $hoy = date('Y-m-d');

    $stmt = $pdo->prepare("SELECT libro_id, fecha_devolucion FROM prestamos WHERE id = ? AND estado = 'activo'");
    $stmt->execute([$id]);
    $p = $stmt->fetch();

    if (!$p) {
        echo "❌ No se encontró el préstamo activo."; exit;
    }

    $dias_atraso = (strtotime($hoy) - strtotime($p['fecha_devolucion'])) / 86400;
    $pdo->beginTransaction();
    try {
        $pdo->prepare("UPDATE prestamos SET estado='devuelto', fecha_dev_real=? WHERE id=?")->execute([$hoy, $id]);
        $pdo->prepare("UPDATE libros SET estado='disponible' WHERE id=?")->execute([$p['libro_id']]);
        $pdo->commit();

        if ($dias_atraso > 0)
            echo "⚠️ Devolución con $dias_atraso días de atraso.";
        else
            echo "✅ Devolución registrada a tiempo.";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "❌ Error al registrar devolución.";
    }
}
