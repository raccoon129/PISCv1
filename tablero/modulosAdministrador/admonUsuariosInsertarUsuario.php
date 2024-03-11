<?php
include '../../db.php';
include('../control_acceso.php');
verificarAcceso(['Administrador']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Nuevo Usuario - Submódulo PISC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h2>Añadir Nuevo Usuario</h2>
        <form action="accionesAdmonUsuarios.php" method="POST" id="formInsertarUsuario">

            <input type="hidden" name="accion" value="nuevo_usuario">

            <div class="mb-3">
                <label for="username" class="form-label">Nombre de Usuario</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="rol" class="form-label">Rol</label>
                <select class="form-select" id="rol" name="rol" required>
                    <option value="Administrador">Administrador</option>
                    <option value="Prestamista">Prestamista</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="nombrecompleto" class="form-label">Nombre Completo</label>
                <input type="text" class="form-control" id="nombrecompleto" name="nombrecompleto" required>
            </div>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="#" onclick="window.close();" class="btn btn-secondary">Cancelar</a>

        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../validarCamposSinEspacios.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            validarCampoSinEspacios('formInsertarUsuario', 'username');
        });
    </script>
</body>

</html>