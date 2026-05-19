<?php
/*
  Script que inserta una nueva mascota en la base de datos.
  Valida los datos recibidos, guarda los valores en sesión si hay error y redirige apropiadamente.
*/
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../procesos/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../procesos/mascotas.php");
    exit;
}

$requeridos = ['chip', 'nombre', 'sexo', 'especie', 'fecha_nacimiento', 'raza_id', 'propietario_id', 'veterinario_id'];
foreach ($requeridos as $campo) {
    if (!isset($_POST[$campo])) {
        echo "Faltan datos en el formulario.";
        mysqli_close($conn);
        exit;
    }
}

$chip = trim($_POST['chip']);
$nombre = trim($_POST['nombre']);
$sexo = $_POST['sexo'];
$especie = trim($_POST['especie']);
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$raza_id = $_POST['raza_id'];
$propietario_id = $_POST['propietario_id'];
$veterinario_id = $_POST['veterinario_id'];

// Guardamos lo escrito en sesion para repintar el formulario si algo falla
$_SESSION['datos_mascota'] = [
    'chip' => $chip,
    'nombre' => $nombre,
    'sexo' => $sexo,
    'especie' => $especie,
    'fecha_nacimiento' => $fecha_nacimiento,
    'raza_id' => $raza_id,
    'propietario_id' => $propietario_id,
    'veterinario_id' => $veterinario_id,
];

if (empty($chip) || empty($nombre) || empty($sexo) || empty($especie) || empty($fecha_nacimiento) || empty($raza_id) || empty($propietario_id) || empty($veterinario_id)) {
    $_SESSION['error_mascota'] = "Todos los campos son obligatorios.";
    header("Location: ../procesos/agregar_mascota.php");
    exit;
}

// El chip son 15 digitos numericos (estandar ISO 11784)
if (strlen($chip) !== 15 || !ctype_digit($chip)) {
    $_SESSION['error_mascota'] = "El chip debe contener exactamente 15 digitos.";
    header("Location: ../procesos/agregar_mascota.php");
    exit;
}

if (!in_array($sexo, ['M', 'F'], true)) {
    $_SESSION['error_mascota'] = "El sexo seleccionado no es valido.";
    header("Location: ../procesos/agregar_mascota.php");
    exit;
}

// No tiene sentido aceptar fechas futuras
if (strtotime($fecha_nacimiento) > time()) {
    $_SESSION['error_mascota'] = "La fecha de nacimiento no puede ser futura.";
    header("Location: ../procesos/agregar_mascota.php");
    exit;
}

// Si ya hay una mascota con el mismo chip, avisamos antes de insertar
$checkSql = "SELECT chip FROM mascotas WHERE chip = ? LIMIT 1";
$checkStmt = mysqli_prepare($conn, $checkSql);
if ($checkStmt) {
    mysqli_stmt_bind_param($checkStmt, "s", $chip);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);

    if (mysqli_stmt_num_rows($checkStmt) > 0) {
        $_SESSION['error_mascota'] = "Ya existe una mascota con ese chip.";
        mysqli_stmt_close($checkStmt);
        mysqli_close($conn);
        header("Location: ../procesos/agregar_mascota.php");
        exit;
    }

    mysqli_stmt_close($checkStmt);
}

$sql = "INSERT INTO mascotas (chip, nombre, sexo, especie, fecha_nacimiento, raza_id, propietario_id, veterinario_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    $_SESSION['error_mascota'] = "Error al preparar la consulta.";
    header("Location: ../procesos/agregar_mascota.php");
    exit;
}

mysqli_stmt_bind_param($stmt, "sssssiii", $chip, $nombre, $sexo, $especie, $fecha_nacimiento, $raza_id, $propietario_id, $veterinario_id);

if (mysqli_stmt_execute($stmt)) {
    unset($_SESSION['datos_mascota']);
    $_SESSION['ok_mascota'] = "Mascota registrada correctamente.";
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header("Location: ../procesos/mascotas.php");
    exit;
}

// El chip es PRIMARY KEY UNIQUE: si se repite, MySQL devuelve 1062
// Asi evitamos la condicion de carrera del SELECT previo
if (mysqli_errno($conn) === 1062) {
    $_SESSION['error_mascota'] = "Ya existe una mascota con ese chip.";
} else {
    $_SESSION['error_mascota'] = "No se pudo registrar la mascota.";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
header("Location: ../procesos/agregar_mascota.php");
exit;
?>
