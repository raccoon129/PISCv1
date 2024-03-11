<?php

include('../../db.php');
include('../control_acceso.php');
verificarAcceso(['Prestamista']);

// Consultas para obtener datos relevantes

$totalPrestamosQuery = "SELECT COUNT(*) AS total FROM vale WHERE FechaDevolucionReal IS NULL"; // Préstamos activos
$bienesPrestadosQuery = "SELECT COUNT(*) AS total FROM detalles_bien WHERE Estado = 'Prestado'";
$ultimosPrestamosQuery = "SELECT v.NumeroVale, d.Nombre, v.FechaHoraPrestamo FROM vale v JOIN deudor d ON v.Deudor_ID = d.ID ORDER BY v.FechaHoraPrestamo DESC LIMIT 5";

// Ejecutar consultas

$totalPrestamosResult = $conexion->query($totalPrestamosQuery)->fetch_assoc();
$bienesPrestadosResult = $conexion->query($bienesPrestadosQuery)->fetch_assoc();
$ultimosPrestamosResult = $conexion->query($ultimosPrestamosQuery);

$fechaHoy = date('Y-m-d');
$prestamosHoyQuery = "SELECT v.NumeroVale, v.FechaHoraPrestamo, d.Nombre AS NombreDeudor, d.Rol, v.CarreraRecibe 
FROM vale v 
JOIN deudor d ON v.Deudor_ID = d.ID 
WHERE DATE(v.FechaHoraPrestamo) = '{$fechaHoy}' 
ORDER BY v.FechaHoraPrestamo DESC";

$prestamosHoyResult = $conexion->query($prestamosHoyQuery);
// Cerrar conexión
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio Prestamista - Módulo PISC</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <style>
        body {
            background-color: rgba(255, 255, 255, 0);
            /* Cambia el color de fondo a transparente*/
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2>Resumen para el Prestamista</h2>
        <br>
        <div class="row">
            <div class="col-md-6">
                <div class="alert alert-success">
                    <strong>Préstamos Activos:</strong> <?= $totalPrestamosResult['total'] ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="alert alert-danger">
                    <strong>Bienes Prestados:</strong> <?= $bienesPrestadosResult['total'] ?>
                </div>
            </div>
        </div>
        <br>
        <h5>Últimos Préstamos</h5>
        <div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Número de Vale</th>
                <th>Fecha y Hora del Préstamo</th>
                <th>Nombre Deudor</th>
                <th>Rol del Deudor</th>
                <th>Carrera Recibe</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($prestamosHoyResult->num_rows > 0) : ?>
                <?php while ($row = $prestamosHoyResult->fetch_assoc()) : ?>
                    <tr>
                        <td><?= htmlspecialchars($row['NumeroVale']) ?></td>
                        <td><?= htmlspecialchars($row['FechaHoraPrestamo']) ?></td>
                        <td><?= htmlspecialchars($row['NombreDeudor']) ?></td>
                        <td><?= htmlspecialchars($row['Rol']) ?></td>
                        <td><?= htmlspecialchars($row['CarreraRecibe']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5">No se han realizado préstamos hoy.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>