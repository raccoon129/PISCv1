<?php
// Submódulo PISC (Funcionalidad)
include '../../db.php';
include('../control_acceso.php');
verificarAcceso(['Administrador']);

function procesarImagen($rutaTemporal, $bienID)
{
    echo "LOGS - Comenzando procesamiento de la imagen para el BienID: $bienID <br>";
    

    if (!$rutaTemporal) {
        echo "Error: No se recibió ninguna ruta temporal.<br>";
        return false;
    }

    // Definir la ruta de destino y extensiones permitidas
    $extension = strtolower(pathinfo($_FILES['imagenBien']['name'], PATHINFO_EXTENSION));
    $rutaDestino = "../../tablero/imgBienes/" . $bienID . '.' . $extension;
    $allowedExtensions = ['jpg', 'jpeg', 'png'];

    // Verificar la extensión de la imagen
    if (!in_array($extension, $allowedExtensions)) {
        return "Error: Tipo de archivo no permitido.";
    }

    if($allowedExtensions == 'png'){
        $imagenOriginal = imagecreatefrompng($rutaTemporal);
        // Preservar transparencia en PNG
        imagealphablending($imagenOriginal, true);
        imagesavealpha($imagenOriginal, true);
    }

    // Obtener las dimensiones de la imagen
    if (!$tamaño = getimagesize($rutaTemporal)) {
        echo "Error: No se pudo obtener el tamaño de la imagen.<br>";
        return false;
    }
    
    list($anchoOriginal, $altoOriginal) = $tamaño;
    echo "Las dimensiones originales son: Ancho = $anchoOriginal, Alto = $altoOriginal <br>";
    
    // Definir el tamaño máximo en píxeles (2MPX)
    $maximoPixeles = 2 * 1000000; // 2 Megapíxeles
    $calidadImagen = 75; // Calidad de JPEG

    // Redimensionar imagen si es necesario
    
    if ($anchoOriginal * $altoOriginal > $maximoPixeles) {
        echo "Redimensionando imagen...<br>";
        $ratio = $anchoOriginal / $altoOriginal;
        $nuevoAncho = sqrt($maximoPixeles * $ratio);
        $nuevoAlto = $maximoPixeles / $nuevoAncho;

        $imagenRedimensionada = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
        $imagenOriginal = imagecreatefromjpeg($rutaTemporal);
        imagecopyresampled($imagenRedimensionada, $imagenOriginal, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $anchoOriginal, $altoOriginal);

        // Guardar imagen redimensionada
        if (!imagejpeg($imagenRedimensionada, $rutaDestino, $calidadImagen)) {
            echo "Error: No se pudo guardar la imagen redimensionada.<br>";
            imagedestroy($imagenOriginal);
            imagedestroy($imagenRedimensionada);
            return false;
        }

        imagedestroy($imagenOriginal);
        imagedestroy($imagenRedimensionada);
        echo "Imagen redimensionada y guardada con éxito.<br>";
    } else {
        echo "Moviendo imagen al destino sin redimensionar...<br>";
        if (!move_uploaded_file($rutaTemporal, $rutaDestino)) {
            echo "Error: No se pudo mover la imagen al destino.<br>";
            return false;
        }
    }

    // Actualizar el JSON con la nueva ruta de imagen
    if (!actualizarJson($bienID, $rutaDestino)) {
        echo "Error: No se pudo actualizar el archivo JSON.<br>";
        return false;
    }

    return true;
}

function actualizarJson($bienID, $rutaImagen)
{
    echo "Actualizando archivo JSON...<br>";
    $jsonFilePath = '../bibliotecaFotosBienes.json';

    $fotosBienes = file_exists($jsonFilePath) ? json_decode(file_get_contents($jsonFilePath), true) : [];
    $fotosBienes[$bienID] = $rutaImagen;

    if (file_put_contents($jsonFilePath, json_encode($fotosBienes, JSON_PRETTY_PRINT))) {
        echo "Archivo JSON actualizado con éxito.<br>";
        return true;
    } else {
        echo "Error: No se pudo guardar el archivo JSON.<br>";
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['imagenBien'])) {
    $rutaTemporal = $_FILES['imagenBien']['tmp_name'];
    $bienID = $_POST['bienID'];

    if (procesarImagen($rutaTemporal, $bienID)) {
        echo "<script>alert('Imagen procesada y guardada con éxito.'); window.close();</script>";
    } else {
        echo "<script>alert('Error al procesar la imagen.'); window.close();</script>";
    }
}
?>
