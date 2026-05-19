<?php
/*
  Script para eliminar una raza.
  Comprueba si hay mascotas asociadas antes de borrar para evitar errores de FK.
*/
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../procesos/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['error_raza'] = "ID invalido.";
    header("Location: ../procesos/razas.php");
    exit;
}

$id_seguro = (int) $_GET['id'];

// Si hay mascotas con esta raza, MySQL tiraria por la FK; lo paramos antes
// para dar un mensaje claro al usuario.
$sql_check = "SELECT chip FROM mascotas WHERE raza_id = ?";
$stmt_check = mysqli_prepare($conn, $sql_check);

if ($stmt_check) {
    mysqli_stmt_bind_param($stmt_check, "i", $id_seguro);
    mysqli_stmt_execute($stmt_check);
    $res_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($res_check) > 0) {
        $_SESSION['error_raza'] = "No se puede eliminar la raza porque hay mascotas asociadas a ella.";
        mysqli_stmt_close($stmt_check);
        mysqli_close($conn);
        header("Location: ../procesos/razas.php");
        exit;
    }
    mysqli_stmt_close($stmt_check);
}

$sql = "DELETE FROM razas WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id_seguro);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['ok_raza'] = "Raza eliminada correctamente.";
    } else {
        $_SESSION['error_raza'] = "Error al eliminar la raza.";
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
header("Location: ../procesos/razas.php");
exit;
?>
