<?php
// FUNCIONALIDAD CRITICA PISC - SI SE MODIFICA, SE PUEDEN PERDER EL ACCESO A TODOS LOS MODULOS
if (!isset($_SESSION)) {
    session_start();
}

// Función para verificar el acceso al módulo
function verificarAcceso($rolesPermitidos = null) {
    // Verificar si el usuario ha iniciado sesión
    if (!isset($_SESSION['username'])) {
        // Si no ha iniciado sesión, redirigir al break
        header("Location: ../../break.php");
        exit();
    }

    // Si se proporcionan roles permitidos, realizar la verificación adicional
    if ($rolesPermitidos !== null && !in_array($_SESSION['rol'], $rolesPermitidos)) {
        // Si el rol no está permitido, redirigir a una página de error o inicio
        header("Location: ../break.php");
        //echo "<script>alert('No tienes permiso para acceder a esta página.'); window.location.href='../index.php';</script>";
        exit();
    }
    // Si no se especifican roles, simplemente se verifica la sesión, permitiendo el acceso a cualquier usuario logueado. 
    // Se podría emplear session_check.php pero la verdad a ese ya ni quiero moverle para anadir esa condición si funciona bien.
}

// En el archivo de módulo específico, llamar a verificarAcceso sin parámetros si el acceso es general
// Ejemplo para un módulo accesible por cualquier usuario logueado:
// verificarAcceso();
//Esto es útil en el caso de modulos en la parte de prestamista que requiere acceso del administrador
