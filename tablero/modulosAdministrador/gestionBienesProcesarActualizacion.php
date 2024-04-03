<?php
include '../../db.php';
include('../control_acceso.php');
verificarAcceso(['Administrador']);

// Si no es una solicitud POST, terminar el script.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  exit('Excepcion. Algo ha pasado :( - No se ha generado algún cambio.');
}

// Extraer y validar los datos enviados desde el formulario
$bienID = $_POST['BienID'] ?? null;
$descripcion = $_POST['Descripcion'] ?? null;
$marca = $_POST['Marca'] ?? null;
$noSerie = $_POST['No_Serie'] ?? null;
$modelo = $_POST['Modelo'] ?? null;
$ubicacion = $_POST['Ubicacion'] ?? null;
$estado = $_POST['Estado'] ?? null;

$stmt = $conexion->prepare("UPDATE inventario SET Descripcion = ?, Marca = ?, No_Serie = ?, Modelo = ?, Ubicacion = ?, Estado = ? WHERE BienID = ?");

// Verificar si la declaración preparada se creó correctamente
if ($stmt === false) {
  $mensaje = "Error al preparar la consulta: " . htmlspecialchars($conexion->error);
  $tipo = "error";
} else {
  // Vincular los parámetros a la declaración preparada
  $stmt->bind_param('sssssss', $descripcion, $marca, $noSerie, $modelo, $ubicacion, $estado, $bienID);

  // Intentar ejecutar la declaración preparada
  if ($stmt->execute()) {
    $mensaje = "El registro ha sido actualizado con éxito.";
    $tipo = "success";
  } else {
    $mensaje = "Error al actualizar el registro: " . htmlspecialchars($stmt->error);
    $tipo = "error";
  }

  // Cerrar la declaración preparada
  $stmt->close();
}

// Cerrar la conexión a la base de datos
$conexion->close();

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Actualización de detalles del Bien - Submódulo PISC (Funcionalidad)</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

  <script>
    Swal.fire({
      title: 'Actualización del bien',
      text: '<?php echo $mensaje; ?>',
      icon: '<?php echo $tipo; ?>',
      confirmButtonText: 'Aceptar'
    }).then((result) => {
      if (result.isConfirmed) {
        window.close(); // Cerrar la ventana emergente
        window.opener.location.reload(); // Recargar la página principal
      }
    });
  </script>

</body>

</html>