<?php
include '../../db.php';
include('../control_acceso.php');
verificarAcceso(['Administrador']);

header('Content-Type: application/json');

function procesarImagen($archivo, $bienID) {
    if ($archivo['error'] != UPLOAD_ERR_OK) {
        return ["success" => false, "message" => "Error al cargar el archivo."];
    }

    $rutaTemporal = $archivo['tmp_name'];
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    $rutaDestino = "../../tablero/imgBienes/" . $bienID . '.' . $extension;
    $allowedExtensions = ['jpg', 'jpeg', 'png'];

    if (!in_array($extension, $allowedExtensions)) {
        return ["success" => false, "message" => "Tipo de archivo no permitido."];
    }

    list($anchoOriginal, $altoOriginal) = getimagesize($rutaTemporal);
    if (!$anchoOriginal || !$altoOriginal) {
        return ["success" => false, "message" => "No se pudo obtener el tamaño de la imagen."];
    }

    $maximoPixeles = 2 * 1000000; // 2 Megapíxeles
    $calidadImagen = 75; // Calidad de JPEG

    if ($anchoOriginal * $altoOriginal > $maximoPixeles) {
        $ratio = $anchoOriginal / $altoOriginal;
        $nuevoAncho = sqrt($maximoPixeles * $ratio);
        $nuevoAlto = $maximoPixeles / $nuevoAncho;

        $imagenRedimensionada = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
        $imagenOriginal = $extension == 'png' ? imagecreatefrompng($rutaTemporal) : imagecreatefromjpeg($rutaTemporal);
        imagecopyresampled($imagenRedimensionada, $imagenOriginal, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $anchoOriginal, $altoOriginal);

        if ($extension == 'png') {
            $resultado = imagepng($imagenRedimensionada, $rutaDestino);
        } else {
            $resultado = imagejpeg($imagenRedimensionada, $rutaDestino, $calidadImagen);
        }

        imagedestroy($imagenOriginal);
        imagedestroy($imagenRedimensionada);

        if (!$resultado) {
            // Si no se puede guardar la imagen redimensionada, intentar guardar la original
            if (!move_uploaded_file($rutaTemporal, $rutaDestino)) {
                return ["success" => false, "message" => "No se pudo guardar la imagen original en el destino."];
            } else {
                return ["success" => true, "message" => "Imagen guardada en tamaño original debido a un problema al redimensionar."];
            }
        }
    } else {
        if (!move_uploaded_file($rutaTemporal, $rutaDestino)) {
            return ["success" => false, "message" => "No se pudo mover la imagen al destino."];
        }
    }

    if (!actualizarJson($bienID, $rutaDestino)) {
        return ["success" => false, "message" => "No se pudo actualizar el archivo JSON."];
    }

    return ["success" => true, "message" => "Imagen procesada y guardada con éxito."];
}

function actualizarJson($bienID, $rutaImagen) {
    $jsonFilePath = '../bibliotecaFotosBienes.json';

    $fotosBienes = file_exists($jsonFilePath) ? json_decode(file_get_contents($jsonFilePath), true) : [];
    $fotosBienes[$bienID] = $rutaImagen;

    if (!file_put_contents($jsonFilePath, json_encode($fotosBienes, JSON_PRETTY_PRINT))) {
        return false;
    }

    return true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['imagenBien'])) {
    $archivo = $_FILES['imagenBien'];
    $bienID = $_POST['bienID'];
    $resultado = procesarImagen($archivo, $bienID);
    echo json_encode($resultado);
} else {
    echo json_encode(["success" => false, "message" => "No se proporcionó ninguna imagen."]);
}
?>