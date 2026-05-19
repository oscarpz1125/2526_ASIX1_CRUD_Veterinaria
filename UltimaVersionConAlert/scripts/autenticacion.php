<?php
/*
  Script que procesa el login del usuario.
  Recibe usuario y contraseña, verifica el hash y crea la sesión si todo es correcto.
*/
session_start();
include '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: ../procesos/login.php");
    exit;
}

if (!isset($_POST['usuario'], $_POST['password'])) {
    echo "Faltan datos en el formulario.";
    mysqli_close($conn);
    exit;
}

$usuario = $_POST['usuario'];
$password = $_POST['password'];

if (empty($usuario) || empty($password)) {
    $_SESSION['error'] = "Todos los campos son obligatorios.";
    header("Location: ../procesos/login.php");
    exit;
}

// Buscamos por nombre y verificamos despues con password_verify contra el hash
$sql = "SELECT id, nombre, password FROM usuarios WHERE nombre = ?";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    echo "Error en la preparacion de la consulta: " . mysqli_error($conn);
    mysqli_close($conn);
    exit;
}

mysqli_stmt_bind_param($stmt, "s", $usuario);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($resultado) === 0) {
    // No revelamos si el fallo es por usuario o por contrasena: misma respuesta
    $_SESSION['error'] = "El usuario no existe.";
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header("Location: ../procesos/login.php");
    exit;
}

$fila = mysqli_fetch_assoc($resultado);
$hash_db = $fila['password'];

if (!password_verify($password, $hash_db)) {
    $_SESSION['error'] = "Contrasena incorrecta.";
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header("Location: ../procesos/login.php");
    exit;
}

// Todo OK: arrancamos sesion
$_SESSION['usuario'] = $fila['nombre'];
$_SESSION['usuario_id'] = $fila['id'];

mysqli_stmt_close($stmt);
mysqli_close($conn);
header("Location: ../index.php");
exit;
?>
