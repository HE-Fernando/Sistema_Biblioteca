<?php
session_start();
require_once "conexion.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';

    // Buscar usuario en la base
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario=?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Comparar contraseña en texto plano
        if ($password === $row['password']) {
            $_SESSION['usuario'] = $row['usuario'];
            $_SESSION['rol'] = $row['rol'];
            header("Location: index.php");
            exit;
        }
    }

    $error = " Usuario o contraseña incorrecto";
}
?>