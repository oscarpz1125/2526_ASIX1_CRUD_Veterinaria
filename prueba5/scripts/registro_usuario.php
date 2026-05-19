<?php
/*
  Script que procesa el registro de un nuevo usuario.
  Valida los datos, cifra la contraseña y guarda el usuario en la base de datos.
*/
session_start();
include '../includes/conexion.php';

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    // Si entran por GET, fuera: el script no se renderiza solo
    header("Location: ../procesos/registro.php");
    exit;
}

if (!isset($_POST['usuario'], $_POST['email'], $_POST['password'], $_POST['confirm_password'])) {
    echo "Faltan datos en el formulario.";
    mysqli_close($conn);
    exit;
}

$usuario = trim($_POST['usuario']);
$email = trim($_POST['email']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

if (empty($usuario) || empty($email) || empty($password) || empty($confirm_password)) {
    $_SESSION['error'] = "Todos los campos son obligatorios.";
    header("Location: ../procesos/registro.php");
    exit;
}

if (strlen($usuario) < 3) {
    $_SESSION['error'] = "El nombre de usuario debe tener minimo 3 caracteres.";
    header("Location: ../procesos/registro.php");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "El formato del email no es valido.";
    header("Location: ../procesos/registro.php");
    exit;
}

// Politica de contrasena: longitud + al menos una mayuscula y un digito
if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
    $_SESSION['error'] = "La contrasena debe tener minimo 8 caracteres, una mayuscula y un numero.";
    header("Location: ../procesos/registro.php");
    exit;
}

if ($password !== $confirm_password) {
    $_SESSION['error'] = "Las contrasenas no coinciden.";
    header("Location: ../procesos/registro.php");
    exit;
}

// Nunca guardamos la contrasena en plano
$pass_hash = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    echo "Error en la preparacion de la consulta: " . mysqli_error($conn);
    mysqli_close($conn);
    exit;
}

mysqli_stmt_bind_param($stmt, "sss", $usuario, $email, $pass_hash);

if (mysqli_stmt_execute($stmt)) {
    // Login automatico tras registrarse correctamente
    $id = mysqli_insert_id($conn);
    $_SESSION['usuario'] = $usuario;
    $_SESSION['usuario_id'] = $id;

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header("Location: ../index.php");
    exit;
}

// Si llegamos aqui, MySQL ha rechazado el INSERT. El caso mas habitual
// es chocar contra la restriccion UNIQUE del email (codigo 1062).
if (mysqli_errno($conn) === 1062) {
    $_SESSION['error'] = "El email ya se encuentra registrado.";
} else {
    $_SESSION['error'] = "No se pudo completar el registro. Intentalo de nuevo.";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
header("Location: ../procesos/registro.php");
exit;
?>
