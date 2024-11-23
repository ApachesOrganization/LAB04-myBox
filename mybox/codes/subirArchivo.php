<?php
require_once 'funciones.php';

// Subir un archivo
if (isset($_FILES['archivo'])) {
    $nombreArchivo = $_FILES['archivo']['name'];
    $rutaArchivo = $ruta . '/' . basename($nombreArchivo);

    if (move_uploaded_file($_FILES['archivo']['tmp_name'], $rutaArchivo)) {
        echo "<script>alert('Archivo subido exitosamente: {$nombreArchivo}');</script>";
    } else {
        echo "<script>alert('Error al subir el archivo: {$nombreArchivo}.');</script>";
    }
}
?>
