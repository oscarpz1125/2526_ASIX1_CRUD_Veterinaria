<?php
/*
  Script que actualiza un veterinario.
  Valida todos los campos y guarda los cambios en la base de datos.
*/
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../procesos/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../procesos/veterinarios.php");
    exit;
}

if (!isset($_POST['id'], $_POST['nombre'], $_POST['email'], $_POST['telefono'], $_POST['especialidad'], $_POST['salario'])) {
    echo "Faltan datos en el formulario.";
    mysqli_close($conn);
    exit;
}

$id = $_POST['id'];
$nombre = trim($_POST['nombre']);
$email = trim($_POST['email']);
$telefono = trim($_POST['telefono']);
$especialidad = trim($_POST['especialidad']);
$salario = trim($_POST['salario']);

$url_editar = "../procesos/editar_veterinario.php?id=$id";

if (empty($nombre) || empty($email) || empty($telefono) || empty($especialidad) || $salario === '') {
    $_SESSION['error_veterinario'] = "Todos los campos son obligatorios.";
    header("Location: $url_editar");
    exit;
}

if (strlen($nombre) < 3) {
    $_SESSION['error_veterinario'] = "El nombre debe tener minimo 3 caracteres.";
    header("Location: $url_editar");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_veterinario'] = "El formato del email no es valido.";
    header("Location: $url_editar");
    exit;
}

if (!ctype_digit($telefono)) {
    $_SESSION['error_veterinario'] = "El telefono solo puede contener numeros.";
    header("Location: $url_editar");
    exit;
}
if (strlen($telefono) !== 9) {
    $_SESSION['error_veterinario'] = "El telefono debe tener exactamente 9 digitos.";
    header("Location: $url_editar");
    exit;
}

if (strlen($especialidad) < 3) {
    $_SESSION['error_veterinario'] = "La especialidad debe tener minimo 3 caracteres.";
    header("Location: $url_editar");
    exit;
}

if (!is_numeric($salario) || $salario <= 0) {
    $_SESSION['error_veterinario'] = "El salario debe ser un numero mayor que 0.";
    header("Location: $url_editar");
    exit;
}

$sql = "UPDATE veterinarios SET nombre = ?, email = ?, telefono = ?, especialidad = ?, salario = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    echo "Error en la preparacion de la consulta: " . mysqli_error($conn);
    mysqli_close($conn);
    exit;
}

mysqli_stmt_bind_param($stmt, "ssssdi", $nombre, $email, $telefono, $especialidad, $salario, $id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['ok_veterinario'] = "Veterinario actualizado correctamente.";
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header("Location: ../procesos/veterinarios.php");
    exit;
}

// Email y telefono son UNIQUE. Miramos el mensaje del driver para precisar
if (mysqli_errno($conn) === 1062) {
    $mensaje_mysql = mysqli_error($conn);
    if (strpos($mensaje_mysql, 'telefono') !== false) {
        $_SESSION['error_veterinario'] = "El telefono ya se encuentra registrado.";
    } elseif (strpos($mensaje_mysql, 'email') !== false) {
        $_SESSION['error_veterinario'] = "El email ya se encuentra registrado.";
    } else {
        $_SESSION['error_veterinario'] = "Ya existe otro veterinario con email o telefono duplicado.";
    }
} else {
    $_SESSION['error_veterinario'] = "Error al actualizar el veterinario.";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
header("Location: $url_editar");
exit;
?>
