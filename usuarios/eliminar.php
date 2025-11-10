<?php
// usuarios/eliminar.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) exit('ID inválido.');

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
$stmt->execute([':id'=>$id]);
$user = $stmt->fetch();
if (!$user) exit('Usuario no encontrado.');

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['_csrf'] ?? '')) $errors[] = 'Token CSRF inválido.';

    // verificar préstamos activos
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM prestamos WHERE usuario_id = :id AND fecha_devolucion IS NULL");
    $stmt->execute([':id'=>$id]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = 'No se puede eliminar: tiene préstamos activos.';
    } else {
        // en vez de borrar, marcar inactivo
        $stmt = $pdo->prepare("UPDATE usuarios SET estado='inactivo' WHERE id = :id");
        $stmt->execute([':id'=>$id]);
        $success = true;
    }
}

$token = csrf_token();
?>

<h1>Eliminar / Inactivar Usuario</h1>
<p>¿Confirmar inactivar al usuario <strong><?= e($user['nombre']) ?></strong> (ID <?= e($user['id']) ?>)?</p>

<?php if ($errors): ?>
  <div class="errors">
    <ul><?php foreach($errors as $err) echo '<li>'.e($err).'</li>'; ?></ul>
  </div>
<?php endif; ?>

<?php if ($success): ?>
  <div class="ok">Usuario inactivado correctamente. <a href="index.php">Volver al listado</a></div>
<?php else: ?>
  <form method="post" action="">
    <input type="hidden" name="_csrf" value="<?= e($token) ?>">
    <button type="submit">Confirmar inactivar</button>
    <a href="index.php">Cancelar</a>
  </form>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>


