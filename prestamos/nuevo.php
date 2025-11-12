<?php
session_start();
if (!isset($_SESSION["usuario"])){
    header("Location: ../biblioteca/login.php");
    exit();
}
require_once '../config/conexion_db.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $libro_id = trim($_POST["libro_id"] ?? "");
    $usuario_id = trim($_POST["usuario_id"] ?? "");
    $fecha_devolucion = trim($_POST["fecha_devolucion"] ?? "");
    $estado = "prestado";
    $observaciones = trim($_POST["observaciones"] ?? "");

    if (empty($libro_id) || empty($usuario_id) || empty($fecha_devolucion)) {
        echo json_encode([
            'success' => false,
            'message' => 'Faltan datos'
        ]);
        exit;
    }
    
    try{
        $sql = "INSERT INTO prestamos (libro_id, usuario_id, fecha_devolucion, estado, observaciones)
        VALUES (?, ?, ?, ?, ?)";
        $stm = $pdo->prepare($sql);
        $stm->execute([$libro_id, $usuario_id, $fecha_devolucion, $estado, $observaciones]);

        $sqlLibro = "UPDATE libro SET estado = 'prestado' WHERE id = ?";
        $stmLibro = $pdo->prepare($sqlLibro);
        $stmLibro->execute([$libro_id]);

        echo json_encode([
            "success" => true,
            "message" => "Prestamo agregado correctamente"
        ]);
    } catch (PDOException $e){
        echo json_encode([
            "success" => false,
            "message" => "Error al agregar prestamo: " . $e->getMessage()
        ]);
    }
    exit;
}
echo json_encode([
    "success" => false,
    "message" => "Método no permitido"
]);
?>