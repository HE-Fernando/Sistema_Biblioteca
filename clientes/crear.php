<?php
session_start();
if (!isset($_SESSION["usuario"])){
    header("Location: ../biblioteca/login.php");
    exit();
}
require_once '../config/conexion_db.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST["nombre"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    $direccion = trim($_POST["direccion"] ?? "");
    $dni = trim($_POST["dni"] ?? "");
    $estado = "activo"; //POR DEFECTO AL CREAR

    if (empty($nombre) || empty($dni) || empty($telefono)) {
        echo json_encode([
            'success' => false,
            'message' => 'Faltan datos'
        ]);
        exit;
    }
    
    try{
        $sql = "INSERT INTO usuario (nombre_completo, email, telefono, direccion, dni, estado)
        VALUES (?, ?, ?, ?, ?, ?)";
        $stm = $pdo->prepare($sql);
        $stm->execute([$nombre, $email, $telefono, $direccion, $dni, $estado]);

        echo json_encode([
            "success" => true,
            "message" => "Cliente agregado correctamente"
        ]);
    } catch (PDOException $e){
        echo json_encode([
            "success" => false,
            "message" => "Error al agregar cliente: " . $e->getMessage()
        ]);
    }
    exit;
}
echo json_encode([
    "success" => false,
    "message" => "Método no permitido"
]);
?>