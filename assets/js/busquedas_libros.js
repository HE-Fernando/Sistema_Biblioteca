document.addEventListener('DOMContentLoaded', () => {
  const input = document.getElementById('busqueda');
  const tbody = document.getElementById('tabla-libros');

  input.addEventListener('keyup', async () => {
    const q = input.value.trim();
    const res = await fetch(`/api/libros.php?q=${encodeURIComponent(q)}`);
    const data = await res.json();

    tbody.innerHTML = '';
    for (const l of data) {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${l.id}</td>
        <td>${l.titulo}</td>
        <td>${l.autor}</td>
        <td>${l.isbn}</td>
        <td><span class="badge ${l.estado === 'disponible' ? 'verde' : 'gris'}">${l.estado}</span></td>
        <td>
          <a href="/libros/detalle.php?id=${l.id}">👁</a> |
          <a href="/libros/editar.php?id=${l.id}">✏</a>
          ${l.estado === 'disponible' ? `| <a href="/libros/eliminar.php?id=${l.id}" onclick="return confirm('¿Eliminar?')">🗑</a>` : ''}
        </td>
      `;
      tbody.appendChild(tr);
    }
  });
});
