<?php
include '../../db.php';
include('../control_acceso.php');
verificarAcceso(['Administrador']);

// Obtener el BienID del formulario
$bienID = isset($_GET['bienID']) ? $_GET['bienID'] : '';

// Flag para saber si se encontraron resultados
$encontrado = false;

if ($bienID !== '') {
    // Consulta a la base de datos para obtener los datos relacionados con el BienID
    $sql = "SELECT * FROM inventario WHERE BienID = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $bienID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $encontrado = true;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Edición de Bienes - Módulo PISC</title>
</head>

<body>
    <div class="container mt-3">
        <h2>Edición de Bienes</h2>
        <br>
        <div class="accordion" id="accordionExample">
            <!-- Panel de búsqueda -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button <?php echo $encontrado ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="<?php echo $encontrado ? 'false' : 'true'; ?>" aria-controls="collapseOne">
                        Buscar detalles del bien
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse <?php echo $encontrado ? '' : 'show'; ?>" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <form id="formBuscarBien" action="gestionBienesEditarBien.php" method="GET">
                            <div class="mb-3">
                                <label for="bienID" class="form-label">Ingresa el No. de Inventario</label>
                                <input type="text" class="form-control" id="bienID" name="bienID" required>
                            </div>
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar y Editar</button>
                            </div>
                        </form>
                        
                    </div>
                </div>
            </div>
            
            <!-- Panel de edición -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button <?php echo $encontrado ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="<?php echo $encontrado ? 'true' : 'false'; ?>" aria-controls="collapseTwo">
                        Editar campos del bien
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse <?php echo $encontrado ? 'show' : ''; ?>" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <div class="alert alert-info" role="alert">
                            Recuerde que el No. de Inventario no puede ser modificado por cuestiones de integridad.
                        </div>
                        <?php if ($encontrado) : ?>
                            <h4>Editar campos para el bien con No. de inventario: <?php echo htmlspecialchars($bienID); ?></h4>
                            <!-- Tabla con campos de formulario -->
                            <form id="formActualizarInventario" action="gestionBienesProcesarActualizacion.php" method="post">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>No. de</th>
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
            <td><input type='hidden' name='InventarioID[]' value='" . $row["InventarioID"] . "'>
                <input type='text' class='form-control' name='BienID[]' value='" . $row["BienID"] . "' readonly></td>
            <td><input type='text' class='form-control' name='Descripcion[]' value='" . $row["Descripcion"] . "'></td>
            <td><input type='text' class='form-control' name='Marca[]' value='" . $row["Marca"] . "'></td>
            <td><input type='text' class='form-control' name='No_Serie[]' value='" . $row["No_Serie"] . "'></td>
            <td><input type='text' class='form-control' name='Modelo[]' value='" . $row["Modelo"] . "'></td>
            <td>
                <select class='form-control' name='Ubicacion[]'>
                    <option value='CC1' " . ($row["Ubicacion"] == "CC1" ? "selected" : "") . ">CC1</option>
                    <option value='CC2' " . ($row["Ubicacion"] == "CC2" ? "selected" : "") . ">CC2</option>
                    <option value='CC3' " . ($row["Ubicacion"] == "CC3" ? "selected" : "") . ">CC3</option>
                    <option value='CC4' " . ($row["Ubicacion"] == "CC4" ? "selected" : "") . ">CC4</option>
                    <option value='CC5' " . ($row["Ubicacion"] == "CC5" ? "selected" : "") . ">CC5</option>
                    <option value='Área General' " . ($row["Ubicacion"] == "Área General" ? "selected" : "") . ">Área General</option>
                    <option value='Área General Recepción' " . ($row["Ubicacion"] == "Área General Recepción" ? "selected" : "") . ">Área General Recepción</option>
                    <option value='Lobby' " . ($row["Ubicacion"] == "Lobby" ? "selected" : "") . ">Lobby</option>
                    <option value='Sun Microsystems' " . ($row["Ubicacion"] == "Sun Microsystems" ? "selected" : "") . ">Sun Microsystems</option>
                    <option value='Data Center' " . ($row["Ubicacion"] == "Data Center" ? "selected" : "") . ">Data Center</option>
                </select>
            </td>
            <td>
                <select class='form-control' name='Estado[]'>
                    <option value='Correcto' " . ($row["Estado"] == "Correcto" ? "selected" : "") . ">Correcto</option>
                    <option value='Dañado' " . ($row["Estado"] == "Dañado" ? "selected" : "") . ">Dañado</option>
                    <option value='En Reparación' " . ($row["Estado"] == "En Reparación" ? "selected" : "") . ">En Reparación</option>
                    <option value='Perdido' " . ($row["Estado"] == "Perdido" ? "selected" : "") . ">Perdido</option>
                </select>
            </td>
            <td><button type='submit' class='btn btn-primary'>Confirmar</button></td>
            </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='8'><strong>No se ha seleccionado un BienID/No. de inventario o no existen datos referentes.</strong></td></tr>";
                                        }
                                        ?>
                                    </tbody>

                                </table>
                            </form>

                        <?php else : ?>
                            <p>No se han encontrado detalles para el bien solicitado.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>