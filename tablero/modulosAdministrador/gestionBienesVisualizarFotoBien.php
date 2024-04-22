<?php
include '../../db.php';
include('../control_acceso.php');
require_once('../../vendor/autoload.php');
verificarAcceso();

$isImageAvailable = false;  // Variable para controlar la disponibilidad de la imagen


$bienID = isset($_GET['bienID']) ? $_GET['bienID'] : '';

$sql = "SELECT BienID, Descripcion, Ubicacion FROM inventario WHERE BienID = ?";
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
//Validacion extra para habilitar o no el boton
if (file_exists($jsonFilePath)) {
    $fotosBienesJson = file_get_contents($jsonFilePath);
    $fotosBienes = json_decode($fotosBienesJson, true);
    if (array_key_exists($bien['BienID'], $fotosBienes) && file_exists($fotosBienes[$bien['BienID']])) {
        $imagenRuta = $fotosBienes[$bien['BienID']];
        $isImageAvailable = true;  // La imagen existe
    }
}

// Definir la ruta de la imagen
if (!empty($fotosBienes) && array_key_exists($bien['BienID'], $fotosBienes)) {
    $imagenRuta = $fotosBienes[$bien['BienID']];
} else {
    $imagenRuta = ''; // No hay imagen disponible

    $mensajeError = 'No hay imagen disponible para este bien.';
}

if (isset($_POST['action']) && $_POST['action'] === 'print') {
    // Crear instancia de FPDF
    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);

    // Logo
    $pdf->Image('../../img/transparenteMediano.png', 10, 10, 33);
    $pdf->Ln(20);

    // Título
    $pdf->Cell(190, 10, 'Fotografia del bien: ' . $bien['BienID'], 0, 1, 'C');

    // Detalles del bien
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(190, 10, 'Descripcion: ' . $bien['Descripcion'], 0, 1);
    $pdf->Cell(190, 10, 'Ubicacion: ' . $bien['Ubicacion'], 0, 1);

    // Imagen del bien
    if ($imagenRuta) {
        $pdf->Image($imagenRuta, 10, 60, 120);
    }

    // Enviar el PDF al navegador
    $pdf->Output('I', 'Fotografia_del_bien_' . $bien['BienID'] . '.pdf');
    exit;
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
        <form method="post">
            <input type="hidden" name="action" value="print">
            <button type="submit" id="btnImprimir" class="btn btn-primary" <?php if (!$isImageAvailable) echo 'disabled'; ?>>Imprimir previsualización</button>
        </form>
        <br>
        <?php if ($imagenRuta) : ?>
            <a href="<?php echo $imagenRuta; ?>" data-lightbox="imagen-bien" data-title="Bien: <?php echo htmlspecialchars($bien['Descripcion']); ?>">
                <img src="<?php echo $imagenRuta; ?>" alt="Foto del Bien" class="img-fluid" onerror="this.src='../imgBienes/notFoundPic.jpg';">
            </a>
        <?php else : ?>
            <p class="alert alert-warning"><?php echo $mensajeError; ?></p>
        <?php endif; ?>
        <br>
        <br>
        <br>
        <br>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

</body>

</body>

</html>