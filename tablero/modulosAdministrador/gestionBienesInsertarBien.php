<?php

include '../../db.php';
include('../control_acceso.php');
verificarAcceso(['Administrador']);
// Leer las ubicaciones desde el archivo JSON
$configuracion = json_decode(file_get_contents('../variablesConfiguracion.json'), true);
$ubicaciones = $configuracion['ubicaciones'];

?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Insertar Nuevo Bien - Submódulo PISC</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

</head>

<body>
    <div class="container mt-3">
        <h2>Insertar Nuevo Bien</h2>
        <form action="consultaGestionBienesInsertarBien.php" method="post" id="formInsertarBien" enctype="multipart/form-data">
            <div class="alert alert-warning" role="alert">
                Ingrese con precisión los detalles del bien. Considere los siguientes puntos:
                <li>Puede realizar correcciones posteriores en su registro correspondiente.</li>
                <li>Los bienes no pueden ser eliminados debido a integridad de todos los registros.</li>
                <li>El No. De Inventario no puede ser modificado.</li>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="bienID">No. De Inventario/BienID:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="bienID" name="bienID" required>
                            <div class="input-group-append">
                                <span class="input-group-text" id="bienIDIcon"></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción:</label>
                        <input type="text" class="form-control" id="descripcion" name="descripcion" required>
                    </div>
                    <div class="form-group">
                        <label for="marca">Marca:</label>
                        <input type="text" class="form-control" id="marca" name="marca" required>
                    </div>
                    <div class="form-group">
                        <label for="noSerie">No. de Serie:</label>
                        <input type="text" class="form-control" id="noSerie" name="noSerie" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="modelo">Modelo:</label>
                        <input type="text" class="form-control" id="modelo" name="modelo" required>
                    </div>
                    <div class="form-group">
                        <label for="ubicacion">Ubicación:</label>
                        <select class="form-control" id="ubicacion" name="ubicacion" required>
                            <option value="">Seleccione una opción</option>
                            <?php foreach ($ubicaciones as $ubicacion) : ?>
                                <option value="<?= htmlspecialchars($ubicacion) ?>"><?= htmlspecialchars($ubicacion) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado:</label>
                        <select class="form-control" id="estado" name="estado" required>
                            <option>Correcto</option>
                            <option>Dañado</option>
                            <option>En Reparación</option>
                            <option>Perdido</option>
                        </select>
                    </div>
                    <!-- Aquí irá la subida de imagenes -->
                    <div class="form-group">
                        <label for="imagenBien">Imagen del Bien:</label>
                        <input type="file" class="form-control-file" id="imagenBien" name="imagenBien" onchange="previsualizarImagen(this)">
                        <small class="form-text text-muted">Selecciona una única imágen en .jpg o .png. Se comprimen al momento de cargarse.</small>
                        <img id="previsualizacion" src="#" alt="Previsualización de la imagen" class="img-thumbnail" style="display: none; width: 200px;" />
                    </div>

                </div>
            </div>

            <button type="submit" class="btn btn-primary" id="btnGuardar">
                Guardar
                <span id="loader" style="display:none;" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </button>

            <button type="button" class="btn btn-secondary" onclick="window.close();">Cerrar</button>

        </form>
    </div>

    <br>
    </form>
    </div>

    <script src="scriptGestionBienesInsertarBien.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>