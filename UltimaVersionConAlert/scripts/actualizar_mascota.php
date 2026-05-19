<?php
/*
  Script que actualiza los datos de una mascota existente.
  Comprueba los campos enviados y actualiza el registro usando statement preparado.
*/
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../procesos/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../procesos/mascotas.php");
    exit;
}

$requeridos = ['chip_original', 'chip', 'nombre', 'sexo', 'especie', 'fecha_nacimiento', 'raza_id', 'propietario_id', 'veterinario_id'];
foreach ($requeridos as $campo) {
    if (!isset($_POST[$campo])) {
        echo "Faltan datos en el formulario.";
        mysqli_close($conn);
        exit;
    }
}

$chip_original = $_POST['chip_original'];
$chip = trim($_POST['chip']);
$nombre = trim($_POST['nombre']);
$sexo = $_POST['sexo'];
$especie = trim($_POST['especie']);
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$raza_id = $_POST['raza_id'];
$propietario_id = $_POST['propietario_id'];
$veterinario_id = $_POST['veterinario_id'];

// Si rebotamos por error, volvemos a la pagina de edicion del chip original
$url_editar = "../procesos/editar_mascota.php?chip=" . urlencode($chip_original);

if (empty($chip) || empty($nombre) || empty($sexo) || empty($especie) || empty($fecha_nacimiento) || empty($raza_id) || empty($propietario_id) || empty($veterinario_id)) {
    $_SESSION['error_mascota'] = "Todos los campos son obligatorios.";
    header("Location: $url_editar");
    exit;
}

if (strlen($chip) < 15) {
    $_SESSION['error_mascota'] = "El chip debe tener minimo 15 caracteres.";
    header("Location: $url_editar");
    exit;
}

if (!ctype_digit($chip)) {
    $_SESSION['error_mascota'] = "El chip solo puede contener numeros.";
    header("Location: $url_editar");
    exit;
}

if (strlen($nombre) < 3) {
    $_SESSION['error_mascota'] = "El nombre debe tener minimo 3 caracteres.";
    header("Location: $url_editar");
    exit;
}

if ($sexo !== 'M' && $sexo !== 'F') {
    $_SESSION['error_mascota'] = "El sexo debe ser Macho o Hembra.";
    header("Location: $url_editar");
    exit;
}

if (strlen($especie) < 3) {
    $_SESSION['error_mascota'] = "La especie debe tener minimo 3 caracteres.";
    header("Location: $url_editar");
    exit;
}

if (strtotime($fecha_nacimiento) > time()) {
    $_SESSION['error_mascota'] = "La fecha de nacimiento no puede ser futura.";
    header("Location: $url_editar");
    exit;
}

// OJO: el chip es PK y esta referenciado desde propietarios. Si cambia,
// hay que actualizar la FK *despues* del UPDATE de la mascota.
$sql = "UPDATE mascotas SET chip = ?, nombre = ?, sexo = ?, especie = ?, fecha_nacimiento = ?, raza_id = ?, propietario_id = ?, veterinario_id = ? WHERE chip = ?";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    echo "Error en la preparacion de la consulta: " . mysqli_error($conn);
    mysqli_close($conn);
    exit;
}

mysqli_stmt_bind_param($stmt, "sssssiiis", $chip, $nombre, $sexo, $especie, $fecha_nacimiento, $raza_id, $propietario_id, $veterinario_id, $chip_original);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);

    // Si han cambiado el chip, sincronizamos la referencia en propietarios
    if ($chip !== $chip_original) {
        $sql_p = "UPDATE propietarios SET mascota_chip = ? WHERE mascota_chip = ?";
        $stmt_p = mysqli_prepare($conn, $sql_p);
        if ($stmt_p) {
            mysqli_stmt_bind_param($stmt_p, "ss", $chip, $chip_original);
            mysqli_stmt_execute($stmt_p);
            mysqli_stmt_close($stmt_p);
        }
    }

    $_SESSION['ok_mascota'] = "Mascota actualizada correctamente.";
    mysqli_close($conn);
    header("Location: ../procesos/mascotas.php");
    exit;
}

if (mysqli_errno($conn) === 1062) {
    $_SESSION['error_mascota'] = "Ya existe otra mascota con ese chip.";
} else {
    $_SESSION['error_mascota'] = "Error al actualizar la mascota.";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
header("Location: $url_editar");
exit;
?>
