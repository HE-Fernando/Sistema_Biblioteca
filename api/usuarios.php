<?php
// api/usuarios.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Opcional: permitir GET público solo para usuarios logueados
require_login();

$q = $_GET['q'] ?? '';
$q = trim($q);

$sql = "SELECT u.id, u.nombre, u.email, u.telefono, u.estado,
       (SELECT COUNT(*) FROM prestamos p WHERE p.usuario_id = u.id AND p.fecha_devolucion IS NULL) as prestamos_activos
       FROM usuarios u
       WHERE u.nombre LIKE :q OR u.email LIKE :q
       ORDER BY u.nombre
       LIMIT 100";
$stmt = $pdo->prepare($sql);
$like = "%$q%";
$stmt->execute([':q' => $like]);
$rows = $stmt->fetchAll();

header('Content-Type: application/json; charset=utf-8');
echo json_encode($rows);
