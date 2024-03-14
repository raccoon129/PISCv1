<?php

include('../../db.php');
include('../control_acceso.php');
verificarAcceso();
// Obtener el NumeroVale desde el GET request
$numeroVale = isset($_GET['numeroVale']) ? $_GET['numeroVale'] : die('ERROR: Número de Vale no proporcionado.');

//  consulta para obtener los detalles del vale, incluyendo información del deudor y del usuario que realiza el préstamo
$queryVale = "SELECT v.*, 
d.Nombre AS NombreDeudor, 
d.Rol, 
u.nombrecompleto AS NombreUsuario, 
v.PersonaRecibe
FROM vale v
JOIN deudor d ON v.Deudor_ID = d.ID
JOIN usuario u ON v.PersonaEntrega = u.ID
WHERE v.NumeroVale = ?;
";
$stmtVale = $conexion->prepare($queryVale);
$stmtVale->bind_param('i', $numeroVale);
$stmtVale->execute();
$resultVale = $stmtVale->get_result();
$detalleVale = $resultVale->fetch_assoc();

// consulta para obtener los bienes asociados a este vale
$queryBienes = "SELECT i.*, db.Estado AS EstadoBien
                FROM detalle_vale dv
                JOIN detalles_bien db ON dv.Bien_ID = db.ID
                JOIN inventario i ON db.ID = i.BienID
                WHERE dv.NumeroVale = ?";

$stmtBienes = $conexion->prepare($queryBienes);
$stmtBienes->bind_param('i', $numeroVale);
$stmtBienes->execute();
$resultBienes = $stmtBienes->get_result();

$stmtVale->close();
$stmtBienes->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Detalles del Préstamo - Submódulo PISC</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-3">
        <h2>Detalles del Préstamo #<?= htmlspecialchars($numeroVale) ?></h2>
        <div><strong>Deudor:</strong> <?= htmlspecialchars($detalleVale['NombreDeudor']) ?> (<?= htmlspecialchars($detalleVale['Rol']) ?>)</div>
        <div><strong>Prestamista que entregó los bienes:</strong> <?= htmlspecialchars($detalleVale['NombreUsuario']) ?></div>
        <div><strong>Prestamista que recibió los bienes:</strong> <?= $detalleVale['PersonaRecibe'] ? htmlspecialchars($detalleVale['PersonaRecibe']) : 'No se ha registrado devolución.' ?></div>
        <div><strong>Fecha de préstamo:</strong> <?= htmlspecialchars($detalleVale['FechaHoraPrestamo']) ?></div>
        <div><strong>Fecha prevista de devolución:</strong> <?= htmlspecialchars($detalleVale['FechaDevolucionPrevista']) ?></div>
        <div><strong>Fecha real de devolución:</strong> <?= htmlspecialchars($detalleVale['FechaDevolucionReal'] ?? 'No devuelto aún') ?></div>
        <div><strong>Observaciones:</strong> <?= htmlspecialchars($detalleVale['observaciones']) ?></div>
        <br>
        <h3>Bienes Asociados</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>No. Serie</th>
                    <th>Ubicación</th>
                    <th>Estado Actual</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($bien = $resultBienes->fetch_assoc()) : ?>
                    <tr>
                        <td><?= htmlspecialchars($bien['Descripcion']) ?></td>
                        <td><?= htmlspecialchars($bien['Marca']) ?></td>
                        <td><?= htmlspecialchars($bien['Modelo']) ?></td>
                        <td><?= htmlspecialchars($bien['No_Serie']) ?></td>
                        <td><?= htmlspecialchars($bien['Ubicacion']) ?></td>
                        <td><?= htmlspecialchars($bien['EstadoBien']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>