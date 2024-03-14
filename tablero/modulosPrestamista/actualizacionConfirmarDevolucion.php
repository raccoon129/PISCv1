<?php
include('../../db.php');
include('../control_acceso.php');
verificarAcceso(['Prestamista']);
$script = ""; // variable para guardar el script de SweetAlert

//echo "<pre>";
//var_dump($_POST);
//echo "</pre>";


if (isset($_POST['numeroVale'], $_POST['usuarioConfirmaDevolucion']) && is_numeric($_POST['numeroVale'])) {
    $numeroVale = $_POST['numeroVale'];
    $personaRecibe = $_POST['usuarioConfirmaDevolucion'];
    $fechaActual = date('Y-m-d H:i:s'); // Fecha y hora actual

    // Intenta actualizar la fecha de devolución real en la tabla `vale`
    $queryVale = "UPDATE vale SET FechaDevolucionReal = ?, PersonaRecibe = ? WHERE NumeroVale = ?";
    $stmtVale = $conexion->prepare($queryVale);
    $stmtVale->bind_param('ssi', $fechaActual, $personaRecibe, $numeroVale);
    $resultVale = $stmtVale->execute();

    // Intenta actualizar el estado de los bienes en `detalles_bien` a "Disponible"
    $queryBienes = "UPDATE detalles_bien JOIN detalle_vale ON detalles_bien.ID = detalle_vale.Bien_ID SET detalles_bien.Estado = 'Disponible' WHERE detalle_vale.NumeroVale = ?";
    $stmtBienes = $conexion->prepare($queryBienes);
    $stmtBienes->bind_param('i', $numeroVale);
    $resultBienes = $stmtBienes->execute();

    $success = $resultVale && $resultBienes;

    //  SweetAlert basado en el resultado
    if ($success) {
        $script = "<script>Swal.fire('Éxito', 'La devolución ha sido registrada exitosamente.', 'success').then((result) => { window.close(); });</script>";
    } else {
        $script = "<script>Swal.fire('Error', 'No se pudo registrar la devolución. Intente de nuevo.', 'error').then((result) => { window.close(); });</script>";
    }

    // Cerrar conexiones
    $stmtVale->close();
    $stmtBienes->close();
    $conexion->close();
} else {
    //$script = "<script>Swal.fire('Error', 'Número de vale inválido.', 'error').then((result) => { window.close(); });</script>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultado de la Devolución - Submódulo PISC</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <?php echo $script; // Imprimir el script del SweetAlert ?>
</body>
</html>
