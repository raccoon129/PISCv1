<?php
include('../../db.php');
include('../control_acceso.php');

// Variables para los filtros
$estadoPrestamo = isset($_POST['estadoPrestamo']) ? $_POST['estadoPrestamo'] : '';
$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

if ($accion === 'exportar' && isset($_POST['formato']) && $_POST['formato'] === 'CSV') {
    ob_start();  // Iniciar captura de salida para evitar problemas de salida prematura

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

    $fechaHoraActual = date('Ymd_His'); // Formato de ejemplo: 20230308_143501
    // Aplicar filtro de estado de préstamo si es necesario
    if ($estadoPrestamo == 'activo') {
        $sql .= " HAVING EstadoPrestamo = 'Préstamo activo'";
    } elseif ($estadoPrestamo == 'finalizado') {
        $sql .= " HAVING EstadoPrestamo = 'Préstamo finalizado'";
    }

    $sql .= " ORDER BY v.FechaHoraPrestamo DESC";
    $resultados = $conexion->query($sql);

    if ($resultados->num_rows > 0) {
        // Preparar cabeceras para descarga de CSV
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"historial_prestamos_{$fechaHoraActual}.csv\"");
        
        // Abrir el flujo de salida
        $salida = fopen('php://output', 'w');

        // Escribir la BOM para UTF-8
        fprintf($salida, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Escribir encabezados de las columnas del CSV
        fputcsv($salida, array('NumeroVale', 'FechaHoraPrestamo', 'Deudor', 'Rol', 'DescripcionBienes', 'FechaDevolucionPrevista', 'FechaDevolucionReal', 'EstadoPrestamo'), ';');

        // Rellenar el archivo CSV con los datos de la base de datos
        while ($fila = $resultados->fetch_assoc()) {
            fputcsv($salida, array(
                $fila['NumeroVale'], 
                $fila['FechaHoraPrestamo'], 
                $fila['Deudor'], 
                $fila['Rol'], 
                $fila['DescripcionBienes'], 
                $fila['FechaDevolucionPrevista'], 
                $fila['FechaDevolucionReal'] ? $fila['FechaDevolucionReal'] : 'No se ha devuelto', 
                $fila['EstadoPrestamo']
            ), ';');
        }
        fclose($salida);
        exit;
        ob_end_clean();  // Limpiar el buffer y desactivar la captura de salida
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
        $pdf->SetX(152.5);
        $pdf->Cell(0, 10, utf8_decode('Reporte de Historial de Préstamos'), 0, 1);
        $pdf->Ln(2);

        // Configurar fuente para la información del laboratorio y fecha
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(10);
        $pdf->Cell(0, 10, utf8_decode("Laboratorio: $nombreLaboratorio"), 0, 1);
        $pdf->SetX(10);
        $pdf->Cell(0, 10, utf8_decode("Generado el: $fechaHoraLegible"), 0, 1);

        $pdf->Ln(2); // espacio antes de la tabla

        // Encabezados de columna
        $pdf->SetFont('Arial', 'B', 8);
        $header = array('No.Vale', 'Fecha del Préstamo', 'Nombre del deudor', 'Rol', 'Descripcion de los bienes', 'Devolución prevista', 'Devolución real', 'Estado del préstamo');
        $widths = array(20, 30, 30, 20, 80, 30, 30, 30); // Aumentamos el ancho de DescripcionBienes
        foreach ($header as $i => $col) {
            $pdf->Cell($widths[$i], 7, utf8_decode($col), 1, 0, 'C');
        }
        $pdf->Ln();

        // Datos de la tabla
        $pdf->SetFont('Arial', '', 8);
        function cortarTexto($texto, $maxLongitud = 18)
        {
            if (strlen(utf8_decode($texto)) > $maxLongitud) {
                return substr(utf8_decode($texto), 0, $maxLongitud - 3) . '...';
            } else {
                return utf8_decode($texto);
            }
        }

        while ($fila = $resultados->fetch_assoc()) {
            $pdf->Cell($widths[0], 6, cortarTexto($fila['NumeroVale'], 15), 1);
            $pdf->Cell($widths[1], 6, cortarTexto($fila['FechaHoraPrestamo'], 20), 1);
            $pdf->Cell($widths[2], 6, cortarTexto($fila['Deudor']), 1);
            $pdf->Cell($widths[3], 6, cortarTexto($fila['Rol']), 1);
            $pdf->Cell($widths[4], 6, cortarTexto($fila['DescripcionBienes'], 61), 1); // DescripcionBienes puede ser más largo
            $pdf->Cell($widths[5], 6, cortarTexto($fila['FechaDevolucionPrevista']), 1);
            $pdf->Cell($widths[6], 6, cortarTexto($fila['FechaDevolucionReal'], 30 ? $fila['FechaDevolucionReal'] : 'No se ha devuelto'),  1);
            $pdf->Cell($widths[7], 6, cortarTexto($fila['EstadoPrestamo'], 30), 1);
            $pdf->Ln();
        }

        // Número de página en la parte inferior
        $pdf->AliasNbPages();
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->SetY(111);
        $pdf->Cell(0, 10, utf8_decode('Página: ') . $pdf->PageNo() . '/{nb}', 0, 0, 'C');

        // Salida del PDF al navegador
        $pdf->Output('I', "historial_prestamos_{$fechaHoraActual}.pdf");
        exit;
    }
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
            Para una correcta visualización del CSV exportado, considere emplear Apache OpenOffice. Puede obtenerlo sin compromiso dando clic <a href="https://www.openoffice.org/es/descargar/" class="alert-link">aquí</a>.
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

            <!-- Selector de formato -->
            <div class="form-group">
                <label for="formato">Formato:</label>
                <select class="form-control" id="formato" name="formato">
                    <option value="CSV">CSV</option>
                    <option value="PDF">PDF</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success" name="accion" value="exportar">Exportar</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    
</body>

</html>