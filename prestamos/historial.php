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
        //Buscar por nombre, dni, titulo
        $sql = "SELECT
        prestamos.id,
        libro.titulo,
        usuario.nombre_completo,
        usuario.dni,
        prestamos.fecha_prestamo,
        prestamos.fecha_devolucion,
        prestamos.fecha_dev_real,
        prestamos.estado,
        prestamos.observaciones
        FROM prestamos
        INNER JOIN usuario ON prestamos.usuario_id = usuario.id
        INNER JOIN libro ON prestamos.libro_id = libro.id
        WHERE usuario.nombre_completo LIKE ? OR usuario.dni LIKE ? OR libro.titulo LIKE ?
        ORDER BY prestamos.id DESC";

        $stm = $pdo->prepare($sql);
        $like = "%$buscar%";
        $stm->execute([$like, $like, $like]);
    } else {
        //Si no hay parametros de busqueda, listar todos los clientes
        $sql = "SELECT
        prestamos.id,
        libro.titulo,
        usuario.nombre_completo,
        usuario.dni,
        prestamos.fecha_prestamo,
        prestamos.fecha_devolucion,
        prestamos.fecha_dev_real,
        prestamos.estado,
        prestamos.observaciones
        FROM prestamos
        INNER JOIN usuario ON prestamos.usuario_id = usuario.id
        INNER JOIN libro ON prestamos.libro_id = libro.id
        ORDER BY prestamos.id DESC";
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
        "message" => "Error al buscar reservas: " . $e->getMessage()
    ]);
}
?>