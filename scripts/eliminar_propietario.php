<?php
session_start();
include '../includes/conexion.php';

// Comprueba que el usuario está autenticado.
if (!isset($_SESSION['usuario'])) {
    header("Location: ../procesos/login.php");
    exit;
}

// Verifica que el id del propietario llegue por GET.
if (!isset($_GET['id'])) {
    $_SESSION['error_propietario'] = "ID invalido.";
    header("Location: ../procesos/propietarios.php");
    exit;
}

// Convierte el id a entero para evitar inyección SQL y asegurar un valor numérico.
$id_seguro = (int) $_GET['id'];

// 1) Limpiamos la clave foránea mascota_chip del propietario, si existiera, esto evita que quede un valor colgando en la tabla propietarios.
$sql_clear = "UPDATE propietarios SET mascota_chip = NULL WHERE id = ?";
$stmt_clear = mysqli_prepare($conn, $sql_clear);
if ($stmt_clear) {
    mysqli_stmt_bind_param($stmt_clear, "i", $id_seguro);
    mysqli_stmt_execute($stmt_clear);
    mysqli_stmt_close($stmt_clear);
}

// 2) Eliminamos todas las mascotas que pertenecen a este propietario.
$sql_mascotas = "DELETE FROM mascotas WHERE propietario_id = ?";
$stmt_mascotas = mysqli_prepare($conn, $sql_mascotas);
if ($stmt_mascotas) {
    mysqli_stmt_bind_param($stmt_mascotas, "i", $id_seguro);
    mysqli_stmt_execute($stmt_mascotas);
    mysqli_stmt_close($stmt_mascotas);
}

// 3) Ahora sí podemos borrar el propietario sin problemas de claves foráneas.
$sql = "DELETE FROM propietarios WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id_seguro);

    if (mysqli_stmt_execute($stmt)) {
        // Si todo sale bien, guardamos un mensaje de éxito en la sesión.
        $_SESSION['ok_propietario'] = "Propietario eliminado correctamente (y sus mascotas asociadas).";
    } else {
        // Si hay un error, guardamos un mensaje de error para mostrarlo.
        $_SESSION['error_propietario'] = "Error al eliminar el propietario.";
    }

    mysqli_stmt_close($stmt);
}

// Cierra la conexión y redirige de regreso al listado de propietarios.
mysqli_close($conn);
header("Location: ../procesos/propietarios.php");
exit;
?>
