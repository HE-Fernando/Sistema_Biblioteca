<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: ../biblioteca/login.php");
    exit();
}

require_once '../config/conexion_db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    //usuarios
    $sqlUsuarios = "SELECT id, nombre_completo, dni FROM usuario ORDER BY nombre_completo ASC";
    $stmUsuarios = $pdo->query($sqlUsuarios);
    $usuarios = $stmUsuarios->fetchAll(PDO::FETCH_ASSOC);

    //libros (disponibles)
    $sqlLibros = "SELECT id, titulo, isbn FROM libro WHERE estado = 'disponible' ORDER BY titulo ASC";
    $stmLibros = $pdo->query($sqlLibros);
    $libros = $stmLibros->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "usuarios" => $usuarios,
        "libros" => $libros
    ]);
    exit;
} catch (PDOException $e){
    echo json_encode([
        "success" => false,
        "message" => "Error al obtener datos: " . $e->getMessage()
    ]);
    exit;
}
?>