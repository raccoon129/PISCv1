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

        echo "<script>alert('El bien se ha añadido correctamente.'); window.close(); window.opener.location.reload();</script>";
    }

    // Cierre de la conexión
    $stmt->close();
    $conexion->close();
}
?>
