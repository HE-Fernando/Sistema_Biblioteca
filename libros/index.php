<?php
include("../includes/auth.php");
include("../includes/header.php");
include("../config/database.php");

// Búsqueda
$q = $_GET['q'] ?? '';

// Paginación
$por_pagina = 10;
$pagina = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($pagina - 1) * $por_pagina;

// Consulta
$sql = "SELECT * FROM libros 
        WHERE titulo LIKE :q OR autor LIKE :q
        ORDER BY id DESC
        LIMIT :offset, :por_pagina";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':q', "%$q%");
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':por_pagina', $por_pagina, PDO::PARAM_INT);
$stmt->execute();
$libros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
  <h2>📚 Libros</h2>
  <div class="acciones">
    <a href="crear.php" class="btn btn-success">+ Nuevo Libro</a>
    <input type="text" id="busqueda" placeholder="Buscar por título o autor">
  </div>

  <table class="tabla">
    <thead>
      <tr>
        <th>ID</th>
        <th>Título</th>
        <th>Autor</th>
        <th>ISBN</th>
        <th>Año</th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody id="tabla-libros">
      <?php foreach ($libros as $l): ?>
        <tr>
          <td><?= $l['id'] ?></td>
          <td><?= htmlspecialchars($l['titulo']) ?></td>
          <td><?= htmlspecialchars($l['autor']) ?></td>
          <td><?= htmlspecialchars($l['isbn']) ?></td>
          <td><?= $l['anio'] ?></td>
          <td>
            <span class="badge <?= $l['estado'] == 'disponible' ? 'verde' : 'gris' ?>">
              <?= $l['estado'] ?>
            </span>
          </td>
          <td>
            <a href="detalle.php?id=<?= $l['id'] ?>">👁 Ver</a> |
            <a href="editar.php?id=<?= $l['id'] ?>">✏ Editar</a>
            <?php if ($l['estado'] == 'disponible'): ?> |
              <a href="eliminar.php?id=<?= $l['id'] ?>" onclick="return confirm('¿Eliminar este libro?')">🗑 Eliminar</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script src="../assets/js/busquedas_libros.js"></script>
<?php include("../includes/footer.php"); ?>
