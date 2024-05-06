<?php
include '../db.php';


// Lista de archivos y directorios a verificar. TODOS los archivos deben estar.
$archivosDirectorios = [
    'bibliotecaFotosBienes.json', 'bienesDadosDeBaja.json', 'control_acceso.php',
    'dashboard.js', 'dashboardAdministrador.php', 'dashboardPrestamista.php',
    'default.php', 'estiloDataTablesResponsivas.css', 'estiloTransparente.css',
    'loader0.html', 'loader1.html', 'logout.php', 'session_check.php',
    'styleDashAdministrador.css', 'styleDashPrestamista.css', 'variablesConfiguracion.json',
    'modulosAdministrador/accionesAdmonUsuarios.php', 'modulosAdministrador/admonHistorialPrestamos.php',
    'modulosAdministrador/admonHistorialPrestamosEliminarPrestamo.php', 'modulosAdministrador/admonPrestamosProcesarEliminarPrestamo.php',
    'modulosAdministrador/admonUsuarios.php', 'modulosAdministrador/admonUsuariosInsertarUsuario.php',
    'modulosAdministrador/configuracion.php', 'modulosAdministrador/configuracionFotosBienes.php',
    'modulosAdministrador/consultaGestionBienesInsertarBien.php', 'modulosAdministrador/consultaGestionBienesVolcarDatosBienes.php',
    'modulosAdministrador/consultaInicioAdministrador.php', 'modulosAdministrador/editarUsuarioAdmonUsuarios.php',
    'modulosAdministrador/estiloInicioAdministrador.css', 'modulosAdministrador/gestionBienes.php',
    'modulosAdministrador/gestionBienesAuditarBajas.php', 'modulosAdministrador/gestionBienesDarDeBajaBien.php',
    'modulosAdministrador/gestionBienesDetallesCadaBien.php', 'modulosAdministrador/gestionBienesEditarBien.php',
    'modulosAdministrador/gestionBienesExportarTabla.php', 'modulosAdministrador/gestionBienesInsertarBien.php',
    'modulosAdministrador/gestionBienesProcesarActualizacion.php', 'modulosAdministrador/gestionBienesSeccionEditarBien.php',
    'modulosAdministrador/gestionBienesVisualizarFotoBien.php', 'modulosAdministrador/gestionBienesVolcarDatosBienes.php',
    'modulosAdministrador/inicioAdministrador.php', 'modulosAdministrador/procesarImagenes.php',
    'modulosAdministrador/scriptAdmonHistorialPrestamos.js', 'modulosAdministrador/scriptAdmonUsuarios.js',
    'modulosAdministrador/scriptAdmonUsuariosInsertarUsuario.js', 'modulosAdministrador/scriptEditarUsuarioAdmonUsuarios.js',
    'modulosAdministrador/scriptGestionBienes.js', 'modulosAdministrador/scriptGestionBienesEditarBien.js',
    'modulosAdministrador/scriptGestionBienesInsertarBien.js', 'modulosAdministrador/scriptHistorialPrestamosEliminarPrestamo.js',
    'modulosPrestamista/actualizacionConfirmarDevolucion.php', 'modulosPrestamista/buscarBienNuevoPrestamo.php',
    'modulosPrestamista/catalogoBienes.php', 'modulosPrestamista/confirmarDevolucion.php',
    'modulosPrestamista/detallesPrestamo.php', 'modulosPrestamista/generarVale.php',
    'modulosPrestamista/historialPrestamos.php', 'modulosPrestamista/historialPrestamosExportarHistorial.php',
    'modulosPrestamista/inicioPrestamista.php', 'modulosPrestamista/nuevoPrestamo.php',
    'modulosPrestamista/procesarPrestamoNuevoPrestamo.php', 'modulosPrestamista/registrarDevolucion.php',
    'modulosPrestamista/scriptCatalogoBienes.js', 'modulosPrestamista/scriptConfirmarDevolucion.js',
    'modulosPrestamista/scriptHistorialPrestamos.js', 'modulosPrestamista/scriptNuevoPrestamo.js',
    'modulosPrestamista/scriptRegistrarDevolucion.js'
];


$errores = [];

// Verificar existencia de archivos y directorios
foreach ($archivosDirectorios as $item) {
    if (!file_exists($item)) {
        $errores[] = $item;
    }
}

// Verificar la conexión a la base de datos
$estadoDB = $conexion->connect_error ? 'Falló la conexión: ' . $conexion->connect_error : 'Conexión exitosa';

// Información del servidor
$versionPHP = phpversion();
$ipServidor = $_SERVER['SERVER_ADDR'];
$sistemaOperativoServidor = PHP_OS;

// Información del cliente se obtendrá a través de JavaScript
$ipCliente = $_SERVER['REMOTE_ADDR'];
$agenteUsuario = $_SERVER['HTTP_USER_AGENT'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Salud del Sistema - PISC v1.1.4</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('sistemaOperativoCliente').textContent = navigator.platform;
        });
    </script>
</head>

<body>
    <div class="container mt-4">
        <h2>Verificación de Salud de PISC v1.1.4</h2>
        <br>
        <p>Verificación de la integridad de los archivos y requerimientos necesarios para el funcionamiento óptimo de esta plataforma.</p>
        <?php if (!$errores) : ?>
            <div class="alert alert-success" role="alert">
                No se han encontrado problemas.
            </div>
        <?php endif; ?>
        <?php if ($errores) : ?>
            <div class="alert alert-danger" role="alert">
                <strong>Violación de Integridad:</strong> Los siguientes archivos o directorios están ausentes o corruptos:
                <ul>
                    <?php foreach ($errores as $error) : ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Componente</th>
                    <th scope="col">Estado</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Conexión a la Base de Datos</td>
                    <td><?= $estadoDB ?></td>
                </tr>
                <tr>
                    <td>Versión de PHP</td>
                    <td><?= $versionPHP ?></td>
                </tr>
                <tr>
                    <td>Sistema Operativo del Servidor</td>
                    <td><?= $sistemaOperativoServidor ?></td>
                </tr>
                <tr>
                    <td>Dirección IP del Servidor</td>
                    <td><?= $ipServidor ?></td>
                </tr>
                <tr>
                    <td>Dirección IP del Cliente</td>
                    <td><?= $ipCliente ?></td>
                </tr>
                <tr>
                    <td>Agente de Usuario</td>
                    <td><?= $agenteUsuario ?></td>
                </tr>
                <tr>
                    <td>Sistema Operativo del Cliente</td>
                    <td id="sistemaOperativoCliente"></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>