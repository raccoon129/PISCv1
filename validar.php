<?php
include('db.php');
session_start();

$usuario = $_POST['username'] ?? '';
$contrasena = $_POST['password'] ?? '';

// Conexión a la base de datos
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

$consulta = $conexion->prepare("SELECT password, rol FROM usuario WHERE username = ?");
if ($consulta === false) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}

$consulta->bind_param("s", $usuario);
$consulta->execute();
$resultado = $consulta->get_result();

if ($resultado) {
    $fila = $resultado->fetch_assoc();
    if ($fila && password_verify($contrasena, $fila['password'])) {
        // Verificación de licencia
        $urlLicencia = 'https://raw.githubusercontent.com/raccoon129/licencias/main/licenciaPISC_ISC-ITESHU.json'; 
        $response = file_get_contents($urlLicencia);
        $licencia = json_decode($response, true);

        if ($licencia['licencia'] == 'false') {
            session_destroy();
            echo "<script>alert('{$licencia['mensaje']}'); window.location.href='index.php';</script>";
            exit;
        }

        // Usuario autenticado correctamente
        $_SESSION['username'] = $usuario;
        $_SESSION['rol'] = $fila['rol'];

        // Registra los datos del inicio de sesión
        registrarInicioSesion($usuario);

        // Redirecciona según el rol
        if ($fila['rol'] == 'Administrador') {
            header("Location: tablero/dashboardAdministrador.php");
        } elseif ($fila['rol'] == 'Prestamista') {
            header("Location: tablero/dashboardPrestamista.php");
        }
    } else {
        // Datos incorrectos
        echo "<script>alert('Error al autenticar. Rectifica los datos'); window.location.href='index.php';</script>";
    }
} else {
    echo "Error en la ejecución de la consulta: " . $conexion->error;
}

$consulta->close();
$conexion->close();

function registrarInicioSesion($usuario) {
    $rutaArchivo = 'logsS.json';
    $datosSesion = [
        'usuario' => $usuario,
        'fechaHora' => date('Y-m-d H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR'],
        'agenteUsuario' => $_SERVER['HTTP_USER_AGENT'],
        'sistemaOperativo' => detectarSO($_SERVER['HTTP_USER_AGENT']),
        'navegador' => detectarNavegador($_SERVER['HTTP_USER_AGENT'])
    ];

    if (file_exists($rutaArchivo)) {
        $datosActuales = json_decode(file_get_contents($rutaArchivo), true);
    } else {
        $datosActuales = [];
    }
    
    $datosActuales[] = $datosSesion;
    file_put_contents($rutaArchivo, json_encode($datosActuales, JSON_PRETTY_PRINT));
}

function detectarSO($agenteUsuario) {
    // Detección simple de sistema operativo
    $arraySO = [
        'Windows' => 'Windows',
        'Mac OS' => 'Macintosh',
        'Linux' => 'Linux',
        'Android' => 'Android',
        'iOS' => 'iOS'
    ];

    foreach ($arraySO as $so => $match) {
        if (preg_match('/' . $match . '/i', $agenteUsuario)) {
            return $so;
        }
    }
    return 'SO Desconocido';
}

function detectarNavegador($agenteUsuario) {
    // Detección de navegador
    $arrayNavegadores = [
        'Chrome' => 'Chrome',
        'Firefox' => 'Firefox',
        'Safari' => 'Safari',
        'Opera' => 'Opera',
        'Edge' => 'Edge',
        'IE' => 'MSIE'
    ];

    foreach ($arrayNavegadores as $navegador => $match) {
        if (preg_match('/' . $match . '/i', $agenteUsuario)) {
            return $navegador;
        }
    }
    return 'Navegador Desconocido';
}
?>
