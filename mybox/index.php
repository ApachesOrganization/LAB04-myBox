<?php
    require_once('codes/conexion.inc');
    $Accion_Formulario = $_SERVER['PHP_SELF'];
    if((isset($_POST['txtUsua'])) && (isset($_POST['txtContra']))) {

        $auxSql = sprintf("select nombre, usuario from usuarios Where usuario = '%s' and contra = md5('%s')", $_POST['txtUsua'],$_POST['txtContra']);
        $regis = mysqli_query($conex,$auxSql);

        // release inputs objects
        unset($_POST['txtUsua']);
        unset($_POST['txtContra']);

        if(mysqli_num_rows($regis) > 0){
            $tupla = mysqli_fetch_assoc($regis);

            // create session variable
            session_start();
            $_SESSION["autenticado"]= "SI";
            $_SESSION["nombre"]=$tupla['nombre'];
            $_SESSION["usuario"]=$tupla['usuario'];

            header("location: carpetas.php");
        }else {
            header("location: errors/400.php");
            exit();
        }
    }
?>
<!doctype html>
<html lang="en">
<head>
    <?php
        include_once('sections/head.inc');
    ?>
    <title>Ingreso al Sitio</title>
</head>
<body class="container-fluid">
    <header class="row">
        <div class="row">
            <?php include_once('sections/header.inc'); ?>
        </div>
    </header>
    <main class="row">
        <div class="panel panel-primary logueo">
            <div class="panel-heading">
                <strong>Autenticación de Usuarios</strong>
            </div>
            <div class="panel-body">
                <form action="<?php echo $Accion_Formulario; ?>" method="post">
                    <fieldset>
                        <label>Usuario:</label><input type="text" name="txtUsua" size="22" maxlength="15" required /><br>
                        <label>Contraseña:</label><input type="password" name="txtContra" size="22" maxlength="15" required />
                    </fieldset>
                    <input type="submit" value="Aceptar" />
                </form>
            </div>
            <br>
            <a href="registrar.php">Registrarse Aquí</a>
        </div>

    </main>

    <footer class="row">
        <?php include_once('sections/foot.inc'); ?>
    </footer>
</body>
</html>
