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
        <h1>Bienvenido <?php echo htmlspecialchars($_SESSION["usuario"]); ?></h1>
        <button id="logoutBtn" class="btn-logout">Cerrar sesión</button>
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

        })
     </script>
</body>
</html>
