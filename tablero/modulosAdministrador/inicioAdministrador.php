<?php
include '../../db.php'; 
include('../control_acceso.php');
verificarAcceso(['Administrador']);

// Consultas para obtener datos relevantes
$totalBienesQuery = "SELECT COUNT(*) AS total FROM detalles_bien";
$totalPrestamosQuery = "SELECT COUNT(*) AS total FROM vale WHERE FechaDevolucionReal IS NULL"; // Préstamos activos
$totalDeudoresQuery = "SELECT COUNT(DISTINCT Deudor_ID) AS total FROM vale"; // Total de deudores únicos
$bienesPrestadosQuery = "SELECT COUNT(*) AS total FROM detalles_bien WHERE Estado = 'Prestado'";
$ultimosPrestamosQuery = "SELECT v.NumeroVale, d.Nombre, v.FechaHoraPrestamo FROM vale v JOIN deudor d ON v.Deudor_ID = d.ID ORDER BY v.FechaHoraPrestamo DESC LIMIT 5";

// Ejecutar consultas
$totalBienesResult = $conexion->query($totalBienesQuery)->fetch_assoc();
$totalPrestamosResult = $conexion->query($totalPrestamosQuery)->fetch_assoc();
$totalDeudoresResult = $conexion->query($totalDeudoresQuery)->fetch_assoc();
$bienesPrestadosResult = $conexion->query($bienesPrestadosQuery)->fetch_assoc();
$ultimosPrestamosResult = $conexion->query($ultimosPrestamosQuery);

// Cerrar conexión
$conexion->close();
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo Inicio - Prestamista PISC</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <link rel="stylesheet" href="estiloInicioAdministrador.css">
</head>

<body>
    <div class="container">
        <br>
        <br>
        <h2>Resumen para el administrador</h2>
        <br>
        <div class="row">
            <div class="col-md-3">
                <div class="alert alert-info">
                    <strong>Total de Bienes:</strong> <?= $totalBienesResult['total'] ?>
                </div>
            </div>
            <div class="col-md-3">
                <div class="alert alert-success">
                    <strong>Préstamos Activos:</strong> <?= $totalPrestamosResult['total'] ?>
                </div>
            </div>
            <div class="col-md-3">
                <div class="alert alert-warning">
                    <strong>Total de Deudores:</strong> <?= $totalDeudoresResult['total'] ?>
                </div>
            </div>
            <div class="col-md-3">
                <div class="alert alert-danger">
                    <strong>Bienes Prestados:</strong> <?= $bienesPrestadosResult['total'] ?>
                </div>
            </div>
        </div>
        

        <?php include('consultaInicioAdministrador.php'); ?>

        <div class="row">
            <!-- Columna para la tarjeta de préstamos de hoy -->
            <div class="col-sm">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-calendar-day card-icon"></i> <!-- Icono de hoy -->
                        Cantidad de préstamos realizados
                    </div>
                    <div class="card-body">
                        <p class="card-text">Hoy: <?php echo $resultadoHoy; ?></p>
                        <p class="card-text">Esta semana: <?php echo $resultadoSemana; ?></p>
                        <p class="card-text">Este mes: <?php echo $resultadoMes; ?></p>
                    </div>
                </div>
            </div>

            <!-- Columna para la tarjeta de préstamos del día -->
            <div class="col-sm">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-arrow-up-right-from-square card-icon"></i> <!-- Icono de préstamos del día -->
                        Préstamos del día
                    </div>
                    <div class="card-body">
                        
                        <table id="tablaPrestamos" class="table">
                            <thead>
                                <tr>
                                    <th>Nombre del Bien</th>
                                    <th>Fecha y Hora del Préstamo</th>
                                    <th>Deudor</th>
                                    <th>Rol</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($resultadoRecientes)) : ?>
                                    <tr>
                                        <td><?php echo $row['NombreDelBien']; ?></td>
                                        <td><?php echo $row['FechaHoraPrestamo']; ?></td>
                                        <td><?php echo $row['Persona']; ?></td>
                                        <td><?php echo $row['Rol']; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <br>
    </div>


    <!-- Bootstrap JS y dependencias -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>