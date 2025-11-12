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
    <title>Gestión de Prestamos</title>
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
        <a href="../libros/index.php">📖 Libros</a>
        <a href="../clientes/index.php">👥 Clientes</a>
        <a href="index.php" class="<?= $pagina_actual == 'index.php' ? 'activo' : '' ?>">🧾 Prestamos</a>
        <!--<a href="#">📊 Reportes</a> -->
    </nav>


    <!-- CONTENEDOR PRINCIPAL -->
    <main class="main-content">
        <h2>Gestión de Préstamos</h2>
        <!-- Formulario para crear -->
        <section>
            <h3>Registrar Préstamo</h3>
            <form id="form-nuevo" class="form-prestamo">
                <div class="form-row">
                    <label for="usuario_id">Usuario:</label>
                    <select name="usuario_id" id="usuario_id" required>
                        <option value="">Seleccione un usuario</option>
                    </select>
                </div>
                <div class="form-row">
                    <label for="libro_id">Libro:</label>
                    <select name="libro_id" id="libro_id" required>
                        <option value="">Seleccione un libro</option>
                    </select>
                </div>
                <div class="form-row">
                    <label for="fecha_devolucion">Fecha de Devolución Pactada:</label>
                    <input type="date" name="fecha_devolucion" id="fecha_devolucion" required>
                </div>
                <div class="form-row">
                    <label for="observaciones">Observaciones:</label>
                    <textarea name="observaciones" id="observaciones" placeholder="Observaciones" rows="3"> </textarea>
                </div>

                <button type="submit" class="btn-guardar">Registrar Préstamo</button>
            </form>
        </section>

        <!-- Buscador -->
        <section>
            <h3>Buscar Préstamos</h3>
            <input type="text" id="buscar" placeholder="Buscar (nombre, dni, título)...">
            <button onclick="buscarPrestamos()">Buscar</button>
        </section>

        <!-- Tabla -->
        <section>
            <table id="tabla-prestamos">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Usuario</th>
                        <th>Fecha de Prestamo</th>
                        <th>Fecha de Devolución</th>
                        <th>Fecha de Devolución Real</th>
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

    // Nuevo préstamo
    document.getElementById('form-nuevo').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const res = await fetch('nuevo.php', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.message);
        buscarPrestamos();
        e.target.reset();
    });

    // Buscar préstamos
    async function buscarPrestamos() {
        //ACTUALIZAR ANTES DE LISTAR
        try {
            const actualizarRes = await fetch("actualizar.php", {method: "POST"});
            const actualizarData = await actualizarRes.json();
            alert(actualizarData.message);
        } catch (err){
            console.error("Error al intentar actualizar prestamos: ", err);
        }

        const q = document.getElementById('buscar').value;
        const res = await fetch('historial.php?buscar=' + encodeURIComponent(q));
        const data = await res.json();

        const tbody = document.querySelector('#tabla-prestamos tbody');
        tbody.innerHTML = '';

        if (data.success && data.data.length > 0) {
            data.data.forEach(prestamo => {
                const fila = document.createElement("tr");
                const colorEstado =
                    prestamo.estado === "prestado" ? "yellow" :
                    prestamo.estado === "devuelto" ? "green" :
                    prestamo.estado === "atrasado" ? "red" :
                    "gray";

                fila.innerHTML = `
                    <td>${prestamo.id}</td>
                    <td><input type="text" value="${prestamo.titulo}" id="titulo-${prestamo.id}"></td>
                    <td><input type="text" value="${prestamo.nombre_completo}" id="nombre_completo-${prestamo.id}"></td>
                    <td><input type="text" value="${prestamo.fecha_prestamo}" id="fecha_prestamo-${prestamo.id}"></td>
                    <td><input type="text" value="${prestamo.fecha_devolucion}" id="fecha_devolucion-${prestamo.id}"></td>
                    <td><input type="text" value="${prestamo.fecha_dev_real}" id="fecha_dev_real-${prestamo.id}"></td>
                    <td style="color:${colorEstado}; font-weight:bold;">${prestamo.estado}</td>
                    <td>
                        <button onclick="prestamoDevolver(${prestamo.id})">🔙</button>
                        <button class="observacion-btn">ℹ️</button>
                    </td>`;
                tbody.appendChild(fila);

                //OVERLAY
                const btnInfo = fila.querySelector(".observacion-btn");
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
                    const observacion = document.createElement('p');
                    observacion.innerText = prestamo.observaciones ?? 'Sin observación';
                    observacion.style.whiteSpace = 'pre-wrap'; // Mantener saltos de línea
                    modal.appendChild(observacion);

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

    // Devolver
    async function prestamoDevolver(id) {
        if (!confirm('¿Desea registrar la devolución?')){
            return;
        }

        const formData = new FormData();
        formData.append('id', id);

        const res = await fetch('devolver.php', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.message);
        buscarprestamos();
    }

    //insertar datos al select de creación de prestamo
    document.addEventListener("DOMContentLoaded", async () => {
        try {
            const res = await fetch("creacion_dinamica.php");
            const data = await res.json();

            if (data.success){
                const usuarioSelect = document.getElementById("usuario_id");
                const libroSelect = document.getElementById("libro_id");

                //limpiar selects
                usuarioSelect.innerHTML = '<option value="">Seleccione un usuario</option>';
                libroSelect.innerHTML = '<option value="">Seleccione un libro</option>';

                //agrega usuarios
                data.usuarios.forEach(usuario => {
                    const option = document.createElement("option");
                    option.value = usuario.id;
                    option.textContent = usuario.nombre_completo + " - DNI[" + usuario.dni + "]";
                    usuarioSelect.appendChild(option);
                });

                //agrega libros
                data.libros.forEach(libro => {
                    const option = document.createElement("option");
                    option.value = libro.id;
                    option.textContent = libro.titulo + " - ISBN[" + libro.isbn + "]";
                    libroSelect.appendChild(option);
                });
            } else {
                console.errpr("Error del servidor: ", data.message);
            }
        }catch (error) {
            console.error("Error al cargar los datos dinámicos: ", error);
        }
    });

    // Buscar automáticamente al cargar
    buscarPrestamos();
</script>
</body>
</html>