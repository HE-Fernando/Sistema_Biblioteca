<?php
    session_start();
    include("../config/conexion_db.php");

    header("Content-Type: application/json; charset=UTF-8");
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $data = json_decode(file_get_contents("php://input"), true);

        $usuario = $data["usuario"] ?? null;
        $password = $data["password"] ?? null;

        if (!$usuario || !$password){
            echo json_encode([
                "success" => false,
                "error" => "Datos incompletos/incorrectos"
            ]);
            exit();
        }

        $sql = "SELECT * FROM usuario_sistema WHERE usuario = :usuario";
        $stm = $pdo->prepare($sql);
        $stm->execute([":usuario" => $usuario]);
        $row = $stm->fetch(PDO::FETCH_ASSOC);

        if ($row){
            if (password_verify($password, $row["password"])){
                $_SESSION["usuario"] = $row["usuario"];
                $_SESSION["rol"] = $row["rol"];
                $_SESSION["nombre"] = $row["nombre"];

                echo json_encode([
                    "success" => true,
                    "usuario" => $row["usuario"],
                    "rol" => $row["rol"],
                    "nombre" => $row["nombre"]
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "error" => "Contraseña incorrecta"
                ]);
            }
        } else {
            echo json_encode([
                "success" => false,
                "error" => "Usuario no encontrado"
            ]);
        }
    } else {
        echo json_encode([
            "success" => false,
            "error" => "Método no permitido"
        ]);
    }
?>