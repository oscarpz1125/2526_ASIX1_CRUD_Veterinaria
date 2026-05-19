<?php
/*
  Script que actualiza una raza existente.
  Revisa que el nombre, peso y altura sean válidos antes de actualizar.
*/
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../procesos/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../procesos/razas.php");
    exit;
}

if (!isset($_POST['id'], $_POST['nombre'], $_POST['peso'], $_POST['altura'], $_POST['temperamento'])) {
    echo "Faltan datos en el formulario.";
    mysqli_close($conn);
    exit;
}

$id = $_POST['id'];
$nombre = trim($_POST['nombre']);
$peso = trim($_POST['peso']);
$altura = trim($_POST['altura']);
$temperamento = trim($_POST['temperamento']);

$url_editar = "../procesos/editar_raza.php?id=$id";

if (empty($nombre) || $peso === '' || $altura === '') {
    $_SESSION['error_raza'] = "Nombre, peso y altura son obligatorios.";
    header("Location: $url_editar");
    exit;
}

if (strlen($nombre) < 3) {
    $_SESSION['error_raza'] = "El nombre debe tener minimo 3 caracteres.";
    header("Location: $url_editar");
    exit;
}

if (!is_numeric($peso) || $peso <= 0) {
    $_SESSION['error_raza'] = "El peso debe ser un numero mayor que 0.";
    header("Location: $url_editar");
    exit;
}

if (!is_numeric($altura) || $altura <= 0) {
    $_SESSION['error_raza'] = "La altura debe ser un numero mayor que 0.";
    header("Location: $url_editar");
    exit;
}

if (!empty($temperamento) && strlen($temperamento) < 3) {
    $_SESSION['error_raza'] = "El temperamento debe tener minimo 3 caracteres.";
    header("Location: $url_editar");
    exit;
}

$sql = "UPDATE razas SET nombre = ?, peso = ?, altura = ?, temperamento = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    echo "Error en la preparacion de la consulta: " . mysqli_error($conn);
    mysqli_close($conn);
    exit;
}

mysqli_stmt_bind_param($stmt, "sddsi", $nombre, $peso, $altura, $temperamento, $id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['ok_raza'] = "Raza actualizada correctamente.";
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header("Location: ../procesos/razas.php");
    exit;
}

// El nombre tiene UNIQUE: si choca, decimos exactamente que pasa
if (mysqli_errno($conn) === 1062) {
    $_SESSION['error_raza'] = "El nombre de la raza ya esta registrado.";
} else {
    $_SESSION['error_raza'] = "Error al actualizar la raza.";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
header("Location: $url_editar");
exit;
?>
