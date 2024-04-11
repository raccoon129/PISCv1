<?php
include('../control_acceso.php');
include('../../db.php');
verificarAcceso(['Prestamista']);
$descripcion = $_GET['descripcion'] ?? '';

$sql = "SELECT inventario.BienID, inventario.Descripcion, inventario.Marca, inventario.Modelo, inventario.Ubicacion 
        FROM inventario
        JOIN detalles_bien ON inventario.BienID = detalles_bien.ID
        WHERE inventario.Estado = 'Correcto'
        AND detalles_bien.Estado = 'Disponible'
        AND (inventario.Descripcion LIKE ? OR inventario.BienID LIKE ?)";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    die("Error al preparar la consulta: " . $conexion->error);
}

$descripcionBusqueda = "%" . $descripcion . "%";
$stmt->bind_param("ss", $descripcionBusqueda, $descripcionBusqueda);
$stmt->execute();
$result = $stmt->get_result();

$bienes = "<ul class='list-group'>";
while ($row = $result->fetch_assoc()) {
    $bienes .= "<li class='list-group-item bien-item' data-bienid='{$row["BienID"]}' data-descripcion='{$row["Descripcion"]}' data-ubicacion='{$row["Ubicacion"]}'>" .
        htmlspecialchars($row["BienID"]) . " - " . htmlspecialchars($row["Descripcion"]) . " | " .
        htmlspecialchars("Marca: " . $row["Marca"]) . " - " .
        htmlspecialchars("Modelo: " . $row["Modelo"]) . "</li>";
}
$bienes .= "</ul>";

echo $bienes;
?>
