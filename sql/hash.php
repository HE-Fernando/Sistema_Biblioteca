<?php
    $clave_plana = "consulta";
    $hash = password_hash($clave_plana, PASSWORD_DEFAULT);
    echo $hash;


?>