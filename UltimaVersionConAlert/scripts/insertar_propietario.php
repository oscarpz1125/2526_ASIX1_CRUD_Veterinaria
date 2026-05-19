<?php
/*
  Script que guarda un propietario nuevo en la base de datos.
  Comprueba los datos obligatorios y redirige de nuevo al formulario si hay problemas.
*/
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../procesos/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../procesos/propietarios.php");
    exit;
}

if (!isset($_POST['nombre'], $_POST['email'], $_POST['telefono'], $_POST['direccion'])) {
    echo "Faltan datos en el formulario.";
    mysqli_close($conn);
    exit;
}

$nombre = trim($_POST['nombre']);
$email = trim($_POST['email']);
$telefono = trim($_POST['telefono']);
$direccion = trim($_POST['direccion']);

// Guardamos los datos en sesion para repintar si algo falla
$_SESSION['datos_propietario'] = [
    'nombre' => $nombre,
    'email' => $email,
    'telefono' => $telefono,
    'direccion' => $direccion,
];

if (empty($nombre) || empty($email) || empty($telefono)) {
    $_SESSION['error_propietario'] = "Nombre, email y telefono son obligatorios.";
    header("Location: ../procesos/agregar_propietario.php");
    exit;
}

if (strlen($nombre) < 3) {
    $_SESSION['error_propietario'] = "El nombre debe tener minimo 3 caracteres.";
    header("Location: ../procesos/agregar_propietario.php");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_propietario'] = "El formato del email no es valido.";
    header("Location: ../procesos/agregar_propietario.php");
    exit;
}

// El telefono es CHAR(9): 9 digitos exactos, nada de espacios ni guiones
if (!ctype_digit($telefono)) {
    $_SESSION['error_propietario'] = "El telefono solo puede contener numeros.";
    header("Location: ../procesos/agregar_propietario.php");
    exit;
}
if (strlen($telefono) !== 9) {
    $_SESSION['error_propietario'] = "El telefono debe tener exactamente 9 digitos.";
    header("Location: ../procesos/agregar_propietario.php");
    exit;
}

// La direccion es opcional, pero si la rellenan no admitimos abreviaturas raras
if (!empty($direccion) && strlen($direccion) < 3) {
    $_SESSION['error_propietario'] = "La direccion debe tener minimo 3 caracteres.";
    header("Location: ../procesos/agregar_propietario.php");
    exit;
}

// Comprobamos duplicados antes de insertar para mostrar un mensaje claro
$checkSql = "SELECT id FROM propietarios WHERE email = ? OR telefono = ? LIMIT 1";
$checkStmt = mysqli_prepare($conn, $checkSql);
if ($checkStmt) {
    mysqli_stmt_bind_param($checkStmt, "ss", $email, $telefono);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);

    if (mysqli_stmt_num_rows($checkStmt) > 0) {
        $_SESSION['error_propietario'] = "Ya existe un propietario con ese email o telefono.";
        mysqli_stmt_close($checkStmt);
        mysqli_close($conn);
        header("Location: ../procesos/agregar_propietario.php");
        exit;
    }

    mysqli_stmt_close($checkStmt);
}

$sql = "INSERT INTO propietarios (nombre, email, telefono, direccion) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    echo "Error en la preparacion de la consulta: " . mysqli_error($conn);
    mysqli_close($conn);
    exit;
}

mysqli_stmt_bind_param($stmt, "ssss", $nombre, $email, $telefono, $direccion);

if (mysqli_stmt_execute($stmt)) {
    unset($_SESSION['datos_propietario']);
    $_SESSION['ok_propietario'] = "Propietario anadido correctamente.";

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header("Location: ../procesos/propietarios.php");
    exit;
}

// La tabla propietarios no tiene UNIQUE, pero por homogeneidad cubrimos el 1062
if (mysqli_errno($conn) === 1062) {
    $_SESSION['error_propietario'] = "Ya existe un propietario con esos datos.";
} else {
    $_SESSION['error_propietario'] = "Error al insertar el propietario.";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
header("Location: ../procesos/agregar_propietario.php");
exit;
?>
