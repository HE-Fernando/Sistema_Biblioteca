<?php
require_once '../config/conexion_db.php';

header('Content-Type: application/json; charset=utf-8');

$buscar = $_GET['buscar'] ?? '';

try {
    if (!empty($buscar)){
        //Buscar por título, autor o ISBN
        $sql = "SELECT id, titulo, autor, isbn, editorial, anio, categoria, estado
        FROM libro WHERE titulo LIKE ? OR autor LIKE ? OR isbn LIKE ? ORDER BY id DESC";
        $stm = $pdo->prepare($sql);
        $like = "%$buscar%";
        $stm->execute([$like, $like, $like]);
    } else {
        //Si no hay parametros de busqueda, listar todos los libros
        $sql = "SELECT id, titulo, autor, isbn, editorial, anio, categoria, estado
        FROM libro ORDER BY id DESC";
        $stm = $pdo->query($sql);
    }
    $resultados = $stm->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "data" => $resultados
    ]);
} catch (PDOException $e){
    echo json_encode([
        "success" => false,
        "message" => "Error al buscar libros: " . $e->getMessage()
    ]);
}
?>