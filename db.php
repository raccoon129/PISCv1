<?php

// host, usuario, contraseña, nombre db


// Esta es para conectarla al localhost
$conexion=mysqli_connect("localhost","root","","dbprestamosisc");

//esta es para conectarlo a la base de datos en la nube
//$conexion=mysqli_connect("db4free.net","usuarioadminisc","sistemas","dbprestamosisc_1");
$conexion->set_charset("utf8mb4");


// Error handling
if (!$conexion) {
    die("Connection failed: " . mysqli_connect_error());
}

?>
