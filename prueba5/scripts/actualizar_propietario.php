<?php
/*
  Script que actualiza los datos de un propietario.
  Valida nombre, email, teléfono y dirección antes de aplicar los cambios.
*/
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../procesos/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../procesos/propietarios.php");
    exit;
}

if (!isset($_POST['id'], $_POST['nombre'], $_POST['email'], $_POST['telefono'], $_POST['direccion'])) {
    echo "Faltan datos en el formulario.";
    mysqli_close($conn);
    exit;
}

$id = $_POST['id'];
$nombre = trim($_POST['nombre']);
$email = trim($_POST['email']);
$telefono = trim($_POST['telefono']);
$direccion = trim($_POST['direccion']);

$url_editar = "../procesos/editar_propietario.php?id=$id";

if (empty($nombre) || empty($email) || empty($telefono)) {
    $_SESSION['error_propietario'] = "Nombre, email y telefono son obligatorios.";
    header("Location: $url_editar");
    exit;
}

if (strlen($nombre) < 3) {
    $_SESSION['error_propietario'] = "El nombre debe tener minimo 3 caracteres.";
    header("Location: $url_editar");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_propietario'] = "El formato del email no es valido.";
    header("Location: $url_editar");
    exit;
}

if (!ctype_digit($telefono)) {
    $_SESSION['error_propietario'] = "El telefono solo puede contener numeros.";
    header("Location: $url_editar");
    exit;
}
if (strlen($telefono) !== 9) {
    $_SESSION['error_propietario'] = "El telefono debe tener exactamente 9 digitos.";
    header("Location: $url_editar");
    exit;
}

if (!empty($direccion) && strlen($direccion) < 3) {
    $_SESSION['error_propietario'] = "La direccion debe tener minimo 3 caracteres.";
    header("Location: $url_editar");
    exit;
}

// No tocamos mascota_chip aqui: esa relacion se maneja en alta/baja de mascotas
$sql = "UPDATE propietarios SET nombre = ?, email = ?, telefono = ?, direccion = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    echo "Error en la preparacion de la consulta: " . mysqli_error($conn);
    mysqli_close($conn);
    exit;
}

mysqli_stmt_bind_param($stmt, "ssssi", $nombre, $email, $telefono, $direccion, $id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['ok_propietario'] = "Propietario actualizado correctamente.";
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header("Location: ../procesos/propietarios.php");
    exit;
}

// Sin UNIQUE en propietarios, pero cubrimos el 1062 por consistencia
if (mysqli_errno($conn) === 1062) {
    $_SESSION['error_propietario'] = "Ya existe otro propietario con esos datos.";
} else {
    $_SESSION['error_propietario'] = "Error al actualizar el propietario.";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
header("Location: $url_editar");
exit;
?>
