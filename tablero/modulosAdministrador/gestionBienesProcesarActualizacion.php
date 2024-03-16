<?php
include '../../db.php';
include('../control_acceso.php');
verificarAcceso(['Administrador']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si los datos esperados están presentes
    if (!empty($_POST['InventarioID'])) {
        $inventarioID = $_POST['InventarioID'][0]; // Se asume que solo hay un bien a actualizar
        $bienID = $_POST['BienID'][0];
        $descripcion = $_POST['Descripcion'][0];
        $marca = $_POST['Marca'][0];
        $noSerie = $_POST['No_Serie'][0];
        $modelo = $_POST['Modelo'][0];
        $ubicacion = $_POST['Ubicacion'][0];
        $estado = $_POST['Estado'][0];

        // Preparar la consulta 
        $stmt = $conexion->prepare("UPDATE inventario SET BienID = ?, Descripcion = ?, Marca = ?, No_Serie = ?, Modelo = ?, Ubicacion = ?, Estado = ? WHERE InventarioID = ?");
        $stmt->bind_param("sssssssi", $bienID, $descripcion, $marca, $noSerie, $modelo, $ubicacion, $estado, $inventarioID);

        if ($stmt->execute()) {
            // Cerrar la conexión
            $stmt->close();
            $conexion->close();
            // Mensaje de éxito con SweetAlert
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script>
                    window.onload = function() {
                      Swal.fire({
                        title: 'Realizado',
                        text: 'Registro actualizado con éxito.',
                        icon: 'success',
                        confirmButtonText: 'Ok'
                      }).then((result) => {
                        if (result.isConfirmed) {
                          window.close(); // Cerrar la ventana emergente
                          window.opener.location.reload(); // Recargar la página principal
                        }
                      });
                    };
                  </script>";
        } else {
            // Mensaje de error con SweetAlert
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script>
                    window.onload = function() {
                      Swal.fire({
                        title: 'Excepción',
                        text: 'Error al actualizar el registro.',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                      }).then(() => {
                        window.close(); // Cerrar la ventana emergente
                      });
                    };
                  </script>";
        }
    } else {
        // Mensaje de error con SweetAlert
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
              <script>
                window.onload = function() {
                  Swal.fire({
                    title: 'Excepción',
                    text: 'No se recibieron los datos esperados.',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                  }).then(() => {
                    window.close(); // Cerrar la ventana emergente
                  });
                };
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualización de Bien en Inventario - Submódulo PISC (Funcionalidad)</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>