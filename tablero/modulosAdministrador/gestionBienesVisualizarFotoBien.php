<?php
include '../../db.php';
include('../control_acceso.php');
verificarAcceso(['Administrador']);

$bienID = isset($_GET['bienID']) ? $_GET['bienID'] : '';

$sql = "SELECT BienID, Descripcion FROM inventario WHERE BienID = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $bienID);
$stmt->execute();
$result = $stmt->get_result();
$bien = $result->fetch_assoc();

// Verificar si el archivo JSON existe y leerlo, de lo contrario asignar un arreglo vacío
$jsonFilePath = '../bibliotecaFotosBienes.json';
if (file_exists($jsonFilePath)) {
    $fotosBienesJson = file_get_contents($jsonFilePath);
    $fotosBienes = json_decode($fotosBienesJson, true);
} else {
    $fotosBienes = [];
}

// Definir la ruta de la imagen
if (!empty($fotosBienes) && array_key_exists($bien['BienID'], $fotosBienes)) {
    $imagenRuta = $fotosBienes[$bien['BienID']];
} else {
    $imagenRuta = ''; // No hay imagen disponible

    $mensajeError = 'No hay imagen disponible para este bien.';
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Visualizar Foto del Bien - Submódulo PISC</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Incluir Lightbox CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-3">
        <h2>Fotografía del bien</h2>
        <p><strong>No. de Inventario:</strong> <?php echo htmlspecialchars($bien['BienID']); ?></p>
        <p><strong>Descripción:</strong> <?php echo htmlspecialchars($bien['Descripcion']); ?></p>

        <?php if ($imagenRuta) : ?>
            <a href="<?php echo $imagenRuta; ?>" data-lightbox="imagen-bien" data-title="Bien: <?php echo htmlspecialchars($bien['Descripcion']); ?>">
                <img src="<?php echo $imagenRuta; ?>" alt="Foto del Bien" class="img-fluid" onerror="this.src='../imgBienes/notFoundPic.jpg';">
            </a>
        <?php else : ?>
            <p class="alert alert-warning"><?php echo $mensajeError; ?></p>
        <?php endif; ?>
        <br>
        <br>
        <button id="btnImprimir" class="btn btn-primary">Imprimir previsualización</button>
        <br>
        <br>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script>
        document.getElementById('btnImprimir').addEventListener('click', function() {
            window.print();
        });
    </script>
</body>

</body>

</html>