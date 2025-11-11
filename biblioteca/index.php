<?php
session_start();
if (!isset($_SESSION["usuario"])){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Biblioteca</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="index">
    <!-- BARRA SUPERIOR -->
    <header class="navbar">
        <h1>Bienvenido
            [<?php echo htmlspecialchars($_SESSION["nombre"]);?>]
            [<?php echo htmlspecialchars($_SESSION["rol"])?>]
        </h1>
        <button id="logoutBtn" class="btn-logout">Cerrar sesión</button>
    </header>

    <!-- GENERAL -->
    <div class="container">
        <!-- BARRA LATERAL -->
        <nav class="sidebar">
            <?php
            $pagina_actual = basename($_SERVER['PHP_SELF']); // obtiene el nombre del archivo actual
            ?>
            <a href="index.php" class="<?= $pagina_actual == 'index.php' ? 'activo' : '' ?>">🏠 Inicio</a>
            <a href="../libros/index.php" class="<?= $pagina_actual == 'index.php' && basename(dirname($_SERVER['PHP_SELF'])) == 'libros' ? 'activo' : '' ?>">📖 Libros</a>
            <a href="#">👥 Clientes</a>
            <a href="#">🧾 Reservas</a>
            <a href="#">📊 Reportes</a>
        </nav>


        <!-- PRINCIPAL -->
        <main class="main-content">
            <h2>Panel principal</h2>
            <?php
                if($_SESSION["rol"] == "admin"){
                    echo "<p>Bienvenido al sistema de gestión de la biblioteca.</p>";
                } else {
                    echo "<p>Bienvenido a la biblitoeca.</p>";
                }
            ?>
            <p id="logoutMessage" class="logout-msg"></p>
        </main>         
    </div>

    <!-- PIE DE PÁGINA-->
    <footer>
        © <?php echo date("Y"); ?> - Sistema de Biblioteca | Benitez - Hirt
    </footer>
    
    <!-- SCRIPTS -->
     <script>
        document.getElementById("logoutBtn").addEventListener("click", async () => {
            const mensaje = document.getElementById("logoutMessage");


            if (!confirm("¿Seguro que querés cerrar sesión?")){
                return;
            }

            mensaje.textContent = "Cerrando sesión...";
            mensaje.className = "logout-msg";

            try{
                const respuesta = await fetch("logout.php", {
                    method: "POST",
                    headers: {"Content-Type": "application/json"}
                });
                const data = await respuesta.json();

                if (data.success){
                    mensaje.textContent = data.message;
                    mensaje.classList.add("ok");
                    setTimeout(() => {
                        window.location.href = "login.php";
                    }, 1500);
                } else {
                    mensaje.textContent = "Error al cerrar sesión.";
                    mensaje.classList.add("error");
                }
            } catch (error) {
                console.error("Error: ", error);
                mensaje.textContent = "No se pudo contectar al servidor.";
                mensaje.className = "error";
            }
        });
     </script>
</body>
</html>
