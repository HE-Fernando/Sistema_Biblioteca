<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Gestión de Libros (AJAX)</title>
<style>
    body { font-family: Arial; margin: 20px; }
    input, button { margin: 5px; }
    table { border-collapse: collapse; width: 100%; margin-top: 15px; }
    th, td { border: 1px solid #ccc; padding: 5px; }
</style>
</head>
<body>

<h2>Gestión de Libros</h2>

<!-- Formulario para crear -->
<h3>Agregar libro</h3>
<form id="form-crear">
    <input type="text" name="titulo" placeholder="Título" required>
    <input type="text" name="autor" placeholder="Autor" required>
    <input type="text" name="isbn" placeholder="ISBN" required>
    <button type="submit">Guardar</button>
</form>

<!-- Formulario de búsqueda -->
<h3>Buscar libros</h3>
<input type="text" id="buscar" placeholder="Buscar...">
<button onclick="buscarLibros()">Buscar</button>

<table id="tabla-libros">
    <thead>
        <tr><th>Título</th><th>Autor</th><th>ISBN</th><th>Acciones</th></tr>
    </thead>
    <tbody></tbody>
</table>

<script>
// Crear libro
document.getElementById('form-crear').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const res = await fetch('crear.php', { method: 'POST', body: formData });
    const data = await res.json();
    alert(data.message);
    buscarLibros();
    e.target.reset();
});

// Buscar libros
async function buscarLibros() {
    const q = document.getElementById('buscar').value;
    const res = await fetch('buscar.php?buscar=' + encodeURIComponent(q));
    const data = await res.json();

    const tbody = document.querySelector('#tabla-libros tbody');
    tbody.innerHTML = '';

    if (data.success && data.data.length > 0) {
        data.data.forEach(libro => {
            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td><input type="text" value="${libro.titulo}" id="titulo-${libro.id}"></td>
                <td><input type="text" value="${libro.autor}" id="autor-${libro.id}"></td>
                <td><input type="text" value="${libro.isbn}" id="isbn-${libro.id}"></td>
                <td>
                    <button onclick="editarLibro(${libro.id})">Guardar</button>
                    <button onclick="eliminarLibro(${libro.id})">Eliminar</button>
                </td>`;
            tbody.appendChild(fila);
        });
    } else {
        tbody.innerHTML = '<tr><td colspan="4">Sin resultados</td></tr>';
    }
}

// Editar libro
async function editarLibro(id) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('titulo', document.getElementById('titulo-' + id).value);
    formData.append('autor', document.getElementById('autor-' + id).value);
    formData.append('isbn', document.getElementById('isbn-' + id).value);

    const res = await fetch('editar.php', { method: 'POST', body: formData });
    const data = await res.json();
    alert(data.message);
    buscarLibros();
}

// Eliminar libro
async function eliminarLibro(id) {
    if (!confirm('¿Desea eliminar este libro?')) return;

    const formData = new FormData();
    formData.append('id', id);

    const res = await fetch('eliminar.php', { method: 'POST', body: formData });
    const data = await res.json();
    alert(data.message);
    buscarLibros();
}

// Buscar automáticamente al cargar
buscarLibros();
</script>
</body>
</html>

