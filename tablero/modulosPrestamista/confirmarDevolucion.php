<?php

include('../../db.php');
include('../control_acceso.php');
include('../session_check.php');
verificarAcceso(['Prestamista']);
// Verificar si el número de vale está establecido y es un número válido
if (isset($_GET['numeroVale']) && is_numeric($_GET['numeroVale'])) {
    $numeroVale = $_GET['numeroVale'];
} else {

    echo "Número de Vale inválido.";
    exit;
}
// Consulta para obtener información del préstamo, usuario y deudor
$consulta = "SELECT v.NumeroVale, u.nombrecompleto, d.Nombre AS DeudorNombre
FROM vale v
JOIN usuario u ON v.PersonaEntrega = u.ID
JOIN deudor d ON v.Deudor_ID = d.ID
WHERE v.NumeroVale = ?";

// Preparar la consulta
$stmt = $conexion->prepare($consulta);
$stmt->bind_param("i", $numeroVale);
$stmt->execute();
$resultadoInfo = $stmt->get_result();
$info = $resultadoInfo->fetch_assoc();

// Consulta para obtener los bienes asociados al vale
$queryBienes = "SELECT db.ID AS BienID, db.Estado AS EstadoDetalle, i.Descripcion, i.Estado AS EstadoInventario
FROM detalle_vale dv
JOIN detalles_bien db ON dv.Bien_ID = db.ID
JOIN inventario i ON db.ID = i.BienID
WHERE dv.NumeroVale = ?";
$stmt = $conexion->prepare($queryBienes);
$stmt->bind_param("i", $numeroVale);
$stmt->execute();
$resultadoBienes = $stmt->get_result();

$username = $_SESSION['username'];
//Obtener el username
$nombreCompletoUsuario = ""; // Inicializa la variable
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $consultaNombre = "SELECT nombrecompleto FROM usuario WHERE username = ?";
    if ($stmt = $conexion->prepare($consultaNombre)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            $nombreCompletoUsuario = $fila['nombrecompleto']; // Aquí guarda el nombre completo del usuario
        }
        $stmt->close();
    }
}


$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Confirmar Devolución - Submódulo PISC</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-3">
        <h2>Confirmar Devolución</h2>

        <input type="hidden" id="numeroVale" value="<?= htmlspecialchars($numeroVale); ?>">
        <input type="hidden" id="usuarioConfirmaDevolucion" value="<?= htmlspecialchars($nombreCompletoUsuario) ?>">
        <br>
        <div class="alert alert-danger" role="alert">
            Esta acción debe ser realizada por personal autorizado bajo presencia de todos los bienes que serán entregados para su posterior almacenamiento.
        </div>
        <h5>Registrar Devolución para el Vale: <?= htmlspecialchars($numeroVale); ?></h5>
        <p>
            <strong>Usuario que realizó el préstamo:</strong>
            <?= htmlspecialchars($info['nombrecompleto']) ?>
        </p>
        <!-- Suponiendo que ya se ha recuperado el nombre completo del usuario logueado -->
        <p>
            <strong>Prestamista que confirma la devolución:</strong> <?= htmlspecialchars($nombreCompletoUsuario) ?>
        </p>

        <p><strong>Deudor:</strong>
            <?= htmlspecialchars($info['DeudorNombre']) ?>
        </p>

        <table class="table">
            <thead>
                <tr>
                    <th>No.Inventario</th>
                    <th>Descripción</th>
                    <th>Estado Actual</th>
                    <th>Nuevo Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($bien = $resultadoBienes->fetch_assoc()) : ?>
                    <tr>
                        <td><?= htmlspecialchars($bien['BienID']) ?></td>
                        <td><?= htmlspecialchars($bien['Descripcion']) ?></td>
                        <td><?= htmlspecialchars($bien['EstadoInventario']) ?></td>
                        <td>
                            <select name="nuevoEstado[]" class="form-control">
                                <option value="Correcto">Correcto</option>
                                <option value="Dañado">Dañado</option>
                                <option value="En Reparación">En Reparación</option>
                                <option value="Perdido">Perdido</option>
                            </select>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <button id="btnConfirmarDevolucion" class="btn btn-primary">Registrar devolución</button>
        <br>
        <br>
    </div>
    <script type="text/javascript">
        var nombreCompletoUsuario = "<?= htmlspecialchars($nombreCompletoUsuario) ?>";
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="scriptConfirmarDevolucion.js"></script>




</body>

</html>