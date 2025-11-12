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
        <div class="navbar-left">
            <img src="../assets/logo.png" alt="Logo del sistema" class="logo-sistema">
            <h1>Bienvenido
            [<?php echo htmlspecialchars($_SESSION["nombre"]);?>]
            [<?php echo htmlspecialchars($_SESSION["rol"])?>]
        </h1>
        </div>
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
        <a href="index.php" class="<?= $pagina_actual == 'index.php' ? 'activo' : '' ?>">👥 Clientes</a>
        <a href="../prestamos/index.php">🧾 Préstamos</a>
        <!--<a href="#">📊 Reportes</a> -->
    </nav>


    <!-- CONTENEDOR PRINCIPAL -->
    <main class="main-content">
        <h2>Gestión de Clientes</h2>
        <!-- Formulario para crear -->
        <section>
            <h3>Agregar cliente</h3>
            <form id="form-crear" class="form-cliente">
                <div class="form-row">
                    <label>Nombre completo:</label>
                    <input type="text" name="nombre" placeholder="Nombre completo" required>
                </div>
                <div class="form-row">
                    <label>Email:</label>
                    <input type="text" name="email" placeholder="Email">
                </div>
                <div class="form-row">
                    <label>Teléfono:</label>
                    <input type="text" name="telefono" placeholder="Teléfono">
                </div>
                <div class="form-row">
                    <label>Dirección:</label>
                    <input type="text" name="direccion" placeholder="Dirección"> 
                </div>
                <div class="form-row">
                    <label>DNI:</label>
                    <input type="text" name="dni" placeholder="DNI"> 
                </div>
                <button type="submit" class="btn-guardar">Guardar</button>
            </form>
        </section>

        <!-- Buscador -->
        <section>
            <h3>Buscar clientes</h3>
            <input type="text" id="buscar" placeholder="Buscar...">
            <button onclick="buscarClientes()">Buscar</button>
        </section>

        <!-- Tabla -->
        <section>
            <table id="tabla-clientes">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>DNI</th>
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

    // Crear cliente
    document.getElementById('form-crear').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const res = await fetch('crear.php', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.message);
        buscarClientes();
        e.target.reset();
    });

    // Buscar cliente
    async function buscarClientes() {
        const q = document.getElementById('buscar').value;
        const res = await fetch('buscar.php?buscar=' + encodeURIComponent(q));
        const data = await res.json();

        const tbody = document.querySelector('#tabla-clientes tbody');
        tbody.innerHTML = '';

        if (data.success && data.data.length > 0) {
            data.data.forEach(cliente => {
                const fila = document.createElement("tr");
                const colorEstado = cliente.estado === "activo" ? "green" : "red";
                fila.innerHTML = `
                    <td>${cliente.id}</td>
                    <td><input type="text" value="${cliente.nombre_completo}" id="nombre-${cliente.id}"></td>
                    <td><input type="text" value="${cliente.email}" id="email-${cliente.id}"></td>
                    <td><input type="text" value="${cliente.telefono}" id="telefono-${cliente.id}"></td>
                    <td><input type="text" value="${cliente.direccion}" id="direccion-${cliente.id}"></td>
                    <td><input type="text" value="${cliente.dni}" id="dni-${cliente.id}"></td>
                    <td style="color:${colorEstado}; font-weight:bold;">${cliente.estado}</td>
                    <td>
                        <button onclick="editarCliente(${cliente.id})">💾</button>
                        <button onclick="eliminarCliente(${cliente.id})">🗑️</button>
                    </td>`;
                tbody.appendChild(fila);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="9">Sin resultados</td></tr>';
        }
    }

    // Editar cliente
    async function editarCliente(id) {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('nombre', document.getElementById('nombre-' + id).value);
        formData.append('email', document.getElementById('email-' + id).value);
        formData.append('telefono', document.getElementById('telefono-' + id).value);
        formData.append('direccion', document.getElementById('direccion-' + id).value);
        formData.append('dni', document.getElementById('dni-' + id).value);
        const res = await fetch('editar.php', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.message);
        buscarClientes();
    }

    // Eliminar cliente
    async function eliminarCliente(id) {
        if (!confirm('¿Desea eliminar este cliente?')){
            return;
        }

        const formData = new FormData();
        formData.append('id', id);

        const res = await fetch('eliminar.php', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.message);
        buscarClientes();
    }

    // Buscar automáticamente al cargar
    buscarClientes();
</script>
</body>
</html>