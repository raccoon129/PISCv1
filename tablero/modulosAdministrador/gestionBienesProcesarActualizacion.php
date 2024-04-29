<?php
//Funcionalidad crítica PISC para la gestion de bienes.
include '../../db.php';
include('../control_acceso.php');
verificarAcceso(['Administrador']);

// Si no es una solicitud POST, terminar el script.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  exit('Excepción! - No se ha generado algún cambio.');
}

$action = $_POST['action'] ?? '';


function bajaBien()
{
  global $conexion;
  $inventarioID = $_POST['inventarioID' ?? null];
  $bienID = $_POST['bienID'] ?? null;
  $observaciones = $_POST['observaciones'] ?? '';
  $fechaBaja = date('Y-m-d H:i:s');
  // Marcar el bien como dado de baja en la base de datos
  $stmt = $conexion->prepare("UPDATE detalles_bien SET Estado = 'Baja' WHERE ID = ?");
  $stmt->bind_param("s", $bienID);
  if ($stmt->execute()) {
    // Actualizar el archivo JSON con la baja
    $jsonFilePath = '../bienesDadosDeBaja.json';
    $jsonData = file_exists($jsonFilePath) ? json_decode(file_get_contents($jsonFilePath), true) : [];
    $jsonData[] = [
      'InventarioID' => $inventarioID,
      'BienID' => $bienID,
      'Observaciones' => $observaciones,
      'FechaBaja' => $fechaBaja
    ];
    file_put_contents($jsonFilePath, json_encode($jsonData, JSON_PRETTY_PRINT));
    $mensaje = "El bien ha sido dado de baja correctamente.";
    $tipo = "success";
  } else {
    $mensaje = "Error al dar de baja el bien: " . $stmt->error;
    $tipo = "error";
  }
  $stmt->close();
  mostrarResultado($mensaje, $tipo);
}

function actualizarBien()
{
  global $conexion;
  // Recoger los datos del formulario
  $bienID = $_POST['BienID'] ?? null;
  $descripcion = $_POST['Descripcion'] ?? null;
  $marca = $_POST['Marca'] ?? null;
  $noSerie = $_POST['No_Serie'] ?? null;
  $modelo = $_POST['Modelo'] ?? null;
  $ubicacion = $_POST['Ubicacion'] ?? null;
  $estado = $_POST['Estado'] ?? null;

  if (!$bienID) {
    mostrarResultado("Error: No se especificó el ID del bien.", "error");
    return;
  }

  // Preparar y ejecutar la consulta para actualizar los detalles del bien
  $sql = "UPDATE inventario SET Descripcion = ?, Marca = ?, No_Serie = ?, Modelo = ?, Ubicacion = ?, Estado = ? WHERE BienID = ?";
  $stmt = $conexion->prepare($sql);
  if ($stmt === false) {
    mostrarResultado("Error al preparar la consulta: " . htmlspecialchars($conexion->error), "error");
    return;
  }

  $stmt->bind_param('sssssss', $descripcion, $marca, $noSerie, $modelo, $ubicacion, $estado, $bienID);
  if ($stmt->execute()) {
    mostrarResultado("El registro ha sido actualizado con éxito.", "success");
  } else {
    mostrarResultado("Error al actualizar el registro: " . htmlspecialchars($stmt->error), "error");
  }

  $stmt->close();
}


function mostrarResultado($mensaje, $tipo)
{
    // Cargar SweetAlert y luego mostrar el resultado
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
    <title>Resultado de la Operación - Funcionalidad PISC</title>
    </head>
    <body>
        <script>
            Swal.fire({
                title: 'Resultado de la operación',
                text: '$mensaje',
                icon: '$tipo',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.close(); // Cerrar la ventana emergente
                    window.opener.location.reload(); // Recargar la página principal
                }
            });
        </script>
    </body>
    </html>";
}

switch ($action) {
    case 'baja':
        bajaBien();
        break;
    case 'actualizar':
        actualizarBien();
        break;
    default:
        mostrarResultado("Acción no especificada", "error");
        break;
}

$conexion->close();
