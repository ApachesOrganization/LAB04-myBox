<?php
session_start();

if ($_SESSION["autenticado"] != "SI") {
    header("Location: index.php");
    exit(); // Fin del script si no está autenticado
}

// Directorio actual
if (isset($_GET['path'])) {
    $ruta = realpath(getenv('HOME_PATH') . '/' . $_SESSION["usuario"] . '/' . $_GET['path']);
} else {
    $ruta = realpath(getenv('HOME_PATH') . '/' . $_SESSION["usuario"]);
}

// Verifica que la ruta sea válida y segura
if (!$ruta || strpos($ruta, realpath(getenv('HOME_PATH') . '/' . $_SESSION["usuario"])) !== 0) {
    echo "<script>alert('Ruta no válida');</script>";
    $ruta = realpath(getenv('HOME_PATH') . '/' . $_SESSION["usuario"]);
}

// Función para eliminar carpetas de manera recursiva
function eliminarCarpeta($carpeta) {
    if (is_dir($carpeta)) {
        $items = array_diff(scandir($carpeta), ['.', '..']);
        foreach ($items as $item) {
            $rutaItem = $carpeta . '/' . $item;
            is_dir($rutaItem) ? eliminarCarpeta($rutaItem) : unlink($rutaItem);
        }
        return rmdir($carpeta);
    }
    return false;
}

// Crear nueva carpeta
if (isset($_POST["nombreCarpeta"])) {
    $nombreCarpeta = trim($_POST["nombreCarpeta"]);
    $nuevaRuta = $ruta . '/' . $nombreCarpeta;

    if (!file_exists($nuevaRuta)) {
        if (mkdir($nuevaRuta, 0700)) {
            echo "<script>alert('Carpeta creada exitosamente.'); window.location.href='carpetas.php?path=" . urlencode($_GET['path'] ?? '') . "';</script>";
            exit();
        } else {
            echo "<script>alert('Error al crear la carpeta.'); window.location.href='carpetas.php?path=" . urlencode($_GET['path'] ?? '') . "';</script>";
            exit();
        }
    } else {
        echo "<script>alert('La carpeta ya existe.'); window.location.href='carpetas.php?path=" . urlencode($_GET['path'] ?? '') . "';</script>";
        exit();
    }
}

// Subir archivo
if (isset($_FILES['archivo'])) {
    // Verificar si hay errores en la subida
    if ($_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
        echo "<script>alert('Error al subir el archivo. Código de error: " . $_FILES['archivo']['error'] . "'); window.location.href='carpetas.php?path=" . urlencode($_GET['path'] ?? '') . "';</script>";
        exit();
    }

    // Verificar tamaño máximo del archivo subido (20MB)
    if ($_FILES['archivo']['size'] > 20 * 1024 * 1024) {
        echo "<script>alert('El archivo supera el tamaño máximo permitido de 20MB.'); window.location.href='carpetas.php?path=" . urlencode($_GET['path'] ?? '') . "';</script>";
        exit();
    }

    $nombreArchivo = $_FILES['archivo']['name'];
    $rutaArchivo = $ruta . '/' . basename($nombreArchivo);

    if (move_uploaded_file($_FILES['archivo']['tmp_name'], $rutaArchivo)) {
        echo "<script>alert('Archivo subido exitosamente.'); window.location.href='carpetas.php?path=" . urlencode($_GET['path'] ?? '') . "';</script>";
        exit();
    } else {
        echo "<script>alert('Error al subir el archivo.'); window.location.href='carpetas.php?path=" . urlencode($_GET['path'] ?? '') . "';</script>";
        exit();
    }
}

// Mover archivo
if (isset($_POST['archivo']) && isset($_POST['destino'])) {
    $archivo = $_POST['archivo'];
    $destino = $_POST['destino'];

    $rutaArchivo = $ruta . '/' . $archivo;
    $rutaDestino = $ruta . '/' . $destino . '/' . $archivo;

    if (file_exists($rutaArchivo) && is_dir($ruta . '/' . $destino)) {
        if (rename($rutaArchivo, $rutaDestino)) {
            echo "<script>alert('Archivo movido exitosamente.'); window.location.href='carpetas.php?path=" . urlencode($_GET['path'] ?? '') . "';</script>";
            exit();
        } else {
            echo "<script>alert('Error al mover el archivo.'); window.location.href='carpetas.php?path=" . urlencode($_GET['path'] ?? '') . "';</script>";
            exit();
        }
    } else {
        echo "<script>alert('El archivo o la carpeta de destino no existen.'); window.location.href='carpetas.php?path=" . urlencode($_GET['path'] ?? '') . "';</script>";
        exit();
    }
}

// Borrar archivo o carpeta
if (isset($_GET['borrar'])) {
    $elementoABorrar = realpath($ruta . '/' . $_GET['borrar']);

    // Verifica que la ruta sea válida y segura
    if ($elementoABorrar && strpos($elementoABorrar, $ruta) === 0) {
        if (is_file($elementoABorrar)) {
            if (unlink($elementoABorrar)) {
                echo "<script>alert('Archivo borrado exitosamente.'); window.location.href='carpetas.php?path=" . urlencode($_GET['path'] ?? '') . "';</script>";
                exit();
            } else {
                echo "<script>alert('Error al borrar el archivo.'); window.location.href='carpetas.php?path=" . urlencode($_GET['path'] ?? '') . "';</script>";
                exit();
            }
        } elseif (is_dir($elementoABorrar)) {
            if (eliminarCarpeta($elementoABorrar)) {
                echo "<script>alert('Carpeta borrada exitosamente.'); window.location.href='carpetas.php?path=" . urlencode($_GET['path'] ?? '') . "';</script>";
                exit();
            } else {
                echo "<script>alert('Error al borrar la carpeta.'); window.location.href='carpetas.php?path=" . urlencode($_GET['path'] ?? '') . "';</script>";
                exit();
            }
        } else {
            echo "<script>alert('El elemento no existe o no es válido.'); window.location.href='carpetas.php?path=" . urlencode($_GET['path'] ?? '') . "';</script>";
            exit();
        }
    } else {
        echo "<script>alert('El elemento no existe o no es válido.'); window.location.href='carpetas.php?path=" . urlencode($_GET['path'] ?? '') . "';</script>";
        exit();
    }
}

// Descargar archivo
if (isset($_GET['descargar'])) {
    $archivoDescargar = realpath($ruta . '/' . $_GET['descargar']);

    // Verifica que la ruta sea válida y segura
    if ($archivoDescargar && strpos($archivoDescargar, $ruta) === 0 && is_file($archivoDescargar)) {
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename="' . basename($archivoDescargar) . '"');
        header('Content-Type: application/octet-stream');
        header('Content-Length: ' . filesize($archivoDescargar));
        header('Pragma: public');
        header('Cache-Control: must-revalidate');
        readfile($archivoDescargar);
        exit();
    } else {
        echo "<script>alert('El archivo no existe.'); window.location.href='carpetas.php?path=" . urlencode($_GET['path'] ?? '') . "';</script>";
        exit();
    }
}

// Obtener carpetas disponibles
$carpetas = array_filter(glob($ruta . '/*'), 'is_dir');

// Función para determinar el ícono según tipo de archivo
function obtenerIcono($archivo) {
    $ext = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
    $iconos = [
        'docx' => 'word_icon.png',
        'pdf' => 'pdf_icon.png',
        'png' => 'png_icon.png',
        'jpg' => 'image_icon1.png',
        'jpeg' => 'image_icon1.png',
        'txt' => 'txt_icon.png',
        'folder' => 'folder_icon.png',
    ];
    return 'images/' . ($iconos[$ext] ?? 'default_icon.png');
}
?>
<!doctype html>
<html lang="en">
<head>
    <?php include_once('sections/head.inc'); ?>
    <title>Mi Cajón de Archivos</title>
</head>
<body class="container-fluid">
<header class="row">
    <div class="row">
        <?php include_once('sections/header.inc'); ?>
    </div>
</header>
<main class="row">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <strong>Mi Cajón de Archivos</strong>
        </div>
        <div class="panel-body">

            <!-- FORMULARIO PARA CREAR UNA NUEVA CARPETA -->
            <form method="post" action="">
                <fieldset>
                    <legend><strong>Crear Carpeta</strong></legend>
                    <label for="nombreCarpeta">Nombre de la carpeta:</label>
                    <input type="text" id="nombreCarpeta" name="nombreCarpeta" placeholder="Nombre de la carpeta" required>
                    <button type="submit">Crear</button>
                </fieldset>
            </form>
            <br><br>

            <!-- FORMULARIO PARA SUBIR ARCHIVOS -->
            <form method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="MAX_FILE_SIZE" value="20971520" /> <!-- 20MB en bytes -->
                <fieldset>
                    <legend><strong>Subir Archivo</strong></legend>
                    <label for="archivo">Seleccionar archivo:</label>
                    <input type="file" id="archivo" name="archivo" required>
                    <button type="submit">Subir</button>
                </fieldset>
            </form>
            <br><br>

            <!-- FORMULARIO PARA MOVER ARCHIVOS -->
            <form method="post" action="">
                <fieldset>
                    <legend><strong>Mover Archivo</strong></legend>
                    <label for="archivo">Archivo:</label>
                    <select id="archivo" name="archivo" required>
                        <?php
                        // Listar archivos
                        $archivos = array_filter(glob($ruta . '/*'), 'is_file');
                        foreach ($archivos as $archivo) {
                            echo '<option value="' . basename($archivo) . '">' . basename($archivo) . '</option>';
                        }
                        ?>
                    </select>

                    <label for="destino">Mover a carpeta:</label>
                    <select id="destino" name="destino" required>
                        <?php
                        // Listar carpetas
                        foreach ($carpetas as $carpeta) {
                            echo '<option value="' . basename($carpeta) . '">' . basename($carpeta) . '</option>';
                        }
                        ?>
                    </select>

                    <button type="submit">Mover</button>
                </fieldset>
            </form>
            <br><br>

            <!-- LISTADO DE ARCHIVOS Y CARPETAS -->
            <a href="carpetas.php">Volver al directorio raíz</a><br><br>
            <table class="table table-striped">
                <tr>
                    <th>Ícono</th>
                    <th>Nombre</th>
                    <th>Tamaño</th>
                    <th>Último acceso</th>
                    <th>Acción</th>
                </tr>
                <?php
                $directorio = opendir($ruta);
                while ($elem = readdir($directorio)) {
                    if ($elem === '.' || $elem === '..') continue;
                    $fullPath = $ruta . '/' . $elem;
                    $isDir = is_dir($fullPath);

                    // Construir ruta relativa para enlaces
                    $relativePath = isset($_GET['path']) ? $_GET['path'] . '/' . $elem : $elem;

                    echo '<tr>';
                    echo '<td><img src="' . ($isDir ? 'images/folder_icon.png' : obtenerIcono($elem)) . '" width="32"></td>';
                    echo '<td>';
                    if ($isDir) {
                        echo '<a href="carpetas.php?path=' . urlencode($relativePath) . '">' . $elem . '</a>';
                    } else {
                        echo '<a href="abrarchi.php?arch=' . urlencode($relativePath) . '">' . $elem . '</a>';
                    }
                    echo '</td>';
                    echo '<td>' . (!$isDir ? filesize($fullPath) . ' bytes' : '-') . '</td>';
                    echo '<td>' . date("d/m/y h:i:s", fileatime($fullPath)) . '</td>';
                    echo '<td>';
                    if (!$isDir) echo '<a href="carpetas.php?descargar=' . urlencode($relativePath) . '">Descargar</a> | ';
                    echo '<a href="carpetas.php?borrar=' . urlencode($relativePath) . '" onclick="return confirm(\'¿Seguro que desea borrar ' . $elem . '?\')">Borrar</a>';
                    echo '</td>';
                    echo '</tr>';
                }
                closedir($directorio);
                ?>
            </table>
        </div>
    </div>
</main>
<footer class="row">
    <?php include_once('sections/foot.inc'); ?>
</footer>
</body>
</html>
