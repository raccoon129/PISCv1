<?php
include '../../db.php';
include('../control_acceso.php');
verificarAcceso(['Administrador']);
// Esta es una extensión de gestionBienesVolcarDatosBienes.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['importar']) && !empty($_POST['datos'])) {
    $datos = json_decode($_POST['datos'], true);
    $consultaMaxId = $conexion->query("SELECT MAX(InventarioID) AS maximo FROM inventario");
    $resultadoMaxId = $consultaMaxId->fetch_assoc();
    $inventarioIDActual = (int)($resultadoMaxId['maximo'] ?? 0) + 1;
    //Se emplea una transacción porque cualquier cosa puede fallar. En condiciones optimas la inserción se 
    //debería realizar sin problemas en teoría jajaj
    $conexion->begin_transaction();
    try {
        foreach ($datos as $index => $fila) {
            // Primero, insertar en detalles_bien para asegurar que el BienID existe
            $sqlDetalles = "INSERT INTO detalles_bien (ID, Estado) VALUES (?, 'Disponible')";
            $stmtDetalles = $conexion->prepare($sqlDetalles);
            $stmtDetalles->bind_param("s", $fila[0]); // Se asume que $fila[0] es BienID
            if (!$stmtDetalles->execute()) {
                throw new Exception("Error al insertar en detalles_bien: " . $stmtDetalles->error);
            }
            $stmtDetalles->close();

            // insertamos en inventario
            $inventarioID = $inventarioIDActual + $index;
            $sqlInventario = "INSERT INTO inventario (InventarioID, BienID, Descripcion, Marca, No_Serie, Modelo, Ubicacion, Estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtInventario = $conexion->prepare($sqlInventario);
            $stmtInventario->bind_param("isssssss", $inventarioID, $fila[0], $fila[1], $fila[2], $fila[3], $fila[4], $fila[5], $fila[6]);
            if (!$stmtInventario->execute()) {
                throw new Exception("Error al insertar en inventario: " . $stmtInventario->error);
            }
            $stmtInventario->close();
        }
        $conexion->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conexion->rollback();
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}
?>
