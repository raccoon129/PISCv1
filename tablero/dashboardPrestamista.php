<?php include('session_check.php'); 
include('control_acceso.php');
verificarAcceso(['Prestamista']);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Prestamista - PISC</title>
    <link rel="stylesheet" href="styleDashPrestamista.css">
    <!-- Inclusión de Font Awesome para los iconos y demás recursos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
        <!-- Botón de cerrar sesión -->
        <button class="cerrar-sesion" onclick="window.location.href='logout.php'">Cerrar sesión</button>
    </nav>

    <div class="barra-lateral" id="barraLateral">
        <nav class="menu">
            <ul>
                <li><a href="#inicio" onclick="cargarModulo('modulosPrestamista/inicioPrestamista.php', '#inicio'); return false;"><i class="fas fa-house"></i> Inicio</a></li>
                <li><a href="#nuevoPrestamo" onclick="cargarModulo('modulosPrestamista/nuevoPrestamo.php', '#nuevoPrestamo'); return false;"><i class="fas fa-edit"></i> Nuevo Préstamo</a></li>
                <li><a href="#registrarDevolucion" onclick="cargarModulo('modulosPrestamista/registrarDevolucion.php', '#registrarDevolucion'); return false;"><i class="fas fa-arrow-up-right-from-square"></i> Registrar devolución</a></li>
                <li><a href="#historialPrestamos" onclick="cargarModulo('modulosPrestamista/historialPrestamos.php', '#historialPrestamos'); return false;"><i class="fas fa-history"></i> Historial de préstamos</a></li>
            </ul>
        </nav>
    </div>

    <div class="contenido-principal" id="contenidoPrincipal">
        <iframe id="contenidoIframe" src="modulosPrestamista/inicioPrestamista.php" frameborder="0" style="width:100%; height:90vh;"></iframe>
    </div>

    <div class="pie-pagina">
        Dashboard para Prestamistas v1.0 | PISC - Préstamos ISC v1.1.1 (Estable)
    </div>

    <!-- Script para cargar los modulos dentro del div. Podrás cuestionar mis métodos, pero no mis resultados. -->
    <script src="dashboard.js"></script>

</body>

</html>