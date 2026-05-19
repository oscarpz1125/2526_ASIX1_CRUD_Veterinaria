<?php
/*
  Script que guarda un veterinario nuevo.
  Valida todos los campos obligatorios y evita duplicados de email o teléfono.
*/
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../procesos/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../procesos/veterinarios.php");
    exit;
}

if (!isset($_POST['nombre'], $_POST['email'], $_POST['telefono'], $_POST['especialidad'], $_POST['salario'])) {
    echo "Faltan datos en el formulario.";
    mysqli_close($conn);
    exit;
}

$nombre = trim($_POST['nombre']);
$email = trim($_POST['email']);
$telefono = trim($_POST['telefono']);
$especialidad = trim($_POST['especialidad']);
$salario = trim($_POST['salario']);

$_SESSION['datos_veterinario'] = [
    'nombre' => $nombre,
    'email' => $email,
    'telefono' => $telefono,
    'especialidad' => $especialidad,
    'salario' => $salario,
];

if (empty($nombre) || empty($email) || empty($telefono) || empty($especialidad) || $salario === '') {
    $_SESSION['error_veterinario'] = "Todos los campos son obligatorios.";
    header("Location: ../procesos/agregar_veterinario.php");
    exit;
}

if (strlen($nombre) < 3) {
    $_SESSION['error_veterinario'] = "El nombre debe tener minimo 3 caracteres.";
    header("Location: ../procesos/agregar_veterinario.php");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_veterinario'] = "El formato del email no es valido.";
    header("Location: ../procesos/agregar_veterinario.php");
    exit;
}

if (!ctype_digit($telefono)) {
    $_SESSION['error_veterinario'] = "El telefono solo puede contener numeros.";
    header("Location: ../procesos/agregar_veterinario.php");
    exit;
}
if (strlen($telefono) !== 9) {
    $_SESSION['error_veterinario'] = "El telefono debe tener exactamente 9 digitos.";
    header("Location: ../procesos/agregar_veterinario.php");
    exit;
}

if (strlen($especialidad) < 3) {
    $_SESSION['error_veterinario'] = "La especialidad debe tener minimo 3 caracteres.";
    header("Location: ../procesos/agregar_veterinario.php");
    exit;
}

if (!is_numeric($salario) || $salario <= 0) {
    $_SESSION['error_veterinario'] = "El salario debe ser un numero mayor que 0.";
    header("Location: ../procesos/agregar_veterinario.php");
    exit;
}

$sql = "INSERT INTO veterinarios (nombre, email, telefono, especialidad, salario) VALUES (?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    echo "Error en la preparacion de la consulta: " . mysqli_error($conn);
    mysqli_close($conn);
    exit;
}

mysqli_stmt_bind_param($stmt, "ssssd", $nombre, $email, $telefono, $especialidad, $salario);

if (mysqli_stmt_execute($stmt)) {
    unset($_SESSION['datos_veterinario']);
    $_SESSION['ok_veterinario'] = "Veterinario anadido correctamente.";

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header("Location: ../procesos/veterinarios.php");
    exit;
}

// En veterinarios tenemos dos UNIQUE (email y telefono): miramos el mensaje
// de MySQL para decirle al usuario exactamente cual ha chocado.
if (mysqli_errno($conn) === 1062) {
    $mensaje_mysql = mysqli_error($conn);
    if (strpos($mensaje_mysql, 'telefono') !== false) {
        $_SESSION['error_veterinario'] = "El telefono ya se encuentra registrado.";
    } elseif (strpos($mensaje_mysql, 'email') !== false) {
        $_SESSION['error_veterinario'] = "El email ya se encuentra registrado.";
    } else {
        $_SESSION['error_veterinario'] = "Ya existe un veterinario con email o telefono duplicado.";
    }
} else {
    $_SESSION['error_veterinario'] = "Error al insertar el veterinario.";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
header("Location: ../procesos/agregar_veterinario.php");
exit;
?>
