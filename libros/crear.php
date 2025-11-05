<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $autor = $_POST['autor'] ?? '';
    $isbn = $_POST['isbn'] ?? '';

    if (empty($titulo) || empty($autor) || empty($isbn)) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos']);
        exit;
    }

    $sql = "INSERT INTO libros (titulo, autor, isbn) VALUES (?,?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$titulo, $autor, $isbn]);

    echo json_encode(['success' => true, 'message' => 'Libro agregado correctamente']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Método no permitido']);

