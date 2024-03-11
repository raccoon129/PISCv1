<?php
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excepción - PISC</title>
    <style>
        /* Estilo CSS */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #0064a7;
            color: #fff;
            text-align: center;
            padding: 10px;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
        }
    </style>
</head>
<body>
    <header>
        <h1> </h1>
    </header>
    <div class="container">
        <h2>¡ALTO!</h2>
        <p>Si miras esta página, es porque no has iniciado sesión, o has intentado acceder a un directorio no autorizado.</p>
        <p>Para más información, visita el manual de usuario.</p>
    </div>

    <script>
        // Temporizador de 3 segundos (3000 milisegundos)
        setTimeout(function() {
            // Redirige a la otra página
            window.location.href = 'index.php';
        }, 3000);
    </script>
</body>
</html>

