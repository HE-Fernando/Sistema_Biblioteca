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
    <!-- BARRA DE SESION -->
    <header class="navbar">
        <h1>Bienvenido [<?php echo htmlspecialchars($_SESSION["nombre"]);?>] [<?php echo htmlspecialchars($_SESSION["rol"])?>]</h1>
        <button id="logoutBtn" class="btn-logout">Cerrar sesión</button>
        <p id="logoutMessage" class="logout-msg"></p>
    </header>

    <!-- ACA VA EL CONTENIDO PRINCIPAL -->
    <main class="main-content">
        <p>Contenido principal</p>
    </main>
    
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
