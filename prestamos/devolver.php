<?php
session_start();
if (!isset($_SESSION["usuario"])){
    header("Location: ../biblioteca/login.php");
    exit();
}
require_once '../config/conexion_db.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 0;

    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'Falta el ID del prestamo'
        ]);
        exit;
    }
    //obtengo el id del libro mediante el id del prestamo
    $sqlLibro = "SELECT libro_id FROM prestamos WHERE id = ?";
    $stmLibro = $pdo->prepare($sqlLibro);
    $stmLibro->execute([$id]);
    $resultado = $stmLibro->fetch(PDO::FETCH_ASSOC);
    $libro_id = $resultado["libro_id"];

    //actualizo el estado del prestamo a devuelto
    $sql = "UPDATE prestamos
    SET fecha_dev_real = CURDATE(),
        estado = 'devuelto'
    WHERE id = ?";
    $stm = $pdo->prepare($sql);
    $stm->execute([$id]);

    //actualizo el estado del libro a disponible
    $sqlActualizar = "UPDATE libro SET estado = ? WHERE id = ?";
    $stmActualizar = $pdo->prepare($sqlActualizar);
    $stmActualizar->execute(["disponible", $libro_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Devolución registrada correctamente'
    ]);
    exit;
}

echo json_encode([
    'success' => false,
    'message' => 'Método no permitido'
]);
?>