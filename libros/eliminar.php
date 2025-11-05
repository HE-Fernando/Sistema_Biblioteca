<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 0;

    if (empty($id)) {
        echo json_encode(['success' => false, 'message' => 'Falta el ID del libro']);
        exit;
    }

    $sql = "DELETE FROM libros WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    echo json_encode(['success' => true, 'message' => 'Libro eliminado correctamente']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Método no permitido']);

