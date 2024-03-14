<?php
session_start();

// Verificar si el usuario ya ha iniciado sesión
if (isset($_SESSION['username']) && isset($_SESSION['rol'])) {
    // Redirigir al usuario a su dashboard correspondiente
    if ($_SESSION['rol'] == 'Administrador') {
        header("Location: tablero/dashboardAdministrador.php");
        exit();
    } elseif ($_SESSION['rol'] == 'Prestamista') {
        header("Location: tablero/dashboardPrestamista.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - PISC Plataforma</title>
    <link rel="stylesheet" type="text/css" href="stylesLogin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

</head>

<body>
    <div class="encabezado">
        <div class="contenedor-logo">
            <img src="img/mini22.png" alt="Logo" class="logo" title="Haz clic aquí para saber más sobre este producto.">
        </div>


        <div class="contenedor-brandingEscuela">
            <img src="img/topISC.png" alt="Top" class="top">
        </div>
    </div>

    <div id="overlay"></div>

    <div class="contenedor">
        <div class="formulario-login">
            <form id="formularioLogin" action="validar.php" method="post">
                <h2>Iniciar Sesión</h2>
                <div class="grupo-formulario">
                    <label><i class="fa fa-user"></i> Usuario:</label>
                    <input type="text" name="username" id="nombreUsuario" required placeholder="Ingresa un nombre de usuario previamente compartido">
                </div>
                <div class="grupo-formulario">
                    <label><i class="fa fa-lock"></i> Contraseña:</label>
                    <input type="password" name="password" id="contrasena" required placeholder="Ingresa una contraseña">
                </div>
                <button type="button" id="botonIngresar">Ingresar al sistema</button>
            </form>
        </div>
        <div class="informacion-formulario">
            <h2>PISC - Sistema de control de prestamos V1.1.1</h2>
            <p>Administra y gestiona bienes, genera vales, y realiza un seguimiento de los préstamos en laboratorios o áreas específicas.</p>
            <p></p>
        </div>
    </div>


    <script src="scriptLogin.js"></script>
</body>

</html>