<?php

include('../../db.php');
include('../control_acceso.php');
verificarAcceso(['Prestamista']);
// Variables para los filtros
$estadoPrestamo = isset($_POST['estadoPrestamo']) ? $_POST['estadoPrestamo'] : '';

// Preparar la consulta base
$sql = "SELECT v.NumeroVale, v.FechaHoraPrestamo, d.Nombre AS Deudor, d.Rol, 
GROUP_CONCAT(i.Descripcion SEPARATOR ', ') AS DescripcionBienes, 
v.FechaDevolucionPrevista, v.FechaDevolucionReal,
CASE 
    WHEN v.FechaDevolucionReal IS NOT NULL THEN 'Préstamo finalizado' 
    ELSE 'Préstamo activo' 
END AS EstadoPrestamo
FROM vale v
JOIN deudor d ON v.Deudor_ID = d.ID
JOIN detalle_vale dv ON v.NumeroVale = dv.NumeroVale
JOIN inventario i ON dv.Bien_ID = i.BienID
GROUP BY v.NumeroVale";

// Aplicar filtro de estado de préstamo si es necesario
if ($estadoPrestamo == 'activo') {
    $sql .= " HAVING EstadoPrestamo = 'Préstamo activo'";
} elseif ($estadoPrestamo == 'finalizado') {
    $sql .= " HAVING EstadoPrestamo = 'Préstamo finalizado'";
}

$sql .= " ORDER BY v.FechaHoraPrestamo DESC";

$resultados = $conexion->query($sql);

// Si se solicita exportar
if (isset($_POST['accion']) && $_POST['accion'] === 'exportar' && $resultados->num_rows > 0) {
    // Generar una cadena con la fecha y hora actuales
    $fechaHoraActual = date('Ymd_His'); // Formato de ejemplo: 20230308_143501

    header('Content-Type: text/csv; charset=utf-8');
    // Incluir la fecha y hora en el nombre del archivo
    header("Content-Disposition: attachment; filename=\"historial_prestamos_{$fechaHoraActual}.csv\"");
    $salida = fopen('php://output', 'w');

    // Escribir la BOM de UTF-8 al inicio del archivo
    fprintf($salida, chr(0xEF).chr(0xBB).chr(0xBF));

    // Las cabeceras del CSV
    fputcsv($salida, array('NumeroVale', 'FechaHoraPrestamo', 'Deudor', 'Rol', 'DescripcionBienes', 'FechaDevolucionPrevista', 'FechaDevolucionReal', 'EstadoPrestamo'), ';');

    // Recorrer los resultados y escribirlos en el archivo CSV
    while ($fila = $resultados->fetch_assoc()) {
        fputcsv($salida, array($fila['NumeroVale'], $fila['FechaHoraPrestamo'], $fila['Deudor'], $fila['Rol'], $fila['DescripcionBienes'], $fila['FechaDevolucionPrevista'], $fila['FechaDevolucionReal'] ? $fila['FechaDevolucionReal'] : 'No se ha devuelto', $fila['EstadoPrestamo']), ';');
    }
    
    fclose($salida);
    exit;
}

$conexion->close();
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Exportar Historial de Préstamos - Submódulo PISC</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Exportar Historial de Préstamos</h2>

        <div class="alert alert-primary" role="alert">
        Para una correcta visualización del archivo exportado, considere emplear Apache OpenOffice. Puede obtenerlo gratis <a href="https://www.openoffice.org/es/descargar/" class="alert-link">dando click aquí</a>.
        </div>
        <form method="post">
            <!-- Selector de estado del préstamo -->
            <div class="form-group">
                <label for="estadoPrestamo">Estado del Préstamo:</label>
                <select class="form-control" id="estadoPrestamo" name="estadoPrestamo">
                    <option value="">Todos</option>
                    <option value="activo">Préstamos Activos</option>
                    <option value="finalizado">Préstamos Finalizados</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success" name="accion" value="exportar">Exportar a CSV</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>