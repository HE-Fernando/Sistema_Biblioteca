<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
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
            'message' => 'Falta el ID del cliente'
        ]);
        exit;
    }

    $sql = "DELETE FROM usuario WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    echo json_encode([
        'success' => true,
        'message' => 'Cliente eliminado correctamente'
    ]);
    exit;
}

echo json_encode([
    'success' => false,
    'message' => 'Método no permitido'
]);
?>