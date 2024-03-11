<?php
// Iniciar la sesión si no se ha iniciado
if (!isset($_SESSION)) {
    session_start();
}

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['username'])) {
    // Si no ha iniciado sesión, redirigir a la página de inicio de sesión
    header("Location: ../break.php");
    exit();
}

// Obtener el nombre de usuario y el rol de la sesión
$nombreUsuario = $_SESSION['username'] ?? 'Usuario';
$rolUsuario = $_SESSION['rol'] ?? 'Invitado'; // Asume 'Invitado' o un valor por defecto si no está definido

// Generar un color aleatorio para el avatar
$colorAvatar = sprintf('#%06X', mt_rand(0, 0xFFFFFF));


if ($rolUsuario == 'Administrador') {
    // Código específico para el administrador
    // Por ejemplo, redirigir a una página de administrador
} elseif ($rolUsuario == 'Prestamista') {
    // Código específico para el prestamista
    // Por ejemplo, continuar en la página actual o realizar alguna acción específica
} else {
    session_destroy();
    exit();
}


?>

