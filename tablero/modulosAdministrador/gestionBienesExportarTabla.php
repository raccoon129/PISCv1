<?php
include '../../db.php';
include('../control_acceso.php');
verificarAcceso(['Administrador']);
// Variables para los filtros
$ubicacionSeleccionada = isset($_POST['ubicacion']) ? $_POST['ubicacion'] : '';
$formato = isset($_POST['formato']) ? $_POST['formato'] : '';
$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

$sql = "SELECT i.InventarioID, i.BienID, i.Descripcion, i.Marca, i.No_Serie, i.Modelo, i.Ubicacion, i.Estado, db.Estado AS EstadoBien 
FROM inventario i 
JOIN detalles_bien db ON i.BienID = db.ID 
WHERE db.Estado <> 'Baja'"; // Excluye los bienes dados de baja

// filtro de ubicación a la consulta si es necesario
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
        fprintf($salida, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($salida, array('No.', 'No. de Inventario', 'Descripción', 'Marca', 'No de Serie', 'Modelo', 'Ubicación', 'Estatus (Inventario)', 'Estatus (Bien)'), ';');
        while ($fila = $resultados->fetch_assoc()) {
            fputcsv($salida, array($fila['InventarioID'], $fila['BienID'], $fila['Descripcion'], $fila['Marca'], $fila['No_Serie'], $fila['Modelo'], $fila['Ubicacion'], $fila['Estado'], $fila['EstadoBien']), ';');
        }
        fclose($salida);
        exit;

    } elseif ($formato === 'PDF') {
        // Implementación para generar el PDF
        require_once('../../vendor/autoload.php'); 

        $configuracion = json_decode(file_get_contents('../variablesConfiguracion.json'), true);
        $nombreLaboratorio = $configuracion['NombreLaboratorio'];

        // Generar una cadena con la fecha y hora actuales para el nombre del archivo
        $fechaHoraActual = date('Ymd_His');
        $fechaHoraLegible = date('d/m/Y H:i:s');

        $pdf = new FPDF('L');
        $pdf->SetMargins(10, 10, 10); // Margen izquierdo, superior y derecho de 10 mm
        $pdf->AddPage();

        $pdf->Image('../../img/transparenteMediano.png', 10, 6, 30);

        // Configurar fuente para el título y la fecha
        $pdf->SetFont('Arial', 'B', 22);
        $pdf->SetX(217);
        $pdf->Cell(0, 10, utf8_decode('Reporte de bienes'), 0, 1);
        $pdf->Ln(2);
        // Configurar fuente para la información del laboratorio y fecha
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(10);
        $pdf->Cell(0, 10, utf8_decode("Laboratorio: $nombreLaboratorio"), 0, 1);
        $pdf->SetX(10);
        $pdf->Cell(0, 10, utf8_decode("Generado el: $fechaHoraLegible"), 0, 1);
        if (!empty($ubicacionSeleccionada)) {
            $pdf->SetX(10);
            $pdf->Cell(0, 10, utf8_decode("Ubicación: $ubicacionSeleccionada"), 0, 1);
        } else {
            $pdf->SetX(10);
            $pdf->Cell(0, 10, utf8_decode("Ubicación: Todos los registros"), 0, 1);
        }

        $pdf->Ln(2); // Añadir un poco de espacio antes de la tabla

        // Encabezados de columna
        $pdf->SetFont('Arial', 'B', 10);
        $header = array('No.', 'No. de Inventario', 'Descripcion', 'Marca', 'No de Serie', 'Modelo', 'Ubicacion', 'Estatus (Inventario)', 'Estatus (Bien)');
        $widths = array(10, 30, 50, 30, 30, 30, 30, 35, 30); // Anchos
        for ($i = 0; $i < count($header); $i++) {
            $pdf->Cell($widths[$i], 7, utf8_decode($header[$i]), 1, 0, 'C');
        }
        $pdf->Ln();

        // Datos de la tabla
        $pdf->SetFont('Arial', '', 8);

        function cortarTexto($texto, $maxLongitud = 17) {
            if (strlen(utf8_decode($texto)) > $maxLongitud) {
                return substr(utf8_decode($texto), 0, $maxLongitud - 3) . '...';
            } else {
                return utf8_decode($texto);
            }
        }
        
        // Luego, dentro del bucle while, se utiliza esta function cortarTexto para cada dato
        while ($fila = $resultados->fetch_assoc()) {
            $pdf->Cell($widths[0], 6, cortarTexto($fila['InventarioID']), 1);
            $pdf->Cell($widths[1], 6, cortarTexto($fila['BienID']), 1);
            $pdf->Cell($widths[2], 6, cortarTexto($fila['Descripcion'], 31), 1); // Límite ampliado para 'Descripcion'
            $pdf->Cell($widths[3], 6, cortarTexto($fila['Marca']), 1);
            $pdf->Cell($widths[4], 6, cortarTexto($fila['No_Serie']), 1);
            $pdf->Cell($widths[5], 6, cortarTexto($fila['Modelo']), 1);
            $pdf->Cell($widths[6], 6, cortarTexto($fila['Ubicacion']), 1);
            $pdf->Cell($widths[7], 6, cortarTexto($fila['Estado']), 1);
            $pdf->Cell($widths[8], 6, cortarTexto($fila['EstadoBien']), 1);
            $pdf->Ln();
        }
        

        // Número de página en la parte inferior. Hay un bug pendiente, se queda asi mientras veo como resolverlo.
        // Solo se muestra en la ultima pag
        $pdf->AliasNbPages();
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->SetY(0);
        $pdf->Cell(0, 10, utf8_decode('Página: ') . $pdf->PageNo() . '/{nb}', 0, 0, 'C');

        // Salida del PDF al navegador
        $pdf->Output('I', "datos_bienes_{$fechaHoraActual}.pdf");
        exit;
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
            <li>Se exportarán únicamente los bienes activos.</li>
            Para una correcta visualización del archivo exportado en CSV considere emplear Apache OpenOffice. 
            <br>
            Puede obtenerlo sin compromiso dando clic <a href="https://www.openoffice.org/es/descargar/" class="alert-link">aquí</a>.
        </div>
        <form method="post">
            <!-- Selector de ubicación -->
            <div class="form-group">
                <label for="ubicacion">Ubicación:</label>
                <select class="form-control" id="ubicacion" name="ubicacion">
                    <option value="">Todos</option>
                    <?php
                    // Obtener las ubicaciones únicas de la base de datos
                    $ubicaciones = $conexion->query("SELECT DISTINCT Ubicacion FROM inventario i JOIN detalles_bien db 
                    ON i.BienID = db.ID WHERE db.Estado <> 'Baja' AND Ubicacion IS NOT NULL ORDER BY Ubicacion ASC");

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
                    <option value="PDF">PDF</option>
                </select>
            </div>

            <!-- Botones para exportar y visualizar -->
            <button type="submit" class="btn btn-success" name="accion" value="exportar">Exportar</button>
            <button type="submit" class="btn btn-primary" name="accion" value="visualizar">Visualizar</button>
        </form>

        <?php if ($accion === 'visualizar' && $resultados->num_rows > 0) : ?>
            <div class="mt-4">
                <table class="table table-striped">
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
                        <?php while ($fila = $resultados->fetch_assoc()) : ?>
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