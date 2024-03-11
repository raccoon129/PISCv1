<?php
include '../../db.php'; 
include('../control_acceso.php');
verificarAcceso(['Administrador']);

// CONSULTA SUBMODULO PISC
$numeroVale = $_POST['numeroVale'] ?? null;

if (!$numeroVale) {
    echo "<script>alert('Número de Vale no proporcionado.'); window.close();</script>";
    exit;
}

// Iniciar transacción
$conexion->begin_transaction();

try {
    // Primero, eliminar registros en detalle_vale que hagan referencia al NumeroVale.
    $eliminarDetallesVale = $conexion->prepare("DELETE FROM detalle_vale WHERE NumeroVale = ?");
    $eliminarDetallesVale->bind_param("i", $numeroVale);
    if (!$eliminarDetallesVale->execute()) {
        throw new Exception('Error al eliminar detalles de vale: ' . $conexion->error);
    }

    //  marcar los bienes asociados como disponibles.
    $actualizarBienes = $conexion->prepare("UPDATE detalles_bien db INNER JOIN detalle_vale dv ON db.ID = dv.Bien_ID SET db.Estado = 'Disponible' WHERE dv.NumeroVale = ?");
    $actualizarBienes->bind_param("i", $numeroVale);
    if (!$actualizarBienes->execute()) {
        throw new Exception('Error al actualizar bienes: ' . $conexion->error);
    }

    // eliminar el préstamo.
    $eliminarPrestamo = $conexion->prepare("DELETE FROM vale WHERE NumeroVale = ?");
    $eliminarPrestamo->bind_param("i", $numeroVale);
    if (!$eliminarPrestamo->execute()) {
        throw new Exception('Error al eliminar préstamo: ' . $conexion->error);
    }

    // Si todo sale bien (ojalá que si en la mayoría de los casos) confirmar los cambios con el commit.
    $conexion->commit();
    echo "<script>alert('El préstamo ha sido eliminado exitosamente.'); window.close();</script>";
} catch (Exception $e) {
    // En caso de error, revierte todos los cambios y acá no sucedió nada.
    $conexion->rollback();
    echo "<script>alert('Hubo un problema al eliminar el préstamo: " . $e->getMessage() . "'); window.close();</script>";
}

$conexion->close();
?>
