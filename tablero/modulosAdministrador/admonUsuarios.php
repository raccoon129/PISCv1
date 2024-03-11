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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script src="js/jquery.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <title>Administración de Usuarios - Submódulo PISC</title>
    <style>
        body {
            background-color: rgba(255, 255, 255, 0);
        }
    </style>
</head>

<body>
    <br>
    <div id="administracionUsuarios" class="container mt-4">
        <h2>Administración de Usuarios</h2>
        <p>&nbsp;</p>
        <div class="row">
            <div class="col-md-4">
                <!-- Formulario para añadir usuario -->
                <div class="card mt-1">
                    <div class="card-header">Acciones</div>
                    <div class="card-body d-flex justify-content-center">
                        <button type="button" onclick="abrirFormularioInsertarUsuario();" class="btn btn-secondary">
                            <i class="fas fa-user-plus"></i> Añadir Usuario
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <h4>Lista de Usuarios</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre de Usuario</th>
                            <th>Rol</th>
                            <th>Nombre completo</th>
                            <th>Fecha de creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="listaUsuarios">
                        <?php
                        $sql = "SELECT * FROM usuario ORDER BY id DESC";
                        if (isset($_GET['username'])) {
                            $username = $_GET['username'];
                            $sql .= " WHERE username LIKE '%" . $conexion->real_escape_string($username) . "%'";
                        }
                        $result = $conexion->query($sql);

                        if ($result = $conexion->query($sql)) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>" . htmlspecialchars($row["username"]) . "</td>
                                        <td>" . htmlspecialchars($row["rol"]) . "</td>
                                        <td>" . htmlspecialchars($row["nombrecompleto"]) . "</td>
                                        <td>" . htmlspecialchars($row["fechacreacion"]) . "</td>
                                        <td>
                                            <button onclick='abrirEditorUsuario(\"" . htmlspecialchars($row['username']) . "\")' class='btn btn-primary btn-sm'>Editar usuario</button>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>Error al realizar la consulta en la tabla usuarios en la DB :(: " . $conexion->error . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <br>
            </div>
        </div>
    </div>
    <script src="scriptAdmonUsuarios.js"></script>
</body>

</html>