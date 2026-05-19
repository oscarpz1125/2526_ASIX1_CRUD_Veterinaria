<?php
/*
  Script para eliminar un veterinario.
  Verifica si tiene mascotas asignadas y no borra si aún existen relaciones.
*/
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../procesos/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['error_veterinario'] = "ID invalido.";
    header("Location: ../procesos/veterinarios.php");
    exit;
}

$id_seguro = (int) $_GET['id'];

// Igual que con razas: si hay mascotas asignadas, avisamos en lugar de petar la FK
$sql_check = "SELECT chip FROM mascotas WHERE veterinario_id = ?";
$stmt_check = mysqli_prepare($conn, $sql_check);

if ($stmt_check) {
    mysqli_stmt_bind_param($stmt_check, "i", $id_seguro);
    mysqli_stmt_execute($stmt_check);
    $res_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($res_check) > 0) {
        $_SESSION['error_veterinario'] = "No se puede eliminar el veterinario porque tiene mascotas asociadas.";
        mysqli_stmt_close($stmt_check);
        mysqli_close($conn);
        header("Location: ../procesos/veterinarios.php");
        exit;
    }
    mysqli_stmt_close($stmt_check);
}

$sql = "DELETE FROM veterinarios WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id_seguro);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['ok_veterinario'] = "Veterinario eliminado correctamente.";
    } else {
        $_SESSION['error_veterinario'] = "Error al eliminar el veterinario.";
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
header("Location: ../procesos/veterinarios.php");
exit;
?>
