<?php
include '../../db.php'; 
include('../control_acceso.php');
verificarAcceso(['Administrador']);
// Obtener el BienID del formulario
$bienID = isset($_GET['bienID']) ? $_GET['bienID'] : '';

// Consulta a la base de datos para obtener los datos relacionados con el BienID
$sql = "SELECT * FROM inventario WHERE BienID = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $bienID);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <title>Edición de Bienes</title>
</head>

<body>
    <div id="edicionBienes" class="container mt-3">
        <h2>Edición de Bienes</h2>
        <p>&nbsp;</p>
        <div class="row">
            <div class="col">
                <h4>Buscar detalles del bien</h4>
                <!-- Formulario para buscar bien -->
                <form id="formBuscarBien" action="gestionBienesSeccionEditarBien.php" method="GET">
                    <div class="mb-3">
                        <label for="bienID" class="form-label">Ingresa el BienID/No. de Inventario</label>
                        <input type="text" class="form-control" id="bienID" name="bienID" required>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar y Editar</button>
                    </div>
                </form>
            </div>
            <div class="container mt-3">
                <h2>Editar campos para el bien con No/ID: <?php echo htmlspecialchars($bienID); ?></h2>

                <!-- Tabla con campos de formulario -->
                <form id="formActualizarInventario" action="gestionBienesProcesarActualizacion.php" method="post">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>BienID</th>
                                <th>Descripción</th>
                                <th>Marca</th>
                                <th>No. de Serie</th>
                                <th>Modelo</th>
                                <th>Ubicación</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                    <td><input type='hidden' name='InventarioID[]' value='".$row["InventarioID"]."'>
                                        <input type='text' class='form-control' name='BienID[]' value='".$row["BienID"]."' readonly></td>
                                    <td><input type='text' class='form-control' name='Descripcion[]' value='".$row["Descripcion"]."'></td>
                                    <td><input type='text' class='form-control' name='Marca[]' value='".$row["Marca"]."'></td>
                                    <td><input type='text' class='form-control' name='No_Serie[]' value='".$row["No_Serie"]."'></td>
                                    <td><input type='text' class='form-control' name='Modelo[]' value='".$row["Modelo"]."'></td>
                                    <td><input type='text' class='form-control' name='Ubicacion[]' value='".$row["Ubicacion"]."'></td>
                                    <td>
                                    <select class='form-control' name='Estado[]'>
                                    <option value='Correcto' ".($row["Estado"] == "Correcto" ? "selected" : "").">Correcto</option>
                                    <option value='Dañado' ".($row["Estado"] == "Dañado" ? "selected" : "").">Dañado</option>
                                    <option value='En Reparación' ".($row["Estado"] == "En Reparación" ? "selected" : "").">En Reparación</option>
                                    <option value='Perdido' ".($row["Estado"] == "Perdido" ? "selected" : "").">Perdido</option>
                                </select>
                                    </td>
                                    <td><button type='submit' class='btn btn-primary'>Confirmar</button></td>
                                  </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9'>No se encontraron datos</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
