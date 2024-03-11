<?php
include('../../db.php');
include('../control_acceso.php');
verificarAcceso(['Administrador']);
$numeroVale = $_GET['numeroVale'] ?? null;

if (!$numeroVale) {
    die("Número de Vale no proporcionado.");
}

// Consulta para obtener los detalles del préstamo
$stmt = $conexion->prepare("SELECT v.NumeroVale, v.FechaHoraPrestamo, d.Nombre AS Deudor, 
CASE WHEN v.FechaDevolucionReal IS NULL THEN 'Activo' 
ELSE 'Finalizado' END AS EstadoPrestamo 
FROM vale v JOIN deudor d ON v.Deudor_ID = d.ID WHERE v.NumeroVale = ?");

$stmt->bind_param("i", $numeroVale);
$stmt->execute();
$prestamo = $stmt->get_result()->fetch_assoc();

$stmt->close();
$conexion->close();

if (!$prestamo) {
    die("No se encontró el préstamo con el Número de Vale proporcionado.");
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Eliminar Vale y Préstamo - Submódulo PISC</title>
    <!-- Incluir Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
</head>

<body>
    <div class="container mt-3">
        <h2>Eliminar vale con préstamos asociados</h2>
        <br>
        <div class="alert alert-danger" role="alert">
            ¡ATENCIÓN! - El realizar esta acción puede generar inconsistencia en los vales generados. Los bienes prestados se marcarán
            como disponibles posterior a su eliminación.
        </div>
        <div class="card">
            <div class="card-body">
                <p><strong>Número de Vale:</strong> <?= htmlspecialchars($prestamo['NumeroVale']) ?></p>
                <p><strong>Fecha y Hora del Préstamo:</strong> <?= htmlspecialchars($prestamo['FechaHoraPrestamo']) ?></p>
                <p><strong>Deudor:</strong> <?= htmlspecialchars($prestamo['Deudor']) ?></p>
                <p><strong>Estado del Préstamo:</strong> <?= htmlspecialchars($prestamo['EstadoPrestamo']) ?></p>

                <!-- Formulario para enviar el NumeroVale -->
                <form id="eliminarPrestamoForm" action="admonPrestamosProcesarEliminarPrestamo.php" method="POST">
                    <input type="hidden" name="numeroVale" value="<?= $prestamo['NumeroVale'] ?>">
                    <button type="button" id="eliminarPrestamoBtn" class="btn btn-danger">Proceder a la eliminación del vale y préstamos asociados</button>
                </form>
            </div>
        </div>
    </div>

    <script src="scriptHistorialPrestamosEliminarPrestamo.js"></script>
</body>

</html>