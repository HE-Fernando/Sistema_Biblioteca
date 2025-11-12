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
    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $direccion = $_POST["direccion"] ?? "";
    $dni = $_POST["dni"] ?? null;
    $estado = $_POST['estado'] ?? 'activo';

    if (empty($id) || empty($nombre)) {
        echo json_encode([
            'success' => false,
            'message' => 'Datos necesarios incompletos (ID, Nombre)'
        ]);
        exit;
    }

    $sql = "UPDATE usuario SET nombre_completo = ?, email = ?, telefono = ?, direccion = ?, dni = ?, estado = ?
    WHERE id = ?";
    $stm = $pdo->prepare($sql);
    $ok = $stm->execute([$nombre, $email, $telefono, $direccion, $dni, $estado, $id]);

    if ($ok){
        echo json_encode([
            "success" => true,
            "message" => "Cliente actualizado correctamente"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Error al actualizar cliente"
        ]);
    }
    exit;
}

echo json_encode([
    'success' => false,
    'message' => 'Método no permitido'
]);
?>