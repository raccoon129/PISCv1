<?php
include '../../db.php';
include('../control_acceso.php');
verificarAcceso(['Administrador']);

$formatoExportacion = $_POST['formatoExportacion'] ?? '';
$accion = $_POST['accion'] ?? '';

// Consulta a la base de datos para obtener los bienes dados de baja
$sql = "SELECT i.InventarioID, i.BienID, i.Descripcion, i.Marca, i.Modelo, i.Ubicacion
        FROM inventario i
        JOIN detalles_bien db ON i.BienID = db.ID
        WHERE db.Estado = 'Baja' ORDER BY i.InventarioID DESC";
$result = $conexion->query($sql);

// Leer las observaciones y fechas de baja desde el archivo JSON
$jsonFilePath = '../bienesDadosDeBaja.json';
$bajasJson = file_exists($jsonFilePath) ? json_decode(file_get_contents($jsonFilePath), true) : [];

// Preparar un array para acceder más fácilmente a los datos por BienID
$bajasDatos = [];
foreach ($bajasJson as $baja) {
    if (isset($baja['BienID'])) {
        $bajasDatos[$baja['BienID']] = $baja;
    }
}

// Gestionar la exportación antes de cualquier salida HTML
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $accion == 'Exportar') {
    if ($formatoExportacion === 'csv') {
        // Generar una cadena con la fecha y hora actuales
        $fechaHoraActual = date('Ymd_His'); // Formato de ejemplo: 20230308_143501
    
        header('Content-Type: text/csv; charset=utf-8');
        // Incluir la BOM de UTF-8 y la fecha y hora en el nombre del archivo
        header("Content-Disposition: attachment; filename=\"datos_bienes_dados_baja_{$fechaHoraActual}.csv\"");
    
        $salida = fopen('php://output', 'w');
    
        // Escribir la BOM de UTF-8 al inicio del archivo para asegurar la correcta codificación
        fprintf($salida, chr(0xEF) . chr(0xBB) . chr(0xBF));
    
        fputcsv($salida, array('No.', 'No. de Inventario', 'Descripción', 'Marca', 'Modelo', 'Ubicación', 'Observaciones', 'Fecha de Baja'), ';');
        while ($fila = $result->fetch_assoc()) {
            fputcsv($salida, array(
                $fila['InventarioID'],
                $fila['BienID'],
                $fila['Descripcion'],
                $fila['Marca'],
                $fila['Modelo'],
                $fila['Ubicacion'],
                $bajasDatos[$fila['BienID']]['Observaciones'] ?? 'Sin observaciones',
                $bajasDatos[$fila['BienID']]['FechaBaja'] ?? 'Fecha no registrada'
            ), ';');
        }
        fclose($salida);
        exit;
    
    } elseif ($formatoExportacion === 'pdf') {
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
        $pdf->SetX(163.3);
        $pdf->Cell(0, 10, utf8_decode('Reporte de bienes dados de baja'), 0, 1);
        $pdf->Ln(2);
        // Configurar fuente para la información del laboratorio y fecha
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(10);
        $pdf->Cell(0, 10, utf8_decode("Laboratorio: $nombreLaboratorio"), 0, 1);
        $pdf->SetX(10);
        $pdf->Cell(0, 10, utf8_decode("Generado el: $fechaHoraLegible"), 0, 1);

        $pdf->SetX(10);
        $pdf->Cell(0, 10, utf8_decode("Todos los bienes dados de baja desde siempre."), 0, 1);


        $pdf->Ln(2); // Añadir un poco de espacio antes de la tabla

        // Encabezados de columna
        $pdf->SetFont('Arial', 'B', 10);
        $header = array('No.', 'No. de Inventario', 'Descripcion', 'Marca', 'Modelo', 'Ubicacion', 'Observaciones', 'Fecha de baja');
        $widths = array(10, 30, 50, 30, 30, 30, 65, 30, 30); // Anchos
        for ($i = 0; $i < count($header); $i++) {
            $pdf->Cell($widths[$i], 7, utf8_decode($header[$i]), 1, 0, 'C');
        }
        $pdf->Ln();

        // Datos de la tabla
        $pdf->SetFont('Arial', '', 8);

        function cortarTexto($texto, $maxLongitud = 17)
        {
            if (strlen(utf8_decode($texto)) > $maxLongitud) {
                return substr(utf8_decode($texto), 0, $maxLongitud - 3) . '...';
            } else {
                return utf8_decode($texto);
            }
        }

        // Luego, dentro del bucle while, se utiliza esta function cortarTexto para cada dato
        while ($fila = $result->fetch_assoc()) {
            $pdf->Cell($widths[0], 6, cortarTexto($fila['InventarioID']), 1);
            $pdf->Cell($widths[1], 6, cortarTexto($fila['BienID']), 1);
            $pdf->Cell($widths[2], 6, cortarTexto($fila['Descripcion'], 31), 1); // Límite ampliado para 'Descripcion'
            $pdf->Cell($widths[3], 6, cortarTexto($fila['Marca']), 1);
            $pdf->Cell($widths[4], 6, cortarTexto($fila['Modelo']), 1);
            $pdf->Cell($widths[5], 6, cortarTexto($fila['Ubicacion'],31), 1);
            $pdf->Cell($widths[6], 6, cortarTexto($bajasDatos[$fila['BienID']]['Observaciones'] ?? 'Sin observaciones', 31), 1);
            $pdf->Cell($widths[7], 6, cortarTexto($bajasDatos[$fila['BienID']]['FechaBaja'] ?? 'Fecha no registrada',31), 1);
            $pdf->Ln();
        }

        // Número de página en la parte inferior. Hay un bug pendiente, se queda asi mientras veo como resolverlo.
        // Solo se muestra en la ultima pag
        $pdf->AliasNbPages();
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->SetY(0);
        $pdf->Cell(0, 10, utf8_decode('Página: ') . $pdf->PageNo() . '/{nb}', 0, 0, 'C');

        // Salida del PDF al navegador
        $pdf->Output('I', "datos_bienes_dados_baja_{$fechaHoraActual}.pdf");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditar Bienes Dados de Baja - Submódulo PISC</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-3">
        <h2>Auditar Bienes Dados de Baja</h2>
        <form method="post">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="formatoExportacion" class="form-label">Formato de exportación:</label>
                    <select class="form-control" id="formatoExportacion" name="formatoExportacion">
                        <option value="csv">CSV</option>
                        <option value="pdf">PDF</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" name="accion" value="Exportar">Exportar</button>
        </form>
        <br>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No. de Inventario</th>
                    <th>Descripción</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Ubicación</th>
                    <th>Observaciones</th>
                    <th>Fecha de Baja</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?= htmlspecialchars($row['InventarioID']) ?></td>
                        <td><?= htmlspecialchars($row['BienID']) ?></td>
                        <td><?= htmlspecialchars($row['Descripcion']) ?></td>
                        <td><?= htmlspecialchars($row['Marca']) ?></td>
                        <td><?= htmlspecialchars($row['Modelo']) ?></td>
                        <td><?= htmlspecialchars($row['Ubicacion']) ?></td>
                        <td><?= htmlspecialchars($bajasDatos[$row['BienID']]['Observaciones'] ?? 'Sin observaciones') ?></td>
                        <td><?= htmlspecialchars($bajasDatos[$row['BienID']]['FechaBaja'] ?? 'Fecha no registrada') ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>