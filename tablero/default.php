<?php
include('../db.php');
session_start();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submodulo PISC Reservado para cargar contenido NULL</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>
    <div class="container mt-5">
        <?php
        require_once 'session_check.php';

        // Función para obtener el saludo según la hora del día
        function obtenerSaludo()
        {
            $hora = date('H');
            if ($hora >= 6 && $hora < 12) {
                return 'Buenos días';
            } elseif ($hora >= 12 && $hora < 18) {
                return 'Buenas tardes';
            } else {
                return 'Buenas noches';
            }
        }


        // Obtener el saludo según la hora del día
        $saludo = obtenerSaludo();

        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }

        // Obtener el nombre completo del usuario desde la base de datos
        $nombreUsuario = $_SESSION['username'];
        $sql = "SELECT nombrecompleto FROM usuario WHERE username = '$nombreUsuario'";
        $resultado = $conexion->query($sql);

        if ($resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            $nombreCompleto = $fila['nombrecompleto'];
        } else {
            $nombreCompleto = 'Usuario';
        }

        $conexion->close();
        ?>

        <h2><?php echo "$saludo, $nombreCompleto"; ?></h2>
        <p>Selecciona una opción en el menú para continuar o refresca esta ventana (F5).</p>
        <br>
        <br>
        <!-- Añadir la imagen -->
        <div class="text-center">
            <img src="../img/NuevoPISC.png" alt="Imagen" class="img-fluid mx-auto d-block" style="max-width: 300px;">
        </div>

        <p strong class="text-center">PISC v1.1.1</p>
        <p strong class="text-center">Última actualización: <?php echo date('H:i:s'); ?></p>

    </div>
</body>

</html>