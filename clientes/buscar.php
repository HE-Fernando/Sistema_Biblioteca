<?php
session_start();
if (!isset($_SESSION["usuario"])){
    header("Location: ../biblioteca/login.php");
    exit();
}
require_once '../config/conexion_db.php';

header('Content-Type: application/json; charset=utf-8');

$buscar = $_GET['buscar'] ?? '';

try {
    if (!empty($buscar)){
        //Buscar por nombre, dni
        $sql = "SELECT id, nombre_completo, email, telefono, direccion, dni, estado
        FROM usuario WHERE nombre_completo LIKE ? OR dni LIKE ? ORDER BY id DESC";
        $stm = $pdo->prepare($sql);
        $like = "%$buscar%";
        $stm->execute([$like, $like]);
    } else {
        //Si no hay parametros de busqueda, listar todos los clientes
        $sql = "SELECT id, nombre_completo, email, telefono, direccion, dni, estado
        FROM usuario ORDER BY id DESC";
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
        "message" => "Error al buscar clientes: " . $e->getMessage()
    ]);
}
?>