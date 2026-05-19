<?php
/*
  Script para eliminar un propietario.
  Borra primero mascotas relacionadas y luego elimina el propio propietario.
*/
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../procesos/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['error_propietario'] = "ID invalido.";
    header("Location: ../procesos/propietarios.php");
    exit;
}

// Casteo a int por seguridad antes de pasarlo a las consultas
$id_seguro = (int) $_GET['id'];

// 1) Limpiamos la FK mascota_chip en este propietario para no dejarla colgando
$sql_clear = "UPDATE propietarios SET mascota_chip = NULL WHERE id = ?";
$stmt_clear = mysqli_prepare($conn, $sql_clear);
if ($stmt_clear) {
    mysqli_stmt_bind_param($stmt_clear, "i", $id_seguro);
    mysqli_stmt_execute($stmt_clear);
    mysqli_stmt_close($stmt_clear);
}

// 2) Borrado en cascada manual: mascotas dependientes primero
$sql_mascotas = "DELETE FROM mascotas WHERE propietario_id = ?";
$stmt_mascotas = mysqli_prepare($conn, $sql_mascotas);
if ($stmt_mascotas) {
    mysqli_stmt_bind_param($stmt_mascotas, "i", $id_seguro);
    mysqli_stmt_execute($stmt_mascotas);
    mysqli_stmt_close($stmt_mascotas);
}

// 3) Y ya podemos borrar el propietario sin choque de FK
$sql = "DELETE FROM propietarios WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id_seguro);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['ok_propietario'] = "Propietario eliminado correctamente (y sus mascotas asociadas).";
    } else {
        $_SESSION['error_propietario'] = "Error al eliminar el propietario.";
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
header("Location: ../procesos/propietarios.php");
exit;
?>
