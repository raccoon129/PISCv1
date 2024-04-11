<?php

include '../../db.php';
include('../control_acceso.php');
verificarAcceso(['Administrador']);

header('Content-Type: application/json');

// Función para verificar si el BienID existe
function verificarBienID($conexion, $bienID) {
    $stmt = $conexion->prepare("SELECT 1 FROM inventario WHERE BienID = ?");
    $stmt->bind_param("s", $bienID);
    $stmt->execute();
    $result = $stmt->get_result();
    $existe = $result->num_rows > 0;
    $stmt->close();
    return $existe;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bienID = $_POST['bienID'] ?? '';
    
    if (isset($_POST['accion']) && $_POST['accion'] === 'verificarBienID') {
        $existe = verificarBienID($conexion, $bienID);
        echo json_encode(['success' => true, 'existe' => $existe]);
        exit;
    }

    $descripcion = $_POST['descripcion'];
    $marca = $_POST['marca'];
    $noSerie = $_POST['noSerie'];
    $modelo = $_POST['modelo'];
    $ubicacion = $_POST['ubicacion'];
    $estado = $_POST['estado'];

    if (verificarBienID($conexion, $bienID)) {
        echo json_encode(['success' => false, 'message' => 'El bien con este ID ya existe.']);
        exit;
    }

    // Intentar insertar en la tabla detalles_bien
    $stmt = $conexion->prepare("INSERT INTO detalles_bien (ID, Estado) VALUES (?, 'Disponible')");
    $stmt->bind_param("s", $bienID);
    $success = $stmt->execute();

    // Intentar insertar en la tabla inventario
    if ($success) {
        $stmt = $conexion->prepare("INSERT INTO inventario (BienID, Descripcion, Marca, No_Serie, Modelo, Ubicacion, Estado) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $bienID, $descripcion, $marca, $noSerie, $modelo, $ubicacion, $estado);
        $success = $stmt->execute();
    }

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'El bien se ha añadido correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al añadir el bien.']);
    }

    $stmt->close();
    $conexion->close();
}
?>
