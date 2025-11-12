<?php
session_start();
if (!isset($_SESSION["usuario"])){
    header("Location: ../biblioteca/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Libros</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="index">
    <!-- BARRA SUPERIOR -->
    <header class="navbar">
        [<?php echo htmlspecialchars($_SESSION["nombre"]);?>]
        [<?php echo htmlspecialchars($_SESSION["rol"])?>]
        <button id="logoutBtn" class="btn-logout">Cerrar sesión</button>
    </header>

<!-- CONTENEDOR GENERAL -->
<div class="container">
    <!-- MENU LATERAL -->
    <nav class="sidebar">
        <?php
        $pagina_actual = basename($_SERVER['PHP_SELF']);
        ?>
        <a href="../biblioteca/index.php">🏠 Inicio</a>
        <a href="index.php" class="<?= $pagina_actual == 'index.php' ? 'activo' : '' ?>">📖 Libros</a>
        <a href="../clientes/index.php">👥 Clientes</a>
        <a href="../prestamos/index.php">🧾 Préstamos</a>
        <!--<a href="#">📊 Reportes</a> -->
    </nav>


    <!-- CONTENEDOR PRINCIPAL -->
    <main class="main-content">
        <h2>Gestión de Libros</h2>
        <!-- Formulario para crear -->
        <section>
            <h3>Agregar libro</h3>
            <form id="form-crear" class="form-libro">
                <div class="form-row">
                    <label>Titulo:</label>
                    <input type="text" name="titulo" placeholder="Título" required>
                </div>
                <div class="form-row">
                    <label>Autor:</label>
                    <input type="text" name="autor" placeholder="Autor">
                </div>
                <div class="form-row">
                    <label>ISBN:</label>
                    <input type="text" name="isbn" placeholder="ISBN">
                </div>
                <div class="form-row">
                    <label>Editorial:</label>
                    <input type="text" name="editorial" placeholder="Editorial"> 
                </div>
                <div class="form-row">
                    <label>Año:</label>
                    <input type="number" name="anio" min="1000" max="2099" placeholder="Año de publicación"> 
                </div>
                <div class="form-row">
                    <label>Categoría:</label>
                    <input type="text" name="categoria" placeholder="Categoría"> 
                </div>
                <div class="form-row">
                    <label>Descripción:</label>
                    <textarea name="descripcion" placeholder="Breve descripción del libro" rows="3"> </textarea>
                </div>
                <button type="submit" class="btn-guardar">Guardar</button>
            </form>
        </section>

        <!-- Buscador -->
        <section>
            <h3>Buscar libros</h3>
            <input type="text" id="buscar" placeholder="Buscar...">
            <button onclick="buscarLibros()">Buscar</button>
        </section>

        <!-- Tabla -->
        <section>
            <table id="tabla-libros">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>ISBN</th>
                        <th>Editorial</th>
                        <th>Año</th>
                        <th>Categoría</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </section>

        <p id="logoutMessage" class="logout-msg"></p>
    </main>
</div>

<!-- PIE DE PAGINA -->
<footer>
    © <?php echo date("Y"); ?> - Sistema de Biblioteca | Benitez - Hirt
</footer>

<script>
    //LOGOUT
    document.getElementById("logoutBtn").addEventListener("click", async () => {
            const mensaje = document.getElementById("logoutMessage");

            if (!confirm("¿Seguro que querés cerrar sesión?")) {
                return;
            }

            mensaje.textContent = "Cerrando sesión...";
            mensaje.className = "logout-msg";

            try {
                const respuesta = await fetch("../biblioteca/logout.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" }
                });
                const data = await respuesta.json();

                if (data.success) {
                    mensaje.textContent = data.message;
                    mensaje.classList.add("ok");
                    setTimeout(() => {
                        window.location.href = "../biblioteca/login.php";
                    }, 1500);
                } else {
                    mensaje.textContent = "Error al cerrar sesión.";
                    mensaje.classList.add("error");
                }
            } catch (error) {
                console.error("Error: ", error);
                mensaje.textContent = "No se pudo conectar al servidor.";
                mensaje.className = "error";
            }
        });

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
                const fila = document.createElement("tr");
                const colorEstado = libro.estado === "disponible" ? "green" : "red";
                fila.innerHTML = `
                    <td>${libro.id}</td>
                    <td><input type="text" value="${libro.titulo}" id="titulo-${libro.id}"></td>
                    <td><input type="text" value="${libro.autor}" id="autor-${libro.id}"></td>
                    <td><input type="text" value="${libro.isbn}" id="isbn-${libro.id}"></td>
                    <td><input type="text" value="${libro.editorial}" id="editorial-${libro.id}"></td>
                    <td><input type="number" value="${libro.anio}" id="anio-${libro.id}"></td>
                    <td><input type="text" value="${libro.categoria}" id="categoria-${libro.id}"></td>
                    <td style="color:${colorEstado}; font-weight:bold;">${libro.estado}</td>
                    <td>
                        <button onclick="editarLibro(${libro.id})">💾</button>
                        <button onclick="eliminarLibro(${libro.id})">🗑️</button>
                        <button class="info-btn">ℹ️</button>
                    </td>`;
                tbody.appendChild(fila);

                //OVERLAY
                const btnInfo = fila.querySelector(".info-btn");
                btnInfo.addEventListener("click", () => {
                    const overlay = document.createElement('div');
                    overlay.style.position = 'fixed';
                    overlay.style.top = 0;
                    overlay.style.left = 0;
                    overlay.style.width = '100%';
                    overlay.style.height = '100%';
                    overlay.style.background = 'rgba(0,0,0,0.5)';
                    overlay.style.display = 'flex';
                    overlay.style.justifyContent = 'center';
                    overlay.style.alignItems = 'center';
                    overlay.style.zIndex = 1000;

                    //RECUADRO
                    const modal = document.createElement('div');
                    modal.style.background = '#fff';
                    modal.style.padding = '20px';
                    modal.style.borderRadius = '8px';
                    modal.style.maxWidth = '400px';
                    modal.style.maxHeight = '300px';
                    modal.style.overflowY = 'auto';
                    modal.style.boxShadow = '0 4px 10px rgba(0,0,0,0.3)';
                    modal.style.position = 'relative';

                    //Contenido
                    const descripcion = document.createElement('p');
                    descripcion.innerText = libro.descripcion ?? 'Sin descripción';
                    descripcion.style.whiteSpace = 'pre-wrap'; // Mantener saltos de línea
                    modal.appendChild(descripcion);

                    //Boton de cerrar
                    const cerrarBtn = document.createElement('button');
                    cerrarBtn.innerText = 'Cerrar';
                    cerrarBtn.style.marginTop = '15px';
                    cerrarBtn.style.padding = '5px 10px';
                    cerrarBtn.style.cursor = 'pointer';
                    cerrarBtn.addEventListener('click', () => {
                        overlay.remove();
                    });

                    modal.appendChild(cerrarBtn);
                    overlay.appendChild(modal);
                    document.body.appendChild(overlay);
                });
            });

        } else {
            tbody.innerHTML = '<tr><td colspan="9">Sin resultados</td></tr>';
        }
    }

    // Editar libro
    async function editarLibro(id) {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('titulo', document.getElementById('titulo-' + id).value);
        formData.append('autor', document.getElementById('autor-' + id).value);
        formData.append('isbn', document.getElementById('isbn-' + id).value);
        formData.append('editorial', document.getElementById('editorial-' + id).value);
        formData.append('anio', document.getElementById('anio-' + id).value);
        formData.append('categoria', document.getElementById('categoria-' + id).value);
        formData.append('descripcion', document.getElementById('descripcion-' + id)?.value || '');

        const res = await fetch('editar.php', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.message);
        buscarLibros();
    }

    // Eliminar libro
    async function eliminarLibro(id) {
        if (!confirm('¿Desea eliminar este libro?')){
            return;
        }

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