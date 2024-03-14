<?php
include '../../db.php';
include('../control_acceso.php');
verificarAcceso(['Administrador']);
$accion = $_POST['accion'] ?? ''; // Usando el operador de fusión de null

function alerta($message) {
    header('Content-Type: text/html; charset=utf-8');
    $message = addslashes($message);
    echo "<script type='text/javascript'>alert('$message'); window.close();</script>";
    exit; // Asegura que no se ejecute más código PHP después de mostrar la alerta
}



switch ($accion) {
    case 'modificar_usuario':
        if (isset($_POST['accion']) && $_POST['accion'] == 'modificar_usuario') {
            // Asumiendo que 'id', 'username', etc., son arrays
            $ids = $_POST['id'];
            $usernames = $_POST['username'];

            $roles = $_POST['rol']; // Usar $roles para evitar la confusión
            $nombresCompletos = $_POST['nombrecompleto'];

            for ($i = 0; $i < count($ids); $i++) {
                $id = $ids[$i];
                $username = $usernames[$i];

                $rol = $roles[$i]; // Corregido para usar $roles
                $nombreCompleto = $nombresCompletos[$i];

                $stmt = $conexion->prepare("UPDATE usuario SET username = ?, nombrecompleto = ?, rol = ? WHERE ID = ?");
                $stmt->bind_param("sssi", $username, $nombreCompleto, $rol, $id);

                if (!$stmt->execute()) {
                    echo "Error actualizando el registro: " . $stmt->error;
                }

                $stmt->close();
            }

            alerta("Usuario modificado exitosamente.");
        }

        break;


    case 'eliminar_usuario':
        $id = $_POST['id'];
        $stmt = $conexion->prepare("DELETE FROM usuario WHERE ID = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "Usuario eliminado exitosamente";
        } else {
            echo "Error eliminando el usuario. Error: $stmt->error";
        }
        $stmt->close();
        break;

        case 'nuevo_usuario':
            $username = $_POST['username'];
            // Verificar si el nombre de usuario ya existe
            $stmt = $conexion->prepare("SELECT ID FROM usuario WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                echo "Error al crear el usuario debido a que ya existe";
            } else {
                $password = $_POST['password']; // Obtener la contraseña enviada por el usuario
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hashear la contraseña
        
                $rol = $_POST['rol'];
                $nombrecompleto = $_POST['nombrecompleto'];
                $fechacreacion = date('Y-m-d H:i:s');
        
                $stmt = $conexion->prepare("INSERT INTO usuario (username, password, rol, nombrecompleto, fechacreacion) VALUES (?, ?, ?, ?, ?)");
                // APasar $hashedPassword en lugar de $password
                $stmt->bind_param("sssss", $username, $hashedPassword, $rol, $nombrecompleto, $fechacreacion);
        
                if ($stmt->execute()) {
                    alerta("Usuario creado exitosamente");
                    echo "Error al crear el usuario. Error $stmt->error";
                }
            }
            $stmt->close();
            break;        

    case 'restablecer_contrasena':
        $username = $_POST['username'];
        $nuevaContrasena = $_POST['password']; // La nueva contraseña enviada desde el cliente

        // Hashear la nueva contraseña antes de guardarla
        $passwordHashed = password_hash($nuevaContrasena, PASSWORD_DEFAULT);

        $stmt = $conexion->prepare("UPDATE usuario SET password = ? WHERE username = ?");
        $stmt->bind_param("ss", $passwordHashed, $username);

        if ($stmt->execute()) {
            echo "Contraseña actualizada exitosamente. Comparta las nuevas credenciales con el usuario: $username";
        } else {
            echo "Error al actualizar contraseña: " . $stmt->error;
        }
        $stmt->close();
        break;


    default:
        alerta('Acción no encontrada');
        break;
}

$conexion->close();
