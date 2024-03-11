<?php

include('../../db.php');
include('../control_acceso.php');
verificarAcceso(['Prestamista']);
$conexion->begin_transaction(); // Comenzar transacción para manejar todo el proceso

try {
    // Recuperar datos del formulario
    $numeroNuevoVale = $_POST['noNuevoVale'];
    $fechaSolicitud = date('Y-m-d H:i:s');
    $bienesID = isset($_POST['bienesID']) ? json_decode($_POST['bienesID'], true) : [];
    $carrera = $_POST['carrera'];
    $fechaDevolucion = $_POST['fechaDevolucionPrevista'];
    $observaciones = $_POST['observaciones'];
    $nombreCompletoEntrega = $_POST['entrega'];
    $nombreDeudor = $_POST['nombreDeudor'];
    $rolDeudor = $_POST['rolDeudor'];

    // Crear ID del deudor basado en su rol
    $idDeudor = "";
    switch ($rolDeudor) {
        case 'Docente':
            $idDeudor = "DOC" . $numeroNuevoVale;
            break;
        case 'Alumno':
            $idDeudor = "ALU" . $numeroNuevoVale;
            break;
        case 'Externo':
            $idDeudor = "EXT" . $numeroNuevoVale;
            break;
    }

    // Insertar en la tabla deudor (solo una vez, ya que el deudor es el mismo para todos los bienes)
    $insertarPersona = "INSERT INTO deudor (ID, Nombre, Rol) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($insertarPersona);
    $stmt->bind_param("sss", $idDeudor, $nombreDeudor, $rolDeudor);
    $stmt->execute();

    // Insertar el vale (sin Bien_ID, ya que esta columna fue eliminada)
    $insertarVale = "INSERT INTO vale (NumeroVale, FechaHoraPrestamo, Deudor_ID, CarreraRecibe, FechaDevolucionPrevista, Observaciones, PersonaEntrega) VALUES (?, ?, ?, ?, ?, ?, (SELECT ID FROM usuario WHERE nombrecompleto = ? LIMIT 1))";
    $stmt = $conexion->prepare($insertarVale);
    $stmt->bind_param("issssss", $numeroNuevoVale, $fechaSolicitud, $idDeudor, $carrera, $fechaDevolucion, $observaciones, $nombreCompletoEntrega);
    $stmt->execute();

    // Insertar cada Bien_ID en la tabla detalle_vale
    foreach ($bienesID as $bienID) {
        $insertarDetalleVale = "INSERT INTO detalle_vale (NumeroVale, Bien_ID) VALUES (?, ?)";
        $stmt = $conexion->prepare($insertarDetalleVale);
        $stmt->bind_param("is", $numeroNuevoVale, $bienID);
        $stmt->execute();

        // Opcional: Actualizar el estado del bien a 'Prestado' en detalles_bien
        $actualizarEstadoBien = "UPDATE detalles_bien SET Estado = 'Prestado' WHERE ID = ?";
        $stmt = $conexion->prepare($actualizarEstadoBien);
        $stmt->bind_param("s", $bienID);
        $stmt->execute();
    }

    $conexion->commit(); // Confirmar la transacción si todo salió bien
    // Mensaje de éxito y redirección
    echo "<script type='text/javascript'>
            alert('El préstamo ha sido registrado exitosamente.');
            window.open('generarVale.php', '_blank'); // Abre generarVale.php en una nueva ventana
          </script>";
} catch (Exception $e) {
    $conexion->rollback(); // Revertir la transacción en caso de error
    // Mensaje de error y redirección
    echo "<script type='text/javascript'>
            alert('Algo ha salido mal durante el proceso de préstamo :( " . addslashes($e->getMessage()) . "');
            window.history.go(-1);
          </script>";
}

$conexion->close(); // Cerrar la conexión a la base de datos
?>
