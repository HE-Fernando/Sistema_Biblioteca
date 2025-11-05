<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 0;
    $titulo = $_POST['titulo'] ?? '';
    $autor = $_POST['autor'] ?? '';
    $isbn = $_POST['isbn'] ?? '';

    if (empty($id) || empty($titulo) || empty($autor) || empty($isbn)) {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
        exit;
    }

    $sql = "UPDATE libros SET titulo=?, autor=?, isbn=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$titulo, $autor, $isbn, $id]);

    echo json_encode(['success' => true, 'message' => 'Libro actualizado']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Método no permitido']);
