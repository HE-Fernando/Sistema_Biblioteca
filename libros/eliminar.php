<?php
session_start();
if (!isset($_SESSION["usuario"])){
    header("Location: ../biblioteca/login.php");
    exit();
}
require_once '../config/conexion_db.php';

$id = $_GET['id'] ?? null;
if (!$id) die("ID no válido.");

$stmt = $pdo->prepare("DELETE FROM libros WHERE id=? AND estado='disponible'");
$stmt->execute([$id]);

<<<<<<< HEAD
echo "<p>✅ Libro eliminado correctamente.</p>";
echo "<a href='index.php'>Volver</a>";

include("../includes/footer.php");
?>

<<<<<<< HEAD

=======
>>>>>>> eduardo

=======
    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'Falta el ID del libro'
        ]);
        exit;
    }

    $sql = "DELETE FROM libro WHERE id=?";
    $stm = $pdo->prepare($sql);
    $stm->execute([$id]);

    echo json_encode([
        'success' => true,
        'message' => 'Libro eliminado correctamente'
    ]);
    exit;
}

echo json_encode([
    'success' => false,
    'message' => 'Método no permitido'
]);
?>
>>>>>>> origin/Fernando
