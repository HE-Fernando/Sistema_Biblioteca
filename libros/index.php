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
        <a href="#">👥 Clientes</a>
        <a href="#">🧾 Reservas</a>
        <a href="#">📊 Reportes</a>
    </nav>


    <!-- CONTENEDOR PRINCIPAL -->
    <main class="main-content">
        <h2>Gestión de Libros</h2>
        <!-- Formulario para crear -->
        <section>
            <h3>Agregar libro</h3>
            <form id="form-crear">
                <input type="text" name="titulo" placeholder="Título" required>
                <input type="text" name="autor" placeholder="Autor" required>
                <input type="text" name="isbn" placeholder="ISBN" required>
                <button type="submit">Guardar</button>
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
                    <tr><th>Título</th><th>Autor</th><th>ISBN</th><th>Acciones</th></tr>
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

