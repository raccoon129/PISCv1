<?php
include '../../db.php';
include('../control_acceso.php');
verificarAcceso(['Administrador']);

$mensaje = '';
$datosCorrectos = true;
$datosVistaPrevia = [];
$contadorID = 1;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fileCSV'])) {
    if (isset($_POST['comprobar'])) {
        $archivoCSV = $_FILES['fileCSV'];
        $consultaMaxId = $conexion->query("SELECT MAX(InventarioID) AS maximo FROM inventario");
        $resultadoMaxId = $consultaMaxId->fetch_assoc();
        $inventarioIDActual = $resultadoMaxId['maximo'] ?? 0;

        if ($archivoCSV['error'] != UPLOAD_ERR_OK) {
            $mensaje = "Error al cargar el archivo.";
            $datosCorrectos = false;
        } else {
            $handle = fopen($archivoCSV['tmp_name'], 'r');
            if ($handle === false) {
                $mensaje = "Error al abrir el archivo.";
                $datosCorrectos = false;
            } else {
                $encabezados = fgetcsv($handle, 1000, ",");
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if (count($data) != 7 || !in_array(trim($data[6]), ['Correcto', 'Dañado', 'En Reparación', 'Perdido'])) {
                        $datosCorrectos = false;
                        $mensaje = "Estructura del archivo errónea o los datos en la columna estado del bien no son válidos.";
                        break;
                    }
                    $datosVistaPrevia[] = $data;
                }
                fclose($handle);
                if ($datosCorrectos) {
                    $mensaje = "Archivo con estructura correcta. Listo para importar, los datos se han preparado exitosamente..";
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volcado Datos de Bienes desde CSV - Submodulo PISC</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container mt-3">
        <h2>Volcado de Datos de Bienes desde CSV</h2>
        <div>
            <?php if (!empty($mensaje)) : ?>
                <div class="alert <?php echo $datosCorrectos ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>
        </div>
        <form action="gestionBienesVolcarDatosBienes.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="fileCSV" class="form-label">Archivo CSV:</label>
                <input type="file" class="form-control" id="fileCSV" name="fileCSV" accept=".csv" required>
            </div>
            <button type="submit" class="btn btn-primary" name="comprobar">Comprobar</button>
        </form>
        <?php if ($datosCorrectos && !empty($datosVistaPrevia)) : ?>

            <hr>
            <div class="alert alert-warning" role="alert">
                Verifique si sus datos son correctos antes de continuar el proceso.
            </div>
            <button type="button" class="btn btn-success" id="btnImportar">Importar</button>
            <br>
            <div id="progressBarContainer" style="display: none;">
                <br>
                <p>Realizando la transacción. ¡No cierre esta ventana!</p>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="progressBar">0%</div>
                </div>
            </div>
            <table class="table table-striped mt-4">
                <thead>
                    <tr>
                        <th>Nuevo No</th>
                        <th>No. de Inventario</th>
                        <th>Descripción</th>
                        <th>Marca</th>
                        <th>No de Serie</th>
                        <th>Modelo</th>
                        <th>Ubicación</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($datosVistaPrevia as $fila) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($inventarioIDActual + $contadorID++); ?></td>
                            <td><?php echo htmlspecialchars($fila[0]); ?></td>
                            <td><?php echo htmlspecialchars($fila[1]); ?></td>
                            <td><?php echo htmlspecialchars($fila[2]); ?></td>
                            <td><?php echo htmlspecialchars($fila[3]); ?></td>
                            <td><?php echo htmlspecialchars($fila[4]); ?></td>
                            <td><?php echo htmlspecialchars($fila[5]); ?></td>
                            <td><?php echo htmlspecialchars($fila[6]); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <!-- Acordeón para mostrar el tutorial sobre cómo rellenar el CSV -->
        <div class="accordion mt-3" id="accordionExample">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        ¿Cómo rellenar el CSV?
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <p>Consulte la imagen a continuación para entender cómo debe estructurarse el archivo CSV que desea cargar.</p>
                        <div class="alert alert-warning" role="alert">
                            Respete los encabezados en el orden tal cual como se muestran. No ingrese una columna enumerando los bienes ya que este dato se genera automáticamente.
                            Para más información, visite el manual de usuario.
                        </div>
                        <img src="../../img/RepresentacionGeneradoCSV.png" alt="Tutorial de cómo rellenar el archivo CSV" class="rounded mx-auto d-block">
                    </div>
                </div>
            </div>
        </div>
        <br>
    </div>

    <script>
        document.getElementById('btnImportar').addEventListener('click', function() {
            Swal.fire({
                title: '¿Está seguro de que desea importar los datos?',
                text: "Esta acción no se puede deshacer. Los bienes añadidos no pueden ser eliminados, únicamente editados individualmente.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Es correcto, importar ahora'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('progressBarContainer').style.display = 'block'; // Muestra la barra de progreso

                    var datosParaImportar = <?php echo json_encode($datosVistaPrevia); ?>;
                    console.log("Datos enviados al servidor:", datosParaImportar);

                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "consultaGestionBienesVolcarDatosBienes.php", true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onprogress = function(e) {
                        if (e.lengthComputable) {
                            var percentComplete = (e.loaded / e.total) * 100;
                            document.getElementById('progressBar').style.width = percentComplete + '%';
                            document.getElementById('progressBar').textContent = Math.round(percentComplete) + '%';
                        }
                    };
                    xhr.onload = function() {
                        if (xhr.status == 200) {
                            console.log("Respuesta del servidor:", xhr.responseText);
                            var response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                Swal.fire('Completado', 'La importación se completó correctamente.', 'success');
                            } else {
                                Swal.fire('Error', 'Hubo un problema con la importación: ' + response.error, 'error');
                            }
                            window.onunload = function() {
                                window.opener.location.reload();
                            };
                            setTimeout(function() {
                                window.close();
                            }, 3000);
                        } else {
                            Swal.fire('Error', 'Hubo un problema con la importación.', 'error');
                        }
                    };
                    xhr.send("importar=true&datos=" + encodeURIComponent(JSON.stringify(datosParaImportar)));
                }
            });
        });
    </script>
</body>

</html>