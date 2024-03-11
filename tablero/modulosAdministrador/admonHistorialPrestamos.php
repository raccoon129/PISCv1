<?php
include '../../db.php'; 
include('../control_acceso.php');
verificarAcceso(['Administrador']);
// consulta para obtener la información necesaria de los préstamos activos
$query = "SELECT v.NumeroVale, v.FechaHoraPrestamo, d.Nombre AS Deudor, d.Rol, 
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
GROUP BY v.NumeroVale
ORDER BY v.FechaHoraPrestamo DESC;
";

$result = $conexion->query($query);

// Verificar si la consulta devuelve filas
if ($result->num_rows > 0) {
    // Inicializar un arreglo para almacenar los resultados
    $prestamosActivos = [];
    while ($row = $result->fetch_assoc()) {
        $row['EstadoPrestamo'] = $row['FechaDevolucionReal'] ? 'Préstamo finalizado' : 'Préstamo activo';
        $prestamosActivos[] = $row;
    }
} else {
    echo "No se encontraron préstamos activos.";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Devolución</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Incluir jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>

    <!-- Incluir DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">

    <!-- Incluir DataTables JS -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color: rgba(255, 255, 255, 0);
            /* Cambia el color de fondo a transparente*/
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2>Administrar Historial de préstamos</h2>
        <table id="prestamosActivos" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Número de Vale</th>
                    <th>Fecha y Hora del Préstamo</th>
                    <th>Deudor</th>
                    <th>Rol</th>
                    <th>Descripción del Bien</th>
                    <th>Fecha de Devolución Prevista</th>
                    <th>Fecha de Devolución Real</th>
                    <th>Estado del Préstamo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($prestamosActivos)) : ?>
                    <?php foreach ($prestamosActivos as $prestamo) : ?>
                        <tr>
                            <td><?= htmlspecialchars($prestamo['NumeroVale']) ?></td>
                            <td><?= htmlspecialchars($prestamo['FechaHoraPrestamo']) ?></td>
                            <td><?= htmlspecialchars($prestamo['Deudor']) ?></td>
                            <td><?= htmlspecialchars($prestamo['Rol']) ?></td>
                            <td><?= htmlspecialchars($prestamo['DescripcionBienes']) ?></td>
                            <td><?= htmlspecialchars($prestamo['FechaDevolucionPrevista']) ?></td>
                            <td><?= $prestamo['FechaDevolucionReal'] ? htmlspecialchars($prestamo['FechaDevolucionReal']) : 'No se ha devuelto' ?></td>
                            <td><?= htmlspecialchars($prestamo['EstadoPrestamo']) ?></td>
                            <td>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="noNuevoVale" value="<?= $prestamo['NumeroVale'] ?>">
                                    <button onclick="abrirGenerarVale('<?= $prestamo['NumeroVale'] ?>')" class="btn btn-success"><i class="fas fa-file-alt"></i> Recuperar Vale</button>
                                    <button onclick="eliminarPrestamoFormulario(<?= $prestamo['NumeroVale'] ?>);" class="btn btn-danger"><i class="fas fa-trash"></i> Eliminar registro</button>
                                </form>
                                <!-- Botón para ver detalles  -->
                                <button onclick="abrirDetallesPrestamo('<?= $prestamo['NumeroVale'] ?>')" class="btn btn-info"><i class="fas fa-eye"></i> Detalles</button>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="9">No se encontraron préstamos activos.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <br>
    <br>
    <script src="scriptAdmonHistorialPrestamos.js"></script>

</body>

</html>