<?php

include '../../db.php';
include('../control_acceso.php');
verificarAcceso(['Administrador']);
// Obtener el username del formulario
$username = isset($_GET['username']) ? $_GET['username'] : '';

// Consulta a la base de datos para obtener los datos relacionados con el username
$sql = "SELECT * FROM usuario WHERE username = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edición de Usuario - Submódulo PISC</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body>
    <div class="container mt-4">
        <h2>Edición de Usuario</h2>
        <div class="alert alert-primary" role="alert">
            Los nombres de usuario no se pueden modificar. Recuerde compartir las credenciales actualizadas. 
        </div>
        <!-- Tabla con campos de formulario para editar usuario -->
        <form id="formActualizarUsuario" action="accionesAdmonUsuarios.php" method="post">
            <input type="hidden" name="accion" value="modificar_usuario">
            <div class="container">
                <?php
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                ?>
                    <form id="formActualizarUsuario" action="accionesAdmonUsuarios.php" method="post" class="form">
                        <input type="hidden" name="accion" value="modificar_usuario">
                        <input type="hidden" name="id[]" value="<?php echo $row["ID"]; ?>">

                        <div class="mb-3">
                            <label for="username" class="form-label">Nombre de usuario:</label>
                            <input type="text" class="form-control" name="username[]" value="<?php echo htmlspecialchars($row["username"]); ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="rol" class="form-label">Rol:</label>
                            <select class="form-control" name="rol[]">
                                <option value="Administrador" <?php echo ($row["rol"] == "Administrador" ? "selected" : ""); ?>>Administrador</option>
                                <option value="Prestamista" <?php echo ($row["rol"] == "Prestamista" ? "selected" : ""); ?>>Prestamista</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="nombrecompleto" class="form-label">Nombre completo:</label>
                            <input type="text" class="form-control" name="nombrecompleto[]" value="<?php echo htmlspecialchars($row["nombrecompleto"]); ?>">
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar cambios</button>
                            <button type="button" class="btn btn-danger" onclick="eliminarUsuario(<?php echo $row["ID"]; ?>)"><i class="fas fa-trash"></i> Eliminar usuario</button>
                            <button type="button" class="btn btn-secondary" onclick='restablecerContrasena("<?php echo $row["username"]; ?>")'><i class="fas fa-key"></i> Restablecer contraseña</button>
                        </div>
                    </form>
                <?php
                } else {
                    echo "<strong>No se encontraron datos para el usuario especificado.</strong>";
                }
                ?>
            </div>

        </form>
    </div>
    <script src="scriptEditarUsuarioAdmonUsuarios.js"></script>

</body>

</html>