<?php

include 'config.php';

$conn = mysqli_connect($servername, $username, $password, $dbname);

// Si la conexion falla, no tiene sentido seguir: cortamos con un mensaje claro
if (!$conn) {
    echo "<script> alert('Error de conexión')</script>";
    die("Error de conexión: " . mysqli_connect_error());
}

?>
