<?php
session_start();

// Verificar autenticación
if ($_SESSION["autenticado"] != "SI") {
    header("Location: index.php");
    exit(); // Finalizar el script si no está autenticado
}

// Obtener el archivo solicitado
if (!isset($_GET['arch'])) {
    echo "<script>alert('Archivo no especificado.'); window.location.href='carpetas.php';</script>";
    exit();
}

$archivo = $_GET['arch'];
$rutaArchivo = realpath(getenv('HOME_PATH') . '/' . $_SESSION["usuario"] . '/' . $archivo);

// Verificar que el archivo exista y esté dentro del directorio permitido
if (!$rutaArchivo || strpos($rutaArchivo, realpath(getenv('HOME_PATH') . '/' . $_SESSION["usuario"])) !== 0 || !file_exists($rutaArchivo)) {
    echo "<script>alert('Archivo no encontrado o acceso denegado.'); window.location.href='carpetas.php';</script>";
    exit();
}

// Verificar que el archivo no exceda los 20MB
if (filesize($rutaArchivo) > 20 * 1024 * 1024) {
    echo "<script>alert('El archivo es demasiado grande para ser procesado.'); window.location.href='carpetas.php';</script>";
    exit();
}

// Determinar el tipo de archivo
$mime = mime_content_type($rutaArchivo);
$extension = strtolower(pathinfo($rutaArchivo, PATHINFO_EXTENSION));

// Extensiones permitidas para mostrar en pantalla
$extensionesMostrar = ['pdf', 'jpg', 'png'];

// Si la extensión está en la lista para mostrar, enviar al navegador
if (in_array($extension, $extensionesMostrar)) {
    header("Content-Type: $mime");
    header("Content-Disposition: inline; filename=" . basename($rutaArchivo));
    readfile($rutaArchivo);
    exit();
}

// Si la extensión no está permitida, forzar descarga
header("Content-Disposition: attachment; filename=" . basename($rutaArchivo));
header("Content-Type: $mime");
header("Content-Length: " . filesize($rutaArchivo));
readfile($rutaArchivo);
exit();
?>
