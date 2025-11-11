<?php
session_start();
if (!isset($_SESSION["usuario"])){
    header("Location: ../biblioteca/login.php");
    exit();
}
require_once '../config/conexion_db.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST["titulo"] ?? "");
    $autor = trim($_POST["autor"] ?? "");
    $isbn = trim($_POST["isbn"] ?? "");
    $editorial = trim($_POST["editorial"] ?? "");
    $anio = trim($_POST["anio"] ?? "");
    $categoria = trim($_POST["categoria"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $estado = "disponible"; //POR DEFECTO AL CREAR

    if (empty($titulo) || empty($autor) || empty($isbn)) {
        echo json_encode(['success' => false,
        'message' => 'Faltan datos']);
        exit;
    }
    
    try{
        $sql = "INSERT INTO libro (titulo, autor, isbn, editorial, anio, categoria, descripcion, estado)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stm = $pdo->prepare($sql);
        $stm->execute([$titulo, $autor, $isbn, $editorial, $anio, $categoria, $descripcion, $estado]);

        echo json_encode([
            "success" => true,
            "message" => "Libro agregado correctamente"
        ]);
    } catch (PDOException $e){
        echo json_encode([
            "success" => false,
            "message" => "Error al agregar libro: " . $e->getMessage()
        ]);
    }
    exit;
}
echo json_encode([
    "success" => false,
    "message" => "Método no permitido"
]);

