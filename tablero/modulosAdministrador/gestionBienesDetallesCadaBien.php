<?php
include '../../db.php'; 
include('../control_acceso.php');
verificarAcceso(['Administrador']);

// Obtener el ID del bien desde la URL
$inventarioID = isset($_GET['id']) ? $_GET['id'] : die('ID del bien no especificado.');

// Consultar la base de datos para obtener los detalles del bien
$sql = "SELECT i.*, db.Estado AS EstadoBien, v.Deudor_ID FROM inventario i 
        LEFT JOIN detalles_bien db ON i.BienID = db.ID 
        LEFT JOIN vale v ON i.BienID = v.Bien_ID 
        WHERE i.InventarioID = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $inventarioID);
$stmt->execute();
$result = $stmt->get_result();
$detalle = $result->fetch_assoc();

if(!$detalle) {
    echo "No se encontró el bien.";
    exit;
}

// Cerrar conexión
$stmt->close();
$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles del Bien</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Detalles del Bien</h2>
        <p><strong>Descripción:</strong> <?php echo $detalle['Descripcion']; ?></p>
        <p><strong>Marca:</strong> <?php echo $detalle['Marca']; ?></p>
        <!-- Añade más detalles según necesites -->
        <?php if($detalle['Deudor_ID']): ?>
            <p><strong>Deudor:</strong> <?php echo $detalle['Deudor_ID']; // Aquí deberías hacer otra consulta para obtener el nombre del deudor basado en el ID ?></p>
        <?php endif; ?>
        <button onclick="window.close();" class="btn btn-primary">Cerrar</button>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
