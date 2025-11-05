<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="login">
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <form id="loginForm">
            <input type="text" id="usuario" name="usuario" placeholder="Usuario" required>
            <input type="password" id="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Autentificar</button>
        </form>
        <div class="mensaje" id="mensaje"></div>
    </div>

    <script>
        document.querySelector("#loginForm").addEventListener("submit", async (e) => {
            e.preventDefault();
            const usuario = document.querySelector("#usuario").value.trim();
            const password = document.querySelector("#password").value.trim();
            const mensaje = document.querySelector("#mensaje");
            mensaje.textContent = "Verificando...";
            mensaje.className = "mensaje";
            
            try {
                const respuesta = await fetch("logica_login.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({usuario, password})
                });
                const data = await respuesta.json();

                if (data.success) {
                    mensaje.textContent = "Inicio de sesión correcto."
                    mensaje.classList.add("ok");
                    setTimeout(() => {
                        window.location.href = "index.php";
                    }, 1500);
                } else {
                    mensaje.textContent = "Error: " + data.error;
                    mensaje.classList.add("error");
                }
            } catch (error) {
                mensaje.textContent = "Error al conectar con el servidor.";
                mensaje.classList.add("error");
                console.error(error);
            }
        });
    </script>
</body>
</html>