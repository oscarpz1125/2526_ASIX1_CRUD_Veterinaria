<?php

session_start();
include '../includes/conexion.php';
// Comprueba que el usuario está autenticado antes de permitir la eliminación.
if (!isset($_SESSION['usuario'])) {
    header("Location: ../procesos/login.php");
    exit;
}

// Verifica que el id de la raza llegue por GET.
if (!isset($_GET['id'])) {
    $_SESSION['error_raza'] = "ID invalido.";
    header("Location: ../procesos/razas.php");
    exit;
}

// Convierte el id a entero para asegurar un valor válido.
$id_seguro = (int) $_GET['id'];

// 1) Comprobamos si existen mascotas que usan esta raza.
// Si existen, no permitimos borrar la raza y devolvemos un mensaje claro.
$sql_check = "SELECT chip FROM mascotas WHERE raza_id = ?";
$stmt_check = mysqli_prepare($conn, $sql_check);

if ($stmt_check) {
    mysqli_stmt_bind_param($stmt_check, "i", $id_seguro);
    mysqli_stmt_execute($stmt_check);
    $res_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($res_check) > 0) {
        // Hay mascotas asociadas, por eso no podemos eliminar la raza.
        $_SESSION['error_raza'] = "No se puede eliminar la raza porque hay mascotas asociadas a ella.";
        mysqli_stmt_close($stmt_check);
        mysqli_close($conn);
        header("Location: ../procesos/razas.php");
        exit;
    }
    mysqli_stmt_close($stmt_check);
}

// 2) Si no hay mascotas asociadas, podemos borrar la raza.
$sql = "DELETE FROM razas WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id_seguro);

    if (mysqli_stmt_execute($stmt)) {
        // Si la ejecución fue correcta, guardamos el mensaje de éxito.
        $_SESSION['ok_raza'] = "Raza eliminada correctamente.";
    } else {
        // Si falló la eliminación, guardamos un mensaje de error.
        $_SESSION['error_raza'] = "Error al eliminar la raza.";
    }

    mysqli_stmt_close($stmt);
}

// Cierra la conexión y redirige al listado de razas.
mysqli_close($conn);
header("Location: ../procesos/razas.php");
exit;
?>
