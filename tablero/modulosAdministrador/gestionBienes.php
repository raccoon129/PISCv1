<?php

include '../../db.php'; 
include('../control_acceso.php');
verificarAcceso(['Administrador']);
// Al principio de gestionBienes.php
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        echo "<script>alert('Registro eliminado con éxito');</script>";
    } elseif ($_GET['status'] == 'error') {
        echo "<script>alert('Hubo un error al eliminar el registro');</script>";
    }
}

// Consulta SQL para obtener todos los registros sin limitarlos por paginación
$sql = "SELECT i.InventarioID, i.BienID, i.Descripcion, i.Marca, i.No_Serie, i.Modelo, i.Ubicacion, i.Estado, db.Estado 
AS EstadoBien FROM inventario i JOIN detalles_bien db ON i.BienID = db.ID ORDER BY i.InventarioID DESC";
$result = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Bienes - Módulo PISC</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <!-- DataTables CSS que se emplearon debido a problemas de responsividad. Ajuste en estiloDataTablesResponsivas.css -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <!-- Optional JavaScript -->
    <!-- jQuery Library -->
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>

    <link rel="stylesheet" href="../estiloDataTablesResponsivas.css">
</head>

<body>

    <div class="container mt-5">
        <h2>Gestión de bienes</h2>
        <br>
        <button class="btn btn-success" onclick="abrirFormularioNuevoBien()">Insertar Nuevo Bien</button>
        <button class="btn btn-primary" onclick="abrirEditarBien()">Editar Bien</button>

        <button class="btn btn-info" onclick="abrirExportarDatosTabla()">Exportar datos y filtrar</button>
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
                        <th>Acciones</th>
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
                        <td>
                            <button class='btn btn-danger btn-sm' onclick='confirmDelete(" . $row["InventarioID"] . ")'>Eliminar</button>
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
        <br>
        <br>
    </div>

    <script src="scriptGestionBienes.js"></script>


</body>

</html>

<?php
$conexion->close();
?>