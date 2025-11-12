<?php
session_start();
if (!isset($_SESSION["usuario"])){
    header("Location: ../biblioteca/login.php");
    exit();
}
require_once '../config/conexion_db.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    try {
        $sql = "UPDATE prestamos
                SET estado = 'atrasado'
                WHERE estado = 'prestado'
                AND fecha_devolucion < CURDATE()";
        
        $stm = $pdo->prepare($sql);
        $stm->execute();

        $registrosAfectados = $stm->rowCount();

        echo json_encode([
            "success" => true,
            "message" => "Actualización completada. $registrosAfectados préstamo(s) marcado(s) como atrasado(s)."
        ]);
        exit;
    } catch (PDOException $e){
        echo json_encode([
            "success" => false,
            "message" => "Error al actualizar los préstamos: " . $e->getMessage()
        ]);
        exit;
    }
}

echo json_encode([
    'success' => false,
    'message' => 'Método no permitido'
]);
?>