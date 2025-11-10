<?php
// usuarios/crear.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';
//require_login();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF
    if (!csrf_check($_POST['_csrf'] ?? '')) {
        $errors[] = 'Token CSRF inválido.';
    }

    // limpiar y validar
    $nombre = trim($_POST['nombre'] ?? '');
    $email  = strtolower(trim($_POST['email'] ?? ''));
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $dni = trim($_POST['dni'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($nombre === '') $errors[] = 'Nombre es obligatorio.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido.';
    if ($dni === '') $errors[] = 'DNI es obligatorio.';

    if (empty($errors)) {
        // verificar unicidad
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email OR dni = :dni");
        $stmt->execute([':email'=>$email, ':dni'=>$dni]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'Email o DNI ya registrado.';
        } else {
            // insertar
            $pdo->beginTransaction();
            try {
                $sql = "INSERT INTO usuarios (nombre,email,telefono,direccion,dni,password,estado,created_by)
                        VALUES (:nombre,:email,:telefono,:direccion,:dni,:password,:estado,:created_by)";
                $stmt = $pdo->prepare($sql);

                $pwdHash = null;
                if ($password !== '') {
                    $pwdHash = password_hash($password, PASSWORD_DEFAULT);
                }

                $stmt->execute([
                    ':nombre'=>$nombre,
                    ':email'=>$email,
                    ':telefono'=>$telefono ?: null,
                    ':direccion'=>$direccion ?: null,
                    ':dni'=>$dni,
                    ':password'=>$pwdHash,
                    ':estado'=>'activo',
                    ':created_by'=>$_SESSION['user_id'] ?? null
                ]);
                $pdo->commit();
                $success = true;
            } catch (Exception $e){
                $pdo->rollBack();
                $errors[] = 'Error al guardar. ';
            }
        }
    }
}
$token = csrf_token();
?>

<h1>Agregar Usuario</h1>

<?php if($success): ?>
  <div class="ok">Registro creado correctamente. <a href="index.php">Volver al listado</a></div>
<?php endif; ?>

<?php if($errors): ?>
  <div class="errors">
    <ul><?php foreach($errors as $err) echo '<li>'.e($err).'</li>'; ?></ul>
  </div>
<?php endif; ?>

<form method="post" action="">
  <input type="hidden" name="_csrf" value="<?= e($token) ?>">
  <label>Nombre completo*<br><input name="nombre" value="<?= e($_POST['nombre'] ?? '') ?>"></label><br>
  <label>Email*<br><input name="email" value="<?= e($_POST['email'] ?? '') ?>"></label><br>
  <label>Teléfono<br><input name="telefono" value="<?= e($_POST['telefono'] ?? '') ?>"></label><br>
  <label>Dirección<br><input name="direccion" value="<?= e($_POST['direccion'] ?? '') ?>"></label><br>
  <label>DNI*<br><input name="dni" value="<?= e($_POST['dni'] ?? '') ?>"></label><br>
  <label>Contraseña (opcional)<br><input type="password" name="password"></label><br>
  <button type="submit">Guardar</button>
  <a href="index.php">Cancelar</a>
</form>

<script src="/assets/js/validaciones.js" defer></script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
