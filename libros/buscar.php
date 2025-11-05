<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

header('Content-Type: application/json; charset=utf-8');

$buscar = $_GET['buscar'] ?? '';

if (!empty($buscar)) {
    $sql = "SELECT * FROM libros 
            WHERE titulo LIKE ? OR autor LIKE ? OR isbn LIKE ?";
    $stmt = $pdo->prepare($sql);
    $like = "%$buscar%";
    $stmt->execute([$like, $like, $like]);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $resultados]);
    exit;
}

echo json_encode(['success' => false, 'data' => []]);

