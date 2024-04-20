<?php
include '../../db.php';
include('../control_acceso.php');
verificarAcceso(['Administrador']);

$bienID = isset($_GET['bienID']) ? $_GET['bienID'] : '';
$descripcion = '';
$ubicacion = '';
$inventarioID = '';
$observaciones = '';
$message = '';

if ($bienID !== '') {
    // Consulta para obtener la información completa del bien incluyendo el InventarioID
    $sql = "SELECT InventarioID, Descripcion, Ubicacion FROM inventario WHERE BienID = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $bienID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $inventarioID = $row['InventarioID'];
        $descripcion = $row['Descripcion'];
        $ubicacion = $row['Ubicacion'];
    }
    $stmt->close();
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dar de baja bien - Submódulo PISC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Dar de baja el bien con No. de Inventario: <?php echo htmlspecialchars($bienID); ?></h2>
        <div class="alert alert-warning" role="alert">
            Esta operación marcará el estado del bien como dado de baja y no podrá ser revertida.
            <br> No podrá volver a ser prestado ni aparecerá listado en el catálogo de bienes.
            <br>
            Realice esta acción bajo autorización previa.
        </div>
        <p><strong>Descripción:</strong> <?php echo htmlspecialchars($descripcion); ?></p>
        <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($ubicacion); ?></p>
        <p><strong>ID en Inventario:</strong> <?php echo htmlspecialchars($inventarioID); ?></p>

        <form action="gestionBienesProcesarActualizacion.php" method="post">
            <input type="hidden" name="action" value="baja">
            <input type="hidden" name="bienID" value="<?php echo htmlspecialchars($bienID); ?>">
            <input type="hidden" name="inventarioID" value="<?php echo htmlspecialchars($inventarioID); ?>">
            <div class="mb-3">
                <label for="observaciones" class="form-label">Observaciones:</label>
                <textarea class="form-control" id="observaciones" name="observaciones" required></textarea>
            </div>
            <button type="button" onclick="confirmBaja()" class="btn btn-danger">Confirmar Baja</button>
            <a onclick="window.close();" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <script>
        function confirmBaja() {
            Swal.fire({
                title: '¿Está seguro?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirmar dada de baja',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.forms[0].submit(); // Envía el formulario si se confirma
                }
            });
        }
    </script>
</body>
</html>
