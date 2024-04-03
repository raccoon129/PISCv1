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

    if ($row = $result->fetch_assoc()) {
        $bienID = $row['BienID'];
        $descripcion = $row['Descripcion'];
        $marca = $row['Marca'];
        $noSerie = $row['No_Serie'];
        $modelo = $row['Modelo'];
        $ubicacion = $row['Ubicacion'];
        $estado = $row['Estado'];
    }

    if ($result->num_rows > 0) {
        $encontrado = true;
    }
}

// Cargar configuraciones desde JSON
$rutaJSONConfiguracion = '../variablesConfiguracion.json';
$configDataJSONConfiguracion = file_exists($rutaJSONConfiguracion) ? json_decode(file_get_contents($rutaJSONConfiguracion), true) : [];

// Extraer ubicaciones del archivo JSON
$ubicaciones = isset($configDataJSONConfiguracion['ubicaciones']) ? $configDataJSONConfiguracion['ubicaciones'] : [];


$jsonFilePath = '../bibliotecaFotosBienes.json';
if (file_exists($jsonFilePath)) {
    $fotosBienesJson = file_get_contents($jsonFilePath);
    $fotosBienes = json_decode($fotosBienesJson, true);
    $imagenRuta = array_key_exists($bienID, $fotosBienes) ? $fotosBienes[$bienID] : '';
} else {
    $fotosBienes = [];
    $imagenRuta = '';
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="scriptGestionBienesEditarBien.js"></script>
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
                            <!-- Formulario de edición -->
                            <form id="formActualizarInventario" action="gestionBienesProcesarActualizacion.php" method="post">
                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- Columna izquierda -->
                                        <div class="mb-3">
                                            <label for="BienID" class="form-label">No. de Inventario:</label>
                                            <input type="text" class="form-control" name="BienID" value="<?php echo $bienID; ?>" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label for="Descripcion" class="form-label">Descripción:</label>
                                            <input type="text" class="form-control" name="Descripcion" value="<?php echo $descripcion  ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label for="Marca" class="form-label">Marca:</label>
                                            <input type="text" class="form-control" name="Marca" value="<?php echo $marca ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Columna derecha -->
                                        <div class="mb-3">
                                            <label for="No_Serie" class="form-label">No. de Serie:</label>
                                            <input type="text" class="form-control" name="No_Serie" value="<?php echo $noSerie ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label for="Modelo" class="form-label">Modelo:</label>
                                            <input type="text" class="form-control" name="Modelo" value="<?php echo $modelo ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label for="Ubicacion" class="form-label">Ubicación:</label>
                                            <select class="form-control" name="Ubicacion">
                                                <?php foreach ($ubicaciones as $ubicacion) : ?>
                                                    <option value="<?php echo htmlspecialchars($ubicacion); ?>" <?php echo ($row["Ubicacion"] == $ubicacion) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($ubicacion); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="Estado" class="form-label">Estado:</label>
                                    <select class='form-control' name='Estado'>
                                        <option value='Correcto' " . ($row[" Estado"]=="Correcto" ? "selected" : "" ) . ">Correcto</option>
                                        <option value='Dañado' " . ($row["Estado"]=="Dañado" ? "selected" : "" ) . ">Dañado</option>
                                        <option value='En Reparación' " . ($row["Estado"]=="En Reparación" ? "selected" : "" ) . ">En Reparación</option>
                                        <option value='Perdido' " . ($row["Estado"]=="Perdido" ? "selected" : "" ) . ">Perdido</option>
                                    </select>                    
                                </div>

                                <button type=" submit" class="btn btn-primary">Confirmar y guardar cambios</button>
                            </form>
                        <?php else : ?>
                            <p>No se han encontrado detalles para el bien solicitado.</p>
                        <?php endif; ?>

                    </div>
                    <!-- CONTENEDOR PARA MOSTRAR LA IMAGEN DEL BIEN -->
                    <div class="container mt-3">
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Vista previa de la imagen del bien</h4>
                                <!-- Mostrar la miniatura de la imagen si existe -->
                                <?php if (!empty($imagenRuta)) : ?>
                                    <img src="<?php echo htmlspecialchars($imagenRuta); ?>" alt="Foto del Bien" class="img-thumbnail mb-3">
                                <?php else : ?>
                                    <div class="alert alert-warning" role="alert">
                                        No existe una imagen para este bien. Cargue una en el botón a continuación.
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <!-- Formulario para cargar y reemplazar la imagen -->
                                <form id="formCargarImagen" action="procesarImagenes.php" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="bienID" value="<?php echo htmlspecialchars($bienID); ?>">
                                    <div class="mb-3">
                                        <label for="imagenBien" class="form-label">Cargar/Reemplazar imagen:</label>
                                        <input type="file" class="form-control" id="imagenBien" name="imagenBien">
                                        <small class="form-text text-muted">Selecciona una única imágen en .jpg o .png. Existe compresión al momento de cargarse.</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary" id="uploadButton" disabled>
                                        Cargar Imagen
                                        <span id="loader" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                                    </button>
                                </form>


                            </div>
                        </div>
                    </div>
                    <br>
                    <br>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>