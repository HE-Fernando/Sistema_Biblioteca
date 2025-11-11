<?php
require_once '../config/conexion_db.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 0;
    $titulo = $_POST['titulo'] ?? '';
    $autor = $_POST['autor'] ?? '';
    $isbn = $_POST['isbn'] ?? '';
    $editorial = $_POST["editorial"] ?? "";
    $anio = $_POST["anio"] ?? null;
    $categoria = $_POST['categoria'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';

    if (empty($id) || empty($titulo) || empty($autor) || empty($isbn)) {
        echo json_encode([
            'success' => false,
            'message' => 'Datos incompletos'
        ]);
        exit;
    }

    $sql = "UPDATE libro SET titulo = ?, autor = ?, isbn = ?, editorial = ?, anio = ?, categoria = ?, descripcion = ?
    WHERE id = ?";
    $stm = $pdo->prepare($sql);
    $ok = $stm->execute([$titulo, $autor, $isbn, $editorial, $anio, $categoria, $descripcion, $id]);

    if ($ok){
        echo json_encode([
            "success" => true,
            "message" => "Libro actualizado correctamente"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Error al actualizar libro"
        ]);
    }
    exit;
}

echo json_encode([
    'success' => false,
    'message' => 'Método no permitido'
]);
?>