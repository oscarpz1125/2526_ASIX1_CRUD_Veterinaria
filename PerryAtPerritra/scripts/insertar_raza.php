<?php
/*
  Script que inserta una nueva raza.
  Valida el nombre, peso y altura antes de escribir en la base de datos.
*/
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../procesos/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../procesos/razas.php");
    exit;
}

if (!isset($_POST['nombre'], $_POST['peso'], $_POST['altura'], $_POST['temperamento'])) {
    echo "Faltan datos en el formulario.";
    mysqli_close($conn);
    exit;
}

$nombre = trim($_POST['nombre']);
$peso = trim($_POST['peso']);
$altura = trim($_POST['altura']);
$temperamento = trim($_POST['temperamento']);

// Datos en sesion para repintar el formulario si rebotamos
$_SESSION['datos_raza'] = [
    'nombre' => $nombre,
    'peso' => $peso,
    'altura' => $altura,
    'temperamento' => $temperamento,
];

if (empty($nombre) || $peso === '' || $altura === '') {
    $_SESSION['error_raza'] = "Nombre, peso y altura son obligatorios.";
    header("Location: ../procesos/agregar_raza.php");
    exit;
}

if (strlen($nombre) < 3) {
    $_SESSION['error_raza'] = "El nombre debe tener minimo 3 caracteres.";
    header("Location: ../procesos/agregar_raza.php");
    exit;
}

// Peso/altura aceptan decimales (DECIMAL(5,2) en BD); usamos is_numeric en PHP
if (!is_numeric($peso) || $peso <= 0) {
    $_SESSION['error_raza'] = "El peso debe ser un numero mayor que 0.";
    header("Location: ../procesos/agregar_raza.php");
    exit;
}

if (!is_numeric($altura) || $altura <= 0) {
    $_SESSION['error_raza'] = "La altura debe ser un numero mayor que 0.";
    header("Location: ../procesos/agregar_raza.php");
    exit;
}

if (!empty($temperamento) && strlen($temperamento) < 3) {
    $_SESSION['error_raza'] = "El temperamento debe tener minimo 3 caracteres.";
    header("Location: ../procesos/agregar_raza.php");
    exit;
}

// Comprobamos si ya existe la raza antes de insertar
$checkSql = "SELECT id FROM razas WHERE nombre = ? LIMIT 1";
$checkStmt = mysqli_prepare($conn, $checkSql);
if ($checkStmt) {
    mysqli_stmt_bind_param($checkStmt, "s", $nombre);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);

    if (mysqli_stmt_num_rows($checkStmt) > 0) {
        $_SESSION['error_raza'] = "El nombre de la raza ya esta registrado.";
        mysqli_stmt_close($checkStmt);
        mysqli_close($conn);
        header("Location: ../procesos/agregar_raza.php");
        exit;
    }

    mysqli_stmt_close($checkStmt);
}

$sql = "INSERT INTO razas (nombre, peso, altura, temperamento) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    echo "Error en la preparacion de la consulta: " . mysqli_error($conn);
    mysqli_close($conn);
    exit;
}

mysqli_stmt_bind_param($stmt, "sdds", $nombre, $peso, $altura, $temperamento);

if (mysqli_stmt_execute($stmt)) {
    unset($_SESSION['datos_raza']);
    $_SESSION['ok_raza'] = "Raza anadida correctamente.";

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header("Location: ../procesos/razas.php");
    exit;
}

// Solo el nombre es UNIQUE en razas, asi que un 1062 = nombre duplicado
if (mysqli_errno($conn) === 1062) {
    $_SESSION['error_raza'] = "El nombre de la raza ya esta registrado.";
} else {
    $_SESSION['error_raza'] = "Error al insertar la raza.";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
header("Location: ../procesos/agregar_raza.php");
exit;
?>
