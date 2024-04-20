<?php

include '../../db.php';
include('../control_acceso.php');
verificarAcceso(['Administrador']);


// Consulta SQL para obtener todos los registros sin limitarlos por paginación
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>

<body>

    <div class="container mt-5">
        <h2>Gestión de bienes</h2>
        <br>
        <button class="btn btn-success" onclick="abrirFormularioNuevoBien()">Insertar Nuevo Bien</button>
        <button class="btn btn-primary" onclick="abrirEditarBien()">Búsqueda y edición de Bien</button>
        <button class="btn btn-info" onclick="abrirExportarDatosTabla()">Exportar datos y filtrar</button>
        <button class="btn btn-primary" onclick="abrirAuditarBajas()">Auditar bajas</button>
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
                                <td class='text-center'>
                                <div class='btn-group-vertical w-100'>
                                    <button class='btn btn-info btn-sm w-100' onclick='verFoto(\"" . $row["BienID"] . "\")' title='Ver la foto del bien'>
                                        <i class='fas fa-image'></i> Ver 
                                    </button>
                                    <button class='btn btn-primary btn-sm w-100' onclick='window.open(\"gestionBienesEditarBien.php?bienID=" . $row["BienID"] . "\", \"_blank\", \"width=1250,height=600\")' title='Editar los detalles del bien'>
                                        <i class='fas fa-edit'></i> Editar
                                    </button>
                                </div>
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