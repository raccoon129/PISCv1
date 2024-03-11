<?php
// ESTE ES UN MÓDULO PRINCIPAL

include('../../db.php');
include('../control_acceso.php');
verificarAcceso(['Prestamista']);
// consulta  para obtener la información necesaria de los préstamos activos
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
WHERE v.FechaDevolucionReal IS NULL
GROUP BY v.NumeroVale
ORDER BY v.FechaHoraPrestamo DESC;
";


// Ejecutar la consulta
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
    <title>Registrar Devolución - Módulo PISC</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Incluir jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>

    <!-- Incluir DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">

    <!-- Incluir DataTables JS -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <style>
        body {
            background-color: rgba(255, 255, 255, 0);
            /* Cambia el color de fondo a transparente*/
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2>Registrar devolución</h2>
        <h6>Préstamos Activos</h6>

        <table id="prestamosActivos" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Número de Vale</th>
                    <th>Fecha y Hora del Préstamo</th>
                    <th>Deudor</th>
                    <th>Rol</th>
                    <th>Descripción del Bien</th>
                    <th>Fecha de Devolución Prevista</th>
                    <th>Estado</th>
                    <th>Acciones</th> <!-- Elimina la columna "Estado del Préstamo" de aquí -->
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
                            <td>
                                <button class="btn btn-primary registrar-devolucion" data-numero-vale="<?= $prestamo['NumeroVale'] ?>">Registrar Devolución</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="7">No se encontraron préstamos activos.</td>
                    </tr>
                <?php endif; ?>
            </tbody>

        </table>
    </div>
    <br>
    <br>

    <script src="scriptRegistrarDevolucion.js">
    </script>

</body>

</html>