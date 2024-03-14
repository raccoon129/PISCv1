<?php
include('../control_acceso.php');
verificarAcceso();
function recortarTexto($texto) {
    if (strlen($texto) > 15) {
        return substr($texto, 0, 15) . "...";
    } else {
        return $texto;
    }
}

function recortarTexto10($texto) {
    if (strlen($texto) > 10) {
        return substr($texto, 0, 10) . "...";
    } else {
        return $texto;
    }
}


require '../../vendor/autoload.php'; // Ajusta la ruta para subir dos niveles y luego acceder a vendor

require '../../db.php';

// ID del vale desde el formulario o el valor más alto de la base de datos
$valeID = isset($_POST['noNuevoVale']) && !empty($_POST['noNuevoVale']) ? $_POST['noNuevoVale'] : null;

if (is_null($valeID)) {
    // Consulta para obtener el último número de vale
    $consulta = "SELECT MAX(NumeroVale) AS ultimoVale FROM vale";
    $resultado = $conexion->query($consulta);
    if ($resultado && $resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $valeID = $fila['ultimoVale'];
    } else {
        die('Error al consultar el último vale: ' . $conexion->error);
    }
}

if (is_null($valeID)) {
    die('No se pudo determinar el ID del vale para generar el PDF.');
}

// obtener la información del vale
$stmt = $conexion->prepare("
    SELECT v.NumeroVale, v.FechaHoraPrestamo, v.Deudor_ID, d.Nombre AS Nombre_Deudor, 
    v.CarreraRecibe AS Procedencia, v.FechaDevolucionPrevista AS FechaDevolucion, 
    v.PersonaEntrega, u.nombrecompleto AS Nombre_Usuario_Entrega, v.observaciones, v.PersonaRecibe 
    FROM vale v
    LEFT JOIN deudor d ON v.Deudor_ID = d.ID
    LEFT JOIN usuario u ON v.PersonaEntrega = u.ID
    WHERE v.NumeroVale = ?
");

$stmt->bind_param("i", $valeID);
$stmt->execute();
$resultado = $stmt->get_result();

// Comprobar si hemos obtenido un resultado
if ($fila = $resultado->fetch_assoc()) {
    // Asignar los datos a variables
    $carrera = $fila['Procedencia'];
    $numeroVale = $fila['NumeroVale'];
    $observaciones = $fila['observaciones'];
    $nombreDeudor = $fila['Nombre_Deudor'];
    $nombreEntrega = $fila['Nombre_Usuario_Entrega'];
    $fechaHoraPrestamo = new DateTime($fila['FechaHoraPrestamo']);
    $fechaSolicitud = $fechaHoraPrestamo->format('Y-m-d'); // Formatear para mostrar solo la fecha
    $fechaDevolucion = new DateTime($fila['FechaDevolucion']);
    $fechaDevolucionPrevista = $fechaDevolucion->format('Y-m-d');
    $nombreRecibe = $fila['PersonaRecibe'];
} else {
    die('No se encontró el vale con el ID proporcionado.');
}

// Consulta para obtener los bienes asociados al vale
$stmt = $conexion->prepare("
    SELECT i.Descripcion, dv.Bien_ID
    FROM detalle_vale dv
    INNER JOIN inventario i ON dv.Bien_ID = i.BienID
    WHERE dv.NumeroVale = ?
");
$stmt->bind_param("i", $valeID);
$stmt->execute();
$bienes = $stmt->get_result();
// Almacenar en un arreglo los datos de los bienes
$bienesData = [];
while ($bien = $bienes->fetch_assoc()) {
    $bienesData[] = $bien;
}



$conexion->close();

//A partir de aquí se generar el PDF
use setasign\Fpdi\Fpdi;

$pdf = new Fpdi();

// Definir la ruta al archivo PDF importar
$pdfPath = '../../resources/F-MA-15_7.pdf';

// Agregar la página del PDF existente
$pageCount = $pdf->setSourceFile($pdfPath);
$tplIdx = $pdf->importPage(1); // Importar la primera página
$size = $pdf->getTemplateSize($tplIdx);

if ($size['orientation'] == 'P') {
    $pdf->AddPage('P', [$size['width'], $size['height']]);
} else {
    $pdf->AddPage('L', [$size['height'], $size['width']]);
}

$pdf->useTemplate($tplIdx);
// Ahora colocar texto encima del PDF importado
$pdf->SetFont('Arial', '', 10);

//  colocar texto
$pdf->SetTextColor(20, 100, 200); // Establecer el color del texto
$pdf->SetXY(8, 10); // Ajustar la posición del texto inicial

$pdf->MultiCell(0, 0, 'No de vale: ' . utf8_decode($numeroVale), 0, 'L');

//escribirDatos($pdf, 160, 26, $fechaHoraPrestamo);
$pdf->Text(118, 27, utf8_decode('Abundio Martínez'), 'LR', 1, 'L');
$pdf->Text(155, 42, utf8_decode($fechaSolicitud), 'LR', 1, 'L');
$pdf->Text(155, 50, utf8_decode($fechaDevolucionPrevista), 'LR', 1, 'L');
$pdf->Text(155, 50, utf8_decode($fechaDevolucionPrevista), 'LR', 1, 'L');
$pdf->Text(20, 42, utf8_decode(recortarTexto($carrera)), 'LR', 1, 'L');

//Recibi del laboratorio "tal"
$pdf->Text(49, 57, utf8_decode('Abundio Martínez'), 'LR', 1, 'L');

// Recorriendo la coleccion de Bienes(solo la descripcion) prestados
$ejeYPrestados = 67; // Posición inicial en Y para lo que se escribirá
foreach ($bienesData as $index => $bien) {
    $line = utf8_decode($bien['Descripcion']);
    $pdf->Text(23, $ejeYPrestados, $line);
    $ejeYPrestados += 6; // Ajuste para la siguiente línea
}

// Recorriendo la coleccion de bienesID/NoInventario
$ejeYPrestados = 67;
foreach ($bienesData as $index => $bien) {
    $line = utf8_decode(recortarTexto10($bien['Bien_ID']));
    $pdf->Text(136, $ejeYPrestados, $line);
    $ejeYPrestados += 6; // Ajuste para la siguiente línea
}

// colocando cantidad (solo uno por integridad), todos los bienes tienen distinto no inventario
$ejeYPrestados = 67;
foreach ($bienesData as $index => $bien) {
    $line = "1";
    $pdf->Text(167, $ejeYPrestados, $line);
    $ejeYPrestados += 6; // Ajuste para la siguiente línea
}

//Aca terminan los campos de lo que se presta
$pdf->Text(40, 99, utf8_decode($observaciones), 'LR', 1, 'L');
$pdf->Text(17, 133, utf8_decode(recortarTexto($nombreDeudor)), 'LR', 1, 'L');
$pdf->Text(57, 133, utf8_decode(recortarTexto($nombreEntrega)), 'LR', 1, 'L');

//Acá la persona que recibe los bienes. Solo aparece cuando ya se han devuelto.
$pdf->Text(99, 133, utf8_decode(recortarTexto($nombreRecibe)), 'LR', 1, 'L');
// La misma persona que se le prestan los bienes, debe entregarlos.
$pdf->Text(140, 133, utf8_decode(recortarTexto($nombreEntrega)), 'LR', 1, 'L');
$pdf->Output('I', 'valeGenerado.pdf'); // Cambiar 'D' por 'I' para abrir el PDF en el navegador

?>

