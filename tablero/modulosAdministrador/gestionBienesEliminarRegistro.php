<?php
include '../../db.php'; 
include('../control_acceso.php');
verificarAcceso(['Administrador']);
// Si se envió el parámetro 'id' por GET
if (isset($_GET['id'])) {
    $inventarioID = $_GET['id'];

    // Preparar sentencia para prevenir inyecciones SQL
    $stmt = $conexion->prepare("DELETE FROM inventario WHERE InventarioID = ?");
    $stmt->bind_param("i", $inventarioID);

    if ($stmt->execute()) {
        echo "Registro eliminado con éxito";
        // Redirigir con un mensaje de éxito
        header("Location: gestionBienes.php?status=success");
    } else {
        echo "Error al eliminar registro";
        // Redirigir con un mensaje de error
        header("Location: gestionBienes.php?status=error");
    }

    $stmt->close();
    // No es necesario cerrar la conexión aquí, puedes dejarla abierta para futuras consultas
    // $conexion->close();

    exit();
}

// Redirigir de vuelta a la página principal si no se ha enviado el parámetro 'id' por GET
//header("Location: gestionBienes.php");
exit();
?>