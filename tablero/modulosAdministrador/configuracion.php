<?php

include('../control_acceso.php');
include('../session_check.php');
verificarAcceso(['Administrador']);

// Cargar el archivo JSON
$filePath = "../variablesConfiguracion.json";
$data = file_exists($filePath) ? json_decode(file_get_contents($filePath), true) : [];
$carreras = $data['carreras'] ?? [];
$nombreLaboratorio = $data['NombreLaboratorio'] ?? '';
$ubicaciones = $data['ubicaciones'] ?? [];

// Procesar la eliminación de una carrera
if (isset($_POST['eliminar']) && !empty($_POST['eliminarCarrera'])) {
    $eliminarCarrera = trim($_POST['eliminarCarrera']);
    if (($key = array_search($eliminarCarrera, $carreras)) !== false) {
        unset($carreras[$key]);
        $data['carreras'] = array_values($carreras); // Reindexar el array
        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
        echo "<script>alert('Carrera eliminada con éxito.'); window.location.href='configuracion.php';</script>";
        exit;
    }
}

// Procesar el formulario de nueva carrera
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nuevaCarrera'])) {
    $nuevaCarrera = trim($_POST['nuevaCarrera']);
    if (!empty($nuevaCarrera) && !in_array($nuevaCarrera, $carreras)) {
        $carreras[] = $nuevaCarrera;
        $data['carreras'] = $carreras;
        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
        echo "<script>alert('Carrera añadida con éxito.'); window.location.href='configuracion.php';</script>";
        exit;
    }
}

// Procesar el nombre del laboratorio
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombreLaboratorio'])) {
    $nombreLaboratorio = trim($_POST['nombreLaboratorio']);
    if (!empty($nombreLaboratorio)) {
        $data['NombreLaboratorio'] = $nombreLaboratorio;
        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
        echo "<script>alert('Nombre del laboratorio actualizado con éxito.'); window.location.href='configuracion.php';</script>";
        exit;
    }
}

// Funciones para procesar las ubicaciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['nuevaUbicacion'])) {
        $nuevaUbicacion = trim($_POST['nuevaUbicacion']);
        if (!empty($nuevaUbicacion) && !in_array($nuevaUbicacion, $ubicaciones)) {
            $ubicaciones[] = $nuevaUbicacion;
            $data['ubicaciones'] = $ubicaciones;
            file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
            echo "<script>alert('Ubicación añadida con éxito.'); window.location.href='configuracion.php';</script>";
            exit;
        }
    } elseif (isset($_POST['eliminarUbicacion'])) {
        $eliminarUbicacion = trim($_POST['eliminarUbicacion']);
        if (($key = array_search($eliminarUbicacion, $ubicaciones)) !== false) {
            unset($ubicaciones[$key]);
            $data['ubicaciones'] = array_values($ubicaciones);
            file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
            echo "<script>alert('Ubicación eliminada con éxito.'); window.location.href='configuracion.php';</script>";
            exit;
        }
    }
}

?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Configuración de variables - Módulo PISC</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Configuración de variables</h2>
        <br>
        <div class="row">
            <div class="col-md-6">
                <form action="configuracion.php" method="post">
                    <h4>Selección de procedencia de deudores</h4>
                    <div class="alert alert-primary" role="alert">
                        Aparecerán al seleccionar una procedencia de deudor en la sección
                        "Nuevo Préstamo" para usuarios Prestamistas.
                    </div>
                    <div class="form-group">
                        <label for="nuevaCarrera">Añadir nueva procedencia:</label>
                        <input type="text" class="form-control" id="nuevaCarrera" name="nuevaCarrera" required>
                        <button type="submit" class="btn btn-primary mt-2">Almacenar</button>
                    </div>
                </form>

                <h5>Variables existentes</h5>
                <ul class="list-group">
                    <?php foreach ($carreras as $carrera) : ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?php echo htmlspecialchars($carrera); ?>
                            <form action="configuracion.php" method="post" onsubmit="return confirm('¿Está seguro de que desea eliminar esta carrera?');">
                                <input type="hidden" name="eliminarCarrera" value="<?php echo htmlspecialchars($carrera); ?>">
                                <input type="submit" name="eliminar" value="Borrar" class="btn btn-danger btn-sm">
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-md-6">
                <h4>Ubicaciones de los bienes</h4>
                <div class="alert alert-info" role="alert">
                    Se mostrarán al momento de añadir o modificar los bienes.
                    No afecta su eliminación o incorporación a los registros existentes.
                </div>
                <form action="configuracion.php" method="post">
                    <div class="form-group">
                        <label for="nuevaUbicacion">Añadir nueva ubicación:</label>
                        <input type="text" class="form-control" id="nuevaUbicacion" name="nuevaUbicacion" required>
                        <button type="submit" class="btn btn-primary mt-2">Almacenar</button>
                    </div>
                </form>

                <h5>Ubicaciones existentes</h5>
                <ul class="list-group">
                    <?php if (!empty($ubicaciones)) : ?>
                        <?php foreach ($ubicaciones as $ubicacion) : ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo htmlspecialchars($ubicacion); ?>
                                <form action="configuracion.php" method="post" onsubmit="return confirm('¿Está seguro de que desea eliminar esta ubicación?');">
                                    <input type="hidden" name="eliminarUbicacion" value="<?php echo htmlspecialchars($ubicacion); ?>">
                                    <input type="submit" name="eliminar" value="Borrar" class="btn btn-danger btn-sm">
                                </form>
                            </li>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <li class="list-group-item">No hay ubicaciones registradas</li>
                    <?php endif; ?>
                    <br>
                </ul>

                <hr> <!-- Separador para la nueva sección -->

                <h4>Nombre del laboratorio</h4>
                <div class="alert alert-danger" role="alert">
                    Modifique esta variable bajo autorización previa.
                    Todos los vales generados por consultar recuperarán el dato.
                </div>
                <form action="configuracion.php" method="post">
                    <div class="form-group">
                        <label for="nombreLaboratorio">Especifica el nombre del laboratorio que se registrarán en los vales de préstamo:</label>
                        <input type="text" class="form-control" id="nombreLaboratorio" name="nombreLaboratorio" value="<?php echo htmlspecialchars($nombreLaboratorio); ?>" required>
                        <button type="submit" class="btn btn-primary mt-2">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <br>
    <br>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>