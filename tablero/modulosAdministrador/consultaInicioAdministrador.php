<?php
// FUNCIONALIDAD PISC - CONSULTANICIOADMINISTRADOR
include('../../db.php'); 
// ...
// Fecha actual
$fechaHoy = date("Y-m-d");

// Consulta para el total de bienes prestados hoy, esta semana, este mes
$totalHoy = mysqli_query($conexion, "SELECT COUNT(*) FROM vale WHERE DATE(FechaHoraPrestamo) = '$fechaHoy'");
$totalSemana = mysqli_query($conexion, "SELECT COUNT(*) FROM vale WHERE WEEK(FechaHoraPrestamo) = WEEK(NOW())");
$totalMes = mysqli_query($conexion, "SELECT COUNT(*) FROM vale WHERE MONTH(FechaHoraPrestamo) = MONTH(NOW())");

// Obtener los resultados
$resultadoHoy = mysqli_fetch_row($totalHoy)[0];
$resultadoSemana = mysqli_fetch_row($totalSemana)[0];
$resultadoMes = mysqli_fetch_row($totalMes)[0];

// Consulta para obtener los bienes prestados recientemente
$consultaRecientes = "SELECT inv.Descripcion AS NombreDelBien, vale.FechaHoraPrestamo, deu.Nombre AS Persona, deu.Rol 
FROM vale
INNER JOIN detalle_vale ON vale.NumeroVale = detalle_vale.NumeroVale
INNER JOIN detalles_bien ON detalle_vale.Bien_ID = detalles_bien.ID
INNER JOIN inventario inv ON detalles_bien.ID = inv.BienID
INNER JOIN deudor deu ON vale.Deudor_ID = deu.ID
WHERE DATE(vale.FechaHoraPrestamo) = '$fechaHoy'
ORDER BY vale.FechaHoraPrestamo DESC";




$resultadoRecientes = mysqli_query($conexion, $consultaRecientes);


// Cerrar la conexión
mysqli_close($conexion);
?>
