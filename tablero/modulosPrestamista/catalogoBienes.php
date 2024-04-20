<?php
include '../../db.php';
include('../control_acceso.php');
verificarAcceso(['Prestamista']);

$sql = "SELECT i.InventarioID, i.BienID, i.Descripcion, i.Marca, i.No_Serie, i.Modelo, i.Ubicacion, i.Estado, db.Estado AS EstadoBien 
        FROM inventario i 
        JOIN detalles_bien db ON i.BienID = db.ID 
        WHERE db.Estado != 'Baja' 
        ORDER BY i.InventarioID DESC";
$result = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Catálogo de Bienes - Módulo PISC</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <link rel="stylesheet" href="../estiloDataTablesResponsivas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>

<body>

    <div class="container mt-5">
        <h2>Catálogo de Bienes</h2>
        <br>
        <div class="tabla-responsiva">
            <table id="tablaBienes" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No. de Inventario</th>
                        <th>Descripción</th>
                        <th>Marca</th>
                        <th>No de Serie</th>
                        <th>Modelo</th>
                        <th>Ubicación</th>
                        <th>Estatus (Inventario)</th>
                        <th>Estatus (Bien)</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . $row["InventarioID"] . "</td>
                                    <td>" . $row["BienID"] . "</td>
                                    <td>" . $row["Descripcion"] . "</td>
                                    <td>" . $row["Marca"] . "</td>
                                    <td>" . $row["No_Serie"] . "</td>
                                    <td>" . $row["Modelo"] . "</td>
                                    <td>" . $row["Ubicacion"] . "</td>
                                    <td>" . $row["Estado"] . "</td>
                                    <td>" . $row["EstadoBien"] . "</td>
                                    <td class='text-center'>
                                    <button class='btn btn-info btn-sm w-100' onclick='verFotoBien(\"" . $row["BienID"] . "\")' title='Ver la foto del bien'>
                                        <i class='fas fa-image'></i> Ver 
                                    </button>
                                    </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10'>No hay datos para mostrar</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="scriptCatalogoBienes.js"></script>

</body>

</html>

<?php
$conexion->close();
?>