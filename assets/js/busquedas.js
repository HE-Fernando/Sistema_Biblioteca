// assets/js/busquedas.js
document.addEventListener('DOMContentLoaded', ()=> {
  const qInput = document.getElementById('q');
  const btn = document.getElementById('buscarBtn');
  const tbody = document.querySelector('#tabla-usuarios tbody');

  async function fetchUsuarios(q='') {
    const url = '/api/usuarios.php?q=' + encodeURIComponent(q);
    const res = await fetch(url, { credentials: 'same-origin' });
    const data = await res.json();
    tbody.innerHTML = '';
    for (const u of data) {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${u.id}</td>
        <td>${escapeHtml(u.nombre)}</td>
        <td>${escapeHtml(u.email)}</td>
        <td>${escapeHtml(u.telefono||'')}</td>
        <td>${escapeHtml(u.estado)}</td>
        <td>${u.prestamos_activos}</td>
        <td>
          <a href="/usuarios/detalle.php?id=${u.id}">Ver</a> |
          <a href="/usuarios/editar.php?id=${u.id}">Editar</a> |
          <a href="/usuarios/eliminar.php?id=${u.id}">Eliminar</a>
        </td>
      `;
      tbody.appendChild(tr);
    }
  }

  btn.addEventListener('click', ()=> fetchUsuarios(qInput.value));
  qInput.addEventListener('keyup', (e)=> {
    if (e.key === 'Enter') fetchUsuarios(qInput.value);
  });

  // inicial
  fetchUsuarios();

  function escapeHtml(unsafe) {
    if (!unsafe) return '';
    return unsafe
      .replaceAll('&','&amp;')
      .replaceAll('<','&lt;')
      .replaceAll('>','&gt;')
      .replaceAll('"','&quot;')
      .replaceAll("'",'&#039;');
  }
});
