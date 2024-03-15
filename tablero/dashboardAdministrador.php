<?php include('session_check.php'); 
include('control_acceso.php');
verificarAcceso(['Administrador']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Administrador - PISC</title>
    <link rel="stylesheet" href="styleDashAdministrador.css">

    <!-- Inclusión de Font Awesome para los iconos y demás recursos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Enlace a jQuery para la búsqueda dinámica -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body>
    <nav class="encabezado">
        <div class="contenedor-logo">
            <img src="../img/mini22.png" alt="Logo" class="logo">
        </div>

        <!-- Avatar y nombre de usuario -->
        <div class="avatar" style="background-color: <?php echo $colorAvatar; ?>">
            <?php echo strtoupper($nombreUsuario[0]); ?>
        </div>
        <span class="user-name"><?php echo $nombreUsuario; ?></span>
        <button class="cerrar-sesion" onclick="window.location.href='logout.php'">Cerrar sesión</button>
    </nav>

    <div class="barra-lateral" id="barraLateral">
        <nav class="menu">
            <ul>
                <li><a href="#inicio" onclick="cargarModulo('modulosAdministrador/inicioAdministrador.php', '#inicio'); return false;"><i class="fas fa-house"></i> Inicio</a></li>
                <li><a href="#gestionBienes" onclick="cargarModulo('modulosAdministrador/gestionBienes.php', '#gestionBienes'); return false;"><i class="fas fa-edit"></i> Gestión de bienes</a></li>
                <li><a href="#historialPrestamos" onclick="cargarModulo('modulosAdministrador/admonHistorialPrestamos.php', '#historialPrestamos'); return false;"><i class="fas fa-box"></i> Administrar historial de préstamos</a></li>
                <li><a href="#admonUsuarios" onclick="cargarModulo('modulosAdministrador/admonUsuarios.php', '#admonUsuarios'); return false;"><i class="fas fa-user"></i> Administración de usuarios</a></li>
                <li><a href="#configuracion" onclick="cargarModulo('modulosAdministrador/configuracion.php', '#configuracion'); return false;"><i class="fas fa-hammer"></i> Configuración de variables</a></li>
            </ul>
        </nav>
    </div>

    <div class="contenido-principal" id="contenidoPrincipal">
        <iframe id="contenidoIframe" src="loader1.html" frameborder="0" style="width:100%; height:90vh;"></iframe>

    </div>

    <div class="pie-pagina">
    Dashboard para Prestamistas v1.0 | PISC - Préstamos ISC v1.1.1 (Estable)
    </div>

    <script src="dashboard.js"></script>

</body>

</html>