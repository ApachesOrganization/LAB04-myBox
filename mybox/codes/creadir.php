<?php
//Inicio la sesión
    session_start();

    // User validate
    if ($_SESSION["autenticado"] != "SI") {
        header("Location: ../index.php");
        exit(); //fin del script
    }

    // Declares home path to current user
    $ruta = getenv('HOME_PATH');
    $ruta = $ruta.'/'.$_SESSION["usuario"];

    // Try to create a new home path directory to new user
    if(!mkdir($ruta,0700)){
        echo 'ERROR:\\ NO se pudo crear directorio para almacenar datos.<br>';
        echo 'Favor pongase en contacto con el departamento de servicio al cliente.<br>';
        echo 'Ruta.....'.$ruta;
    }else{
        header("Location: ../carpetas.php");
    }
?>
