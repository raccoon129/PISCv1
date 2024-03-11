<?php

// AQUÍ SE VALIDA EL INICIO DE SESIÓN DE LOGIN.PHP
$usuario = $_POST['username'];
$contrasena = $_POST['password'];

session_start();
$_SESSION['username'] = $usuario;

include('db.php');

// Verificar la conexión
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}


$stmt = $conexion->prepare("SELECT password, rol FROM usuario WHERE username = ?");

// Verificar si la preparación fue exitosa
if ($stmt === false) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}

$stmt->bind_param("s", $usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado) {
    $filas = $resultado->fetch_assoc();

    if ($filas) {
        // Verificar la contraseña hasheada
        if (password_verify($contrasena, $filas['password'])) {
            // Guardar el rol en la sesión y redirigir
            $_SESSION['rol'] = $filas['rol'];
            if ($filas['rol'] == 'Administrador') {
                header("Location: tablero/dashboardAdministrador.php");
            } elseif ($filas['rol'] == 'Prestamista') {
                header("Location: tablero/dashboardPrestamista.php");
            }
        } else {
            // Contraseña incorrecta
            echo "<script>alert('Error al autenticar. Rectifica los datos'); window.location.href='index.php';</script>";
        }
    } else {
        // Usuario no encontrado
        echo "<script>alert('Error al autenticar. Rectifica los datos'); window.location.href='index.php';</script>";
    }
    $stmt->free_result();
} else {
    echo "Error en la ejecución de la consulta: " . $conexion->error;
}

$stmt->close();
$conexion->close();
?>
