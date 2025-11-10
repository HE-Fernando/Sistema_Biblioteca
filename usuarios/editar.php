<?php
// usuarios/editar.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    exit('ID inválido.');
}

// cargar
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
$stmt->execute([':id'=>$id]);
$user = $stmt->fetch();
if (!$user) exit('Usuario no encontrado.');

$errors = [];
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['_csrf'] ?? '')) $errors[] = 'Token CSRF inválido.';

    $nombre = trim($_POST['nombre'] ?? '');
    $email  = strtolower(trim($_POST['email'] ?? ''));
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $dni = trim($_POST['dni'] ?? '');
    $estado = in_array($_POST['estado'] ?? '', ['activo','suspendido','inactivo']) ? $_POST['estado'] : 'activo';
    $password = trim($_POST['password'] ?? '');

    if ($nombre === '') $errors[] = 'Nombre es obligatorio.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido.';
    if ($dni === '') $errors[] = 'DNI es obligatorio.';

    if (empty($errors)) {
        // verificar unicidad (excluyendo este id)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE (email = :email OR dni = :dni) AND id != :id");
        $stmt->execute([':email'=>$email,':dni'=>$dni,':id'=>$id]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'Email o DNI ya en uso por otro usuario.';
        } else {
            $pdo->beginTransaction();
            try {
                $sql = "UPDATE usuarios SET nombre=:nombre,email=:email,telefono=:telefono,direccion=:direccion,dni=:dni,estado=:estado";
                if ($password !== '') {
                    $sql .= ", password = :password";
                }
                $sql .= " WHERE id = :id";
                $stmt = $pdo->prepare($sql);

                $params = [
                    ':nombre'=>$nombre,
                    ':email'=>$email,
                    ':telefono'=>$telefono ?: null,
                    ':direccion'=>$direccion ?: null,
                    ':dni'=>$dni,
                    ':estado'=>$estado,
                    ':id'=>$id
                ];
                if ($password !== '') $params[':password'] = password_hash($password, PASSWORD_DEFAULT);

                $stmt->execute($params);
                $pdo->commit();
                $success = true;
                // recargar datos
                $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
                $stmt->execute([':id'=>$id]);
                $user = $stmt->fetch();
            } catch (Exception $e){
                $pdo->rollBack();
                $errors[] = 'Error al actualizar.';
            }
        }
    }
}
$token = csrf_token();
?>

<h1>Editar Usuario #<?= e($user['id']) ?></h1>

<?php if($success): ?>
  <div class="ok">Guardado correctamente.</div>
<?php endif; ?>

<?php if($errors): ?>
  <div class="errors">
    <ul><?php foreach($errors as $err) echo '<li>'.e($err).'</li>'; ?></ul>
  </div>
<?php endif; ?>

<form method="post" action="">
  <input type="hidden" name="_csrf" value="<?= e($token) ?>">
  <label>Nombre completo*<br><input name="nombre" value="<?= e($user['nombre']) ?>"></label><br>
  <label>Email*<br><input name="email" value="<?= e($user['email']) ?>"></label><br>
  <label>Teléfono<br><input name="telefono" value="<?= e($user['telefono']) ?>"></label><br>
  <label>Dirección<br><input name="direccion" value="<?= e($user['direccion']) ?>"></label><br>
  <label>DNI*<br><input name="dni" value="<?= e($user['dni']) ?>"></label><br>
  <label>Estado<br>
    <select name="estado">
      <option value="activo" <?= $user['estado']=='activo' ? 'selected':'' ?>>Activo</option>
      <option value="suspendido" <?= $user['estado']=='suspendido' ? 'selected':'' ?>>Suspendido</option>
      <option value="inactivo" <?= $user['estado']=='inactivo' ? 'selected':'' ?>>Inactivo</option>
    </select>
  </label><br>
  <label>Cambiar contraseña (dejar vacío para no cambiar)<br><input type="password" name="password"></label><br>
  <button type="submit">Guardar</button>
  <a href="index.php">Volver</a>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>


