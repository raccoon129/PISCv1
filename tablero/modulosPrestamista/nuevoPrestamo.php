<?php

include('../../db.php');
include('../control_acceso.php');
include('../session_check.php');
verificarAcceso(['Prestamista']);
// Generar las fechas
$fechaHoy = date('Y-m-d'); // Fecha de hoy
$fechaDevolucion = date('Y-m-d', strtotime('+1 day'));

// Consulta para obtener el último número de vale
$consultaVale = "SELECT MAX(NumeroVale) AS ultimoVale FROM vale";
$resultadoVale = $conexion->query($consultaVale);
if ($resultadoVale) {
    $filaVale = $resultadoVale->fetch_assoc();
    $ultimoVale = $filaVale['ultimoVale'] + 1; // Incrementar en uno para el nuevo vale
} else {
    $ultimoVale = 1; // En caso de no encontrar vales, empezar en 1 que será así por default cuando se inicien los registros
}


// Preparar y ejecutar la consulta para obtener el nombre completo del usuario en sesión
$consulta = $conexion->prepare("SELECT nombrecompleto FROM usuario WHERE username = ?");
$consulta->bind_param("s", $nombreUsuario); // $nombreUsuario viene de sesion_check.php
$consulta->execute();
$resultado = $consulta->get_result();

if ($fila = $resultado->fetch_assoc()) {
    $nombreCompletoEntrega = $fila['nombrecompleto'];
} else {
    $nombreCompletoEntrega = "No disponible";
}

// Cargar las carreras desde el archivo JSON
$data = json_decode(file_get_contents("../variablesConfiguracion.json"), true);
$carreras = $data['carreras'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Préstamo</title>
    <!-- Enlace a Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Enlace a jQuery para la búsqueda dinámica -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h2>Nuevo Préstamo</h2>
        <br>
        <form action="procesarPrestamoNuevoPrestamo.php" method="post">

            <div class="row">
                <!-- Columna izquierda (Campos de solo lectura) -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="noNuevoVale">Número del nuevo vale a generar: </label>
                        <input type="text" class="form-control" id="noNuevoVale" name="noNuevoVale" value="<?php echo $ultimoVale; ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="entrega">Nombre completo del prestamista que entrega los bienes:</label>
                        <input type="text" class="form-control" id="entrega" name="entrega" value="<?php echo htmlspecialchars($nombreCompletoEntrega); ?>" readonly>
                    </div>
                    <!-- Área, Unidad y Responsable -->
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="area">Área:</label>
                            <input type="text" class="form-control" id="area" name="area" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="unidad">Unidad:</label>
                            <input type="text" class="form-control" id="unidad" name="unidad">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="responsable">Responsable:</label>
                        <input type="text" class="form-control" id="responsable" name="responsable">
                    </div>

                    <!-- Tarjeta para visualizar los bienes agregados -->
                    <div class="card mt-4">
                        <div class="card-header">Listado de bienes a prestar</div>
                        <div class="table-responsive"> <!-- Envuelve tu tabla con .table-responsive -->
                            <table class="table" id="tablaBienes">
                                <thead>
                                    <tr>
                                        <th>Descripción</th>
                                        <th>Bien ID/No. de Inventario</th>
                                        <th>Procedencia</th> <!-- Nueva columna para la procedencia -->
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Los bienes agregados se mostrarán aquí -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Campo oculto para almacenar los IDs de los bienes para poder mandarlos al otro submodulo-->
                    <input type="hidden" name="bienesID" id="bienesID" value="">


                </div>
                <!-- Columna derecha (Campos editables) -->

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="carrera">Procedencia del deudor:</label>
                        <select class="form-control" id="carrera" name="carrera" required>
                            <?php foreach ($carreras as $carrera) : ?>
                                <option value="<?php echo $carrera; ?>"><?php echo $carrera; ?></option>
                            <?php endforeach; ?>
                            <option value="No aplica">No aplica</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="rolDeudor">Rol del Deudor:</label>
                        <select class="form-control" id="rolDeudor" name="rolDeudor" required>
                            <option value="Docente">Docente</option>
                            <option value="Alumno">Alumno</option>
                            <option value="Administrativo">Administrativo</option>
                            <option value="Externo">Externo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="fechaSolicitud">Fecha de Solicitud:</label>
                        <input type="date" class="form-control" id="fechaSolicitud" name="fechaSolicitud" required value="<?php echo $fechaHoy; ?>">
                    </div>
                    <div class="form-group">
                        <label for="fechaDevolucionPrevista">Fecha de Devolución prevista:</label>
                        <input type="date" class="form-control" id="fechaDevolucionPrevista" name="fechaDevolucionPrevista" required value="<?php echo $fechaDevolucion; ?>">
                    </div>
                    <div class="form-group">
                        <label for="nombreDeudor">Nombre del Deudor:</label>
                        <input type="text" class="form-control" id="nombreDeudor" name="nombreDeudor" required>
                    </div>
                    <!-- Poner placeholder-->
                    <div class="form-group">
                        <label for="descripcionBien">Descripción del Bien:</label>
                        <input type="text" class="form-control" id="descripcionBien" name="descripcionBien" placeholder="Escribe la descripción del bien y selecciona un resultado listado.">
                        <!-- Botón para agregar bien al listado -->
                        <button type="button" id="agregarBien" class="btn btn-info mt-2">Agregar Bien</button>
                        <div id="listaBienes"></div> <!-- Contenedor para mostrar resultados de búsqueda dinámica -->
                    </div>
                </div>
            </div>
            <!-- Observaciones fuera de las columnas -->
            <div class="form-group">
                <label>Observaciones:</label>
                <textarea class="form-control" id="observaciones" name="observaciones" rows="3" cols="50"></textarea>
            </div>
            <button id="btnRegistrarPrestamo" type="submit" class="btn btn-primary">Registrar Préstamo</button>
        </form>
    </div>
    <br>
    <br>
    <script src="scriptNuevoPrestamo.js"></script>

    <!-- Opcional: enlace a Bootstrap JS y Popper.js -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</body>

</html>