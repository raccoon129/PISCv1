<?php

include('../control_acceso.php');
include('../session_check.php');
verificarAcceso(['Administrador']);

// Cargar el archivo JSON
$filePath = "../variablesConfiguracion.json";
$data = file_exists($filePath) ? json_decode(file_get_contents($filePath), true) : [];
$carreras = $data['carreras'] ?? [];
$nombreLaboratorio = $data['NombreLaboratorio'] ?? '';

// Procesar la eliminación de una carrera
if (isset($_POST['eliminar']) && !empty($_POST['eliminarCarrera'])) {
    $eliminarCarrera = $_POST['eliminarCarrera'];
    if (($key = array_search($eliminarCarrera, $carreras)) !== false) {
        unset($carreras[$key]);
        $data['carreras'] = array_values($carreras); // Reindexar el array
        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
        echo "<script>alert('Carrera eliminada con éxito.'); window.location.href='configuracion.php';</script>";
        exit;
    }
}

// Procesar el formulario de nueva carrera
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nuevaCarrera']) && !empty($_POST['nuevaCarrera'])) {
    $nuevaCarrera = $_POST['nuevaCarrera'];
    if (!in_array($nuevaCarrera, $carreras)) {
        $carreras[] = $nuevaCarrera;
        $data['carreras'] = $carreras;
        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
        echo "<script>alert('Carrera añadida con éxito.'); window.location.href='configuracion.php';</script>";
        exit;
    }
}

// Procesar el nombre del laboratorio
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombreLaboratorio'])) {
    $nombreLaboratorio = $_POST['nombreLaboratorio'];
    $data['NombreLaboratorio'] = $nombreLaboratorio;
    file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
    echo "<script>alert('Nombre del laboratorio actualizado con éxito.'); window.location.href='configuracion.php';</script>";
    exit;
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
                        Estas aparecerán al seleccionar una procedencia de deudor en la sección
                        "Nuevo Préstamo" para usuarios Prestamistas.
                    </div>
                    <div class="form-group">
                        <label for="nuevaCarrera">Añadir nueva procedencia:</label>
                        <input type="text" class="form-control" id="nuevaCarrera" name="nuevaCarrera" required>
                        <button type="submit" class="btn btn-primary mt-2">Almacenar</button>
                    </div>
                </form>
                <hr>
                <h4>Variables existentes</h4>
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
                <h4>Nombre del laboratorio</h4>
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