<?php

// ESTE ARCHIVO ES LA VERIFICACIÓN INICIAL SI EXISTEN ADMINISTRADORES PARA ENTRAR EN LA PLATAFORMA.
// DEBERÍA MOSTRARSE UNA ÚNICA VEZ.

//Quizá se muestre en el caso que se borren todos los usuarios administradores. Se ha previsto ese caso.

include 'db.php';

// Verificar si existe al menos un administrador
//$sql = "SELECT ID FROM usuario WHERE rol='Administrador'";
$sql = "SELECT ID FROM usuario WHERE rol='Administrador' AND ID = -1"; 
$result = mysqli_query($conexion, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    // Redirigir a la página de inicio de sesión
    header("Location: login.php");
    exit();
} else {
?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Instalación de PISC v1.0.1</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    </head>

    <body>
        <div class="container mt-5">
            <div class="card">
                <div class="card-body">
                    <h1 class="card-title mb-4">Instalación de PISC v1.1.1</h1>
                    <h3 class="card-subtitle mb-2 text-muted">Gracias por adquirir su nueva solución para el control de préstamos y de bienes.</h3>
                    <h5>Por favor, ingrese los campos requeridos para crear su nueva cuenta de administrador:</h5>
                    <div class='alert alert-info'>Recuerde sus credenciales, esta pantalla aparecerá una única vez. <a href='/resources/ManualUsuarioPISC.pdf' class='alert-link'>Más información</a>.</div>

                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'], $_POST['password'])) {
                        $username = mysqli_real_escape_string($conexion, $_POST['username']);
                        $password = mysqli_real_escape_string($conexion, $_POST['password']); // Esta es la contraseña en texto plano
                        $nombrecompleto = mysqli_real_escape_string($conexion, $_POST['nombrecompleto']);

                        // Hashear la contraseña antes de guardarla en la base de datos
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                        // Obtener la fecha y hora actuales
                        $fechacreacion = date('Y-m-d H:i:s');

                        $sql = "INSERT INTO usuario (username, password, rol, nombrecompleto, fechacreacion) VALUES (?, ?, 'Administrador', ?, ?)";

                        if ($stmt = $conexion->prepare($sql)) {
                            // Nota: Ahora estamos pasando $hashed_password en lugar de $password
                            $stmt->bind_param("ssss", $username, $hashed_password, $nombrecompleto, $fechacreacion);

                            if ($stmt->execute()) {
                                echo "<script>alert('Administrador creado con éxito. Por favor, inicie sesión.'); window.location = 'login.php';</script>";
                            } else {
                                echo "<div class='alert alert-danger'>Error al crear el administrador: " . $stmt->error . "</div>";
                            }

                            $stmt->close();
                        } else {
                            echo "<div class='alert alert-danger'>Error al preparar la consulta: " . $conexion->error . "</div>";
                        }
                    }


                    ?>

                    <form method="POST" id="formCrearUsuario">
                        <div class="form-group">
                            <label for="username">Nombre de usuario para inicio de sesión: (No se permiten espacios en blanco)</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Contraseña:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="nombrecompleto">Nombre completo:</label>
                            <input type="text" class="form-control" id="nombrecompleto" name="nombrecompleto" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Crear cuenta de Administrador</button>
                    </form>

                    <div class="mt-4 text-center">
                        <img src="img/CajaPISC.png" alt="Branding PISC" class="img-fluid">
                        <p>PISC - Préstamos ISC v1.0.1</p>
                    </div>
                </div>
            </div>
        </div>
    </body>
    <script src="validarCamposSinEspacios.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            validarCampoSinEspacios('formCrearUsuario', 'username');
        });
    </script>

    </html>
<?php
}

mysqli_close($conexion);
?>