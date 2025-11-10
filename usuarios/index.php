<?php
// usuarios/index.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';

require_login();
$token = csrf_token();
?>

<h1>Usuarios</h1>

<div>
  <a href="crear.php">Agregar usuario</a>
</div>

<input id="q" placeholder="Buscar por nombre o email" />
<button id="buscarBtn">Buscar</button>

<table border="1" id="tabla-usuarios">
  <thead>
    <tr>
      <th>ID</th><th>Nombre</th><th>Email</th><th>Teléfono</th><th>Estado</th><th>Préstamos activos</th><th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <!-- Cuerpo rellenado por fetch AJAX -->
  </tbody>
</table>

<script>
  // token para peticiones POST si hiciera falta
  const CSRF_TOKEN = "<?= $token ?>";
</script>
<script src="/assets/js/busquedas.js" defer></script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
