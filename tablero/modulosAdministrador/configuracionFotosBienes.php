<?php
include '../../db.php';
include('../control_acceso.php');
verificarAcceso(['Administrador']);

$directorioImagenes = '../imgBienes/';
$mensaje = '';

$jsonFilePath = '../bibliotecaFotosBienes.json';
$imagenesJSON = file_exists($jsonFilePath) ? json_decode(file_get_contents($jsonFilePath), true) : [];

// Leer los identificadores de bienes del inventario
$sql = "SELECT BienID FROM inventario";
$resultado = $conexion->query($sql);
$bienesInventario = [];
while ($fila = $resultado->fetch_assoc()) {
    $bienesInventario[] = $fila['BienID'];
}

// Escanear el directorio de imágenes
$archivos = scandir($directorioImagenes);
$imagenesDisponibles = array_diff($archivos, ['.', '..']);

// Filtrar las imágenes que no están en el JSON pero están en la base de datos y son de formatos permitidos
$imagenesNoRegistradas = [];
foreach ($imagenesDisponibles as $imagen) {
    $extension = strtolower(pathinfo($imagen, PATHINFO_EXTENSION));
    $bienID = pathinfo($imagen, PATHINFO_FILENAME);
    if (in_array($extension, ['jpg', 'jpeg', 'png']) && !isset($imagenesJSON[$bienID]) && in_array($bienID, $bienesInventario)) {
        $imagenesNoRegistradas[] = $imagen;
    }
}

$datosCorrectos = !empty($imagenesNoRegistradas);

// Procesar la solicitud de registro en el JSON
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar'])) {
    foreach ($imagenesNoRegistradas as $imagen) {
        $bienID = pathinfo($imagen, PATHINFO_FILENAME);
        $imagenesJSON[$bienID] = $directorioImagenes . $imagen;
    }
    file_put_contents($jsonFilePath, json_encode($imagenesJSON, JSON_PRETTY_PRINT));
    $mensaje = "Las imágenes han sido registradas exitosamente.";
    echo '<script>setTimeout(function(){ window.location.reload(1); }, 2000);</script>'; // JavaScript para recargar
} elseif (!$datosCorrectos) {
    $mensaje = "No hay nuevas imágenes elegibles para registrar.";
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Revisión de Imágenes de Bienes - Submódulo PISC</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-3">
        <h2>Revisión de Imágenes de Bienes</h2>
        <div class="alert alert-info" role="alert">
            En el caso que haya cargado imágenes de manera manual en el directorio 'imgBienes' (<strong>no recomendado</strong> debido a que no se garantiza
            su correcta visualización en condiciones de red inestables, o en la manipulación del backend de este software) puede registrar su correspondencia a un bien para que pueda ser visualizado.
            <li>Considere que el nombre del archivo debe ser igual al <strong>No de Inventario</strong> de cada bien previamente registrado.</li>
            <li>El formato debe ser jpg, jpeg o png. Si es distinto, el archivo es posible que no se muestre listado o se pueda previsualizar.</li>
            <li>Es recomendable cargar cada imagen en el cuadro de diálogo correspondiente al insertar un bien individualmente o al editarlo.</li>
        </div>
        <?php if (!empty($mensaje)) : ?>
            <div class="alert <?php echo $datosCorrectos ? 'alert-success' : 'alert-warning'; ?>" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
        <form action="" method="post">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No. de Inventario</th>
                        <th>Imagen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($imagenesNoRegistradas as $imagen) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars(pathinfo($imagen, PATHINFO_FILENAME)); ?></td>
                            <td><img src="<?php echo htmlspecialchars($directorioImagenes . $imagen); ?>" style="width: 100px; height: auto;"></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if ($imagenesNoRegistradas) : ?>
                <button type="submit" class="btn btn-primary" name="registrar">Registrar nuevas variables de imagenes</button>
            <?php endif; ?>
        </form>
    </div>
</body>

</html>