<?php
include '../../db.php'; 
include('../control_acceso.php');
verificarAcceso(['Administrador']);
// Variables para los filtros
$ubicacionSeleccionada = isset($_POST['ubicacion']) ? $_POST['ubicacion'] : '';
$formato = isset($_POST['formato']) ? $_POST['formato'] : '';
$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

// Preparar la consulta base
$sql = "SELECT i.InventarioID, i.BienID, i.Descripcion, i.Marca, i.No_Serie, i.Modelo, i.Ubicacion, i.Estado, db.Estado 
AS EstadoBien FROM inventario i JOIN detalles_bien db ON i.BienID = db.ID";

// Agregar filtro de ubicación a la consulta si es necesario
if (!empty($ubicacionSeleccionada)) {
    $sql .= " WHERE i.Ubicacion = '" . $ubicacionSeleccionada . "'";
}

$sql .= " ORDER BY i.InventarioID DESC";

$resultados = $conexion->query($sql);

// Si se solicita exportar, gestionar la exportación antes de cualquier salida HTML
if ($accion === 'exportar' && $resultados->num_rows > 0) {
    if ($formato === 'CSV') {
        // Generar una cadena con la fecha y hora actuales
        $fechaHoraActual = date('Ymd_His'); // Formato de ejemplo: 20230308_143501

        header('Content-Type: text/csv; charset=utf-8');
        // Incluir la BOM de UTF-8 y la fecha y hora en el nombre del archivo
        header("Content-Disposition: attachment; filename=\"datos_bienes_{$fechaHoraActual}.csv\"");

        $salida = fopen('php://output', 'w');

        // Escribir la BOM de UTF-8 al inicio del archivo para asegurar la correcta codificación
        fprintf($salida, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($salida, array('No.', 'No. de Inventario', 'Descripción', 'Marca', 'No de Serie', 'Modelo', 'Ubicación', 'Estatus (Inventario)', 'Estatus (Bien)'), ';');
        while ($fila = $resultados->fetch_assoc()) {
            fputcsv($salida, array($fila['InventarioID'], $fila['BienID'], $fila['Descripcion'], $fila['Marca'], $fila['No_Serie'], $fila['Modelo'], $fila['Ubicacion'], $fila['Estado'], $fila['EstadoBien']), ';');
        }
        fclose($salida);
        exit;
    } elseif ($formato === 'PDF') {
        // Implementación para generar el PDF
    }
}

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Exportar Datos de Bienes - Submódulo PISC</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Exportar Datos de Bienes</h2>
        <div class="alert alert-primary" role="alert">
        Para una correcta visualización del archivo exportado en CSV, considere emplear Apache OpenOffice. Puede obtenerlo gratis <a href="https://www.openoffice.org/es/descargar/" class="alert-link">dando click aquí</a>.
        </div>
        <form method="post">
            <!-- Selector de ubicación -->
            <div class="form-group">
                <label for="ubicacion">Ubicación:</label>
                <select class="form-control" id="ubicacion" name="ubicacion">
                    <option value="">Todos</option>
                    <?php
                    // Obtener las ubicaciones únicas de la base de datos
                    $ubicaciones = $conexion->query("SELECT DISTINCT Ubicacion FROM inventario WHERE Ubicacion IS NOT NULL ORDER BY Ubicacion ASC");
                    while ($ubi = $ubicaciones->fetch_assoc()) {
                        echo "<option value=\"" . $ubi['Ubicacion'] . "\"" . ($ubi['Ubicacion'] == $ubicacionSeleccionada ? ' selected' : '') . ">" . $ubi['Ubicacion'] . "</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Selector de formato -->
            <div class="form-group">
                <label for="formato">Formato:</label>
                <select class="form-control" id="formato" name="formato">
                    <option value="CSV">CSV</option>
                    <option value="PDF">-</option>
                </select>
            </div>

            <!-- Botones para exportar y visualizar -->
            <button type="submit" class="btn btn-primary" name="accion" value="visualizar">Visualizar</button>
            <button type="submit" class="btn btn-success" name="accion" value="exportar">Exportar</button>
        </form>
        
        <?php if ($accion === 'visualizar' && $resultados->num_rows > 0): ?>
        <div class="mt-4">
            <table class="table">
                <thead class="thead-light">
                    <tr>
                        <th>No.</th>
                        <th>No. de Inventario</th>
                        <th>Descripción</th>
                        <th>Marca</th>
                        <th>No de Serie</th>
                        <th>Modelo</th>
                        <th>Ubicación</th>
                        <th>Estatus (Inventario)</th>
                        <th>Estatus (Bien)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($fila = $resultados->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fila['InventarioID']); ?></td>
                        <td><?php echo htmlspecialchars($fila['BienID']); ?></td>
                        <td><?php echo htmlspecialchars($fila['Descripcion']); ?></td>
                        <td><?php echo htmlspecialchars($fila['Marca']); ?></td>
                        <td><?php echo htmlspecialchars($fila['No_Serie']); ?></td>
                        <td><?php echo htmlspecialchars($fila['Modelo']); ?></td>
                        <td><?php echo htmlspecialchars($fila['Ubicacion']); ?></td>
                        <td><?php echo htmlspecialchars($fila['Estado']); ?></td>
                        <td><?php echo htmlspecialchars($fila['EstadoBien']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    <!-- Opcional: incluir jQuery y JS de Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
