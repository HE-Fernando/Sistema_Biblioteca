<?php
include("../includes/auth.php");
include("../includes/header.php");
include("../config/database.php");
?>

<div class="container">
  <h2>🔎 Buscar Libros</h2>
  <form id="form-busqueda" class="busqueda-form">
    <label>Título:</label>
    <input type="text" id="titulo" name="titulo" placeholder="Ej: Cien años de soledad">
    
    <label>Autor:</label>
    <input type="text" id="autor" name="autor" placeholder="Ej: García Márquez">
    
    <label>ISBN:</label>
    <input type="text" id="isbn" name="isbn" placeholder="Ej: 978-84-376-0494-7">
    
    <label>Categoría:</label>
    <input type="text" id="categoria" name="categoria" placeholder="Ej: Novela, Historia...">

    <button type="button" id="btnBuscar" class="btn btn-primary">Buscar</button>
  </form>

  <hr>
  <h3>Resultados:</h3>
  <table class="tabla">
    <thead>
      <tr>
        <th>ID</th>
        <th>Título</th>
        <th>Autor</th>
        <th>ISBN</th>
        <th>Categoría</th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody id="resultados">
      <tr><td colspan="7" style="text-align:center;">🔍 Ingrese criterios y presione “Buscar”</td></tr>
    </tbody>
  </table>
</div>

<script>
// JS simple con Fetch y comentarios
document.addEventListener('DOMContentLoaded', () => {
  const btnBuscar = document.getElementById('btnBuscar');
  const resultados = document.getElementById('resultados');

  btnBuscar.addEventListener('click', async () => {
    // Tomar valores del formulario
    const titulo = document.getElementById('titulo').value;
    const autor = document.getElementById('autor').value;
    const isbn = document.getElementById('isbn').value;
    const categoria = document.getElementById('categoria').value;

    // Crear URL con parámetros (query string)
    const params = new URLSearchParams({ titulo, autor, isbn, categoria });

    // Llamar al endpoint AJAX
    const res = await fetch(`/api/libros.php?${params.toString()}`);
    const data = await res.json();

    // Mostrar resultados
    resultados.innerHTML = '';
    if (data.length === 0) {
      resultados.innerHTML = '<tr><td colspan="7" style="text-align:center;">❌ No se encontraron resultados.</td></tr>';
      return;
    }

    for (const l of data) {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${l.id}</td>
        <td>${l.titulo}</td>
        <td>${l.autor}</td>
        <td>${l.isbn}</td>
        <td>${l.categoria ?? '-'}</td>
        <td><span class="badge ${l.estado === 'disponible' ? 'verde' : 'gris'}">${l.estado}</span></td>
        <td>
          <a href="detalle.php?id=${l.id}">👁 Ver</a> |
          <a href="editar.php?id=${l.id}">✏ Editar</a>
          ${l.estado === 'disponible' ? `| <a href="eliminar.php?id=${l.id}" onclick="return confirm('¿Eliminar libro?')">🗑</a>` : ''}
        </td>
      `;
      resultados.appendChild(tr);
    }
  });
});
</script>

<?php include("../includes/footer.php"); ?>
