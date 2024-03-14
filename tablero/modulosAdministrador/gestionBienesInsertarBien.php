<?php

include '../../db.php';
include('../control_acceso.php');
verificarAcceso(['Administrador']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $bienID = $_POST['bienID'];
    $descripcion = $_POST['descripcion'];
    $marca = $_POST['marca'];
    $noSerie = $_POST['noSerie'];
    $modelo = $_POST['modelo'];
    $ubicacion = $_POST['ubicacion'];
    $estado = $_POST['estado'];

    // Verificar si el BienID o No_Serie ya existen
    $stmt = $conexion->prepare("SELECT * FROM inventario WHERE BienID = ? OR No_Serie = ?");
    $stmt->bind_param("ss", $bienID, $noSerie);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Si ya existe un registro con el mismo BienID o No_Serie
        echo "<script>alert('Error: El bien con este ID o número de serie ya existe. 
        En el caso que haya dado doble click en el botón guardar, revise la tabla de bienes.'); window.close();</script>";
    } else {
        // Inserta en la tabla detalles_bien
        $stmt = $conexion->prepare("INSERT INTO detalles_bien (ID, Estado) VALUES (?, 'Disponible')");
        $stmt->bind_param("s", $bienID);
        $stmt->execute();

        // Inserta en la tabla inventario
        $stmt = $conexion->prepare("INSERT INTO inventario (BienID, Descripcion, Marca, No_Serie, Modelo, Ubicacion, Estado) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $bienID, $descripcion, $marca, $noSerie, $modelo, $ubicacion, $estado);
        $stmt->execute();

        echo "<script>alert('El bien se ha añadido correctamente. Presione enter.'); window.close(); window.opener.location.reload();</script>";
    }

    // Cierre de la conexión
    $stmt->close();
    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Insertar Nuevo Bien</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

<body>
    <div class="container mt-3">
        <h2>Insertar Nuevo Bien</h2>
        <form action="gestionBienesInsertarBien.php" method="post" id="formInsertarBien">
            <div class="alert alert-warning" role="alert">
                Ingrese con precisión los detalles del bien. Puede realizar correcciones en su opción correspondiente.
                Los bienes no pueden ser eliminados debido a integridad de todos los registros.
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="bienID">No. De Inventario/BienID:</label>
                        <input type="text" class="form-control" id="bienID" name="bienID" required>
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
                            <option>...</option>
                            <option>Lockers</option>
                            <option>CC1</option>
                            <option>CC2</option>
                            <option>CC3</option>
                            <option>CC4</option>
                            <option>CC5</option>
                            <option>Sun Microsystems</option>
                            <option>Área General</option>
                            <option>Recepción</option>
                            <option>Lobby</option>
                            <option>Otro</option>
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
                </div>
            </div>

            <button type="submit" class="btn btn-primary" id="btnGuardar">Guardar</button>
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