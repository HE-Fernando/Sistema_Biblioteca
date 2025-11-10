<<?php
include("../config/database.php");

$q = $_GET['q'] ?? '';

$stmt = $pdo->prepare("SELECT id, titulo, autor, isbn, estado FROM libros WHERE titulo LIKE :q OR autor LIKE :q LIMIT 20");
$stmt->bindValue(':q', "%$q%");
$stmt->execute();
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
