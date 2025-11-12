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
            'message' => 'Falta el ID del libro'
        ]);
        exit;
    }

    $sql = "DELETE FROM libro WHERE id=?";
    $stm = $pdo->prepare($sql);
    $stm->execute([$id]);

    echo json_encode([
        'success' => true,
        'message' => 'Libro eliminado correctamente'
    ]);
    exit;
}

echo json_encode([
    'success' => false,
    'message' => 'Método no permitido'
]);
?>