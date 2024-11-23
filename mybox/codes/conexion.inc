<?php
    // Parámetros de conexión
    $servidor  = "localhost";
    $basedatos = "mybox";
    $usuario   = getenv('DB_USER');
    $contra    = getenv('DB_PASS');

    // Crear conexión con el servidor de base de datos
    $conex = mysqli_connect($servidor, $usuario, $contra, $basedatos);
    
    if (!$conex) {
        echo "<h3><font color='red'>Error: No se puede conectar al servidor de MySQL.</font></h3><hr>";
        echo "<strong>Número........:</strong> " . mysqli_connect_errno() . "<br>";
        echo "<strong>Descripción...:</strong> " . mysqli_connect_error() . "<br>";
        exit;
    }

    // Configurar el conjunto de caracteres de la conexión
    if (!mysqli_set_charset($conex, "utf8")) {
        echo "<h3><font color='red'>Error al configurar el conjunto de caracteres de la conexión.</font></h3><hr>";
        exit;
    }
?>
