<?php
include("../includes/auth.php");
include("../includes/header.php");
include("../config/database.php");

$id = $_GET['id'] ?? null;
if (!$id) die("ID no válido.");

$stmt = $pdo->prepare("DELETE FROM libros WHERE id=? AND estado='disponible'");
$stmt->execute([$id]);

echo "<p>✅ Libro eliminado correctamente.</p>";
echo "<a href='index.php'>Volver</a>";

include("../includes/footer.php");
?>


