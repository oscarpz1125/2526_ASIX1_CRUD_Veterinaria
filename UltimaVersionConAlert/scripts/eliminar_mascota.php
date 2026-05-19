<?php
/*
  Script para eliminar una mascota.
  Actualiza primero las referencias y luego borra la mascota de la base de datos.
*/
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../procesos/login.php");
    exit;
}

if (!isset($_GET['chip']) || empty($_GET['chip'])) {
    $_SESSION['error_mascota'] = "Falta el chip de la mascota.";
    header("Location: ../procesos/mascotas.php");
    exit;
}

$chip = $_GET['chip'];

// Antes de borrar la mascota, anulamos la FK en propietarios que apunte a ella
$sql_clear = "UPDATE propietarios SET mascota_chip = NULL WHERE mascota_chip = ?";
$stmt_clear = mysqli_prepare($conn, $sql_clear);
if ($stmt_clear) {
    mysqli_stmt_bind_param($stmt_clear, "s", $chip);
    mysqli_stmt_execute($stmt_clear);
    mysqli_stmt_close($stmt_clear);
}

$sql = "DELETE FROM mascotas WHERE chip = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $chip);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['ok_mascota'] = "Mascota eliminada correctamente.";
    } else {
        $_SESSION['error_mascota'] = "Error al eliminar la mascota.";
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
header("Location: ../procesos/mascotas.php");
exit;
?>
