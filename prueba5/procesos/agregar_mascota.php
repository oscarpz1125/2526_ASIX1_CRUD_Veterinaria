<?php
/*
  Página para añadir una nueva mascota.
  Comprueba que el usuario esté conectado, carga los datos de los desplegables
  y muestra el formulario con los valores previos si hay errores.
*/
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// Sacamos los desplegables una sola vez para no consultar la BD en bucle
$res_razas = mysqli_query($conn, "SELECT id, nombre FROM razas ORDER BY nombre ASC");
$res_props = mysqli_query($conn, "SELECT id, nombre FROM propietarios ORDER BY nombre ASC");
$res_vets = mysqli_query($conn, "SELECT id, nombre FROM veterinarios ORDER BY nombre ASC");

$error = isset($_SESSION['error_mascota']) ? $_SESSION['error_mascota'] : '';
unset($_SESSION['error_mascota']);
$datos = isset($_SESSION['datos_mascota']) ? $_SESSION['datos_mascota'] : array();
unset($_SESSION['datos_mascota']);

// Helpers para repintar el formulario con los datos que el usuario habia metido
function valorAnterior($campo, $datos) {
    return isset($datos[$campo]) ? htmlspecialchars($datos[$campo]) : '';
}

function selSi($valor, $datos, $campo) {
    if (!isset($datos[$campo])) return '';
    return ((string)$datos[$campo] === (string)$valor) ? 'selected' : '';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Mascota - PerryatPerriatra</title>
    <link rel="stylesheet" href="../estilos/estilo.css">
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="contenedor-form">
    <h2> Nueva Mascota</h2>
    <h3>Rellena los datos de la mascota</h3>

    <?php if ($error !== ''): ?>
        <div class="error-general"><?= $error ?></div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($res_razas) == 0 || mysqli_num_rows($res_props) == 0 || mysqli_num_rows($res_vets) == 0): ?>
        <!-- Aviso al usuario: sin estos catalogos basicos no se puede dar de alta una mascota -->
        <div class="error-general">
            Antes de añadir una mascota necesitas tener al menos una raza, un propietario y un veterinario registrados.
        </div>
    <?php endif; ?>

    <form action="../scripts/insertar_mascota.php" method="POST">

        <label>Chip</label>
        <input type="text" name="chip" id="chip" value="<?= valorAnterior('chip', $datos) ?>" onblur="validarChip()" minlength="15" maxlength="15" inputmode="numeric">
        <div class="error-campo" id="errorChip"></div>

        <label>Nombre</label>
        <input type="text" name="nombre" id="nombre" value="<?= valorAnterior('nombre', $datos) ?>" onblur="validarNombre()">
        <div class="error-campo" id="errorNombre"></div>

        <label>Sexo</label>
        <select name="sexo" id="sexo" onblur="validarSexo()">
            <option value="">-- Elige sexo --</option>
            <option value="M" <?= selSi('M', $datos, 'sexo') ?>>Macho</option>
            <option value="F" <?= selSi('F', $datos, 'sexo') ?>>Hembra</option>
        </select>
        <div class="error-campo" id="errorSexo"></div>

        <label>Especie</label>
        <input type="text" name="especie" id="especie" value="<?= valorAnterior('especie', $datos) ?>" onblur="validarEspecie()">
        <div class="error-campo" id="errorEspecie"></div>

        <label>Fecha de nacimiento</label>
        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" value="<?= valorAnterior('fecha_nacimiento', $datos) ?>" onblur="validarFechaNac()">
        <div class="error-campo" id="errorFecha"></div>

        <label>Raza</label>
        <select name="raza_id" id="raza_id" onblur="validarRaza()">
            <option value="">-- Elige raza --</option>
            <?php while ($r = mysqli_fetch_assoc($res_razas)): ?>
                <option value="<?= $r['id'] ?>" <?= selSi($r['id'], $datos, 'raza_id') ?>>
                    <?= htmlspecialchars($r['nombre']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <div class="error-campo" id="errorRaza"></div>

        <label>Propietario</label>
        <select name="propietario_id" id="propietario_id" onblur="validarPropietario()">
            <option value="">-- Elige propietario --</option>
            <?php while ($p = mysqli_fetch_assoc($res_props)): ?>
                <option value="<?= $p['id'] ?>" <?= selSi($p['id'], $datos, 'propietario_id') ?>>
                    <?= htmlspecialchars($p['nombre']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <div class="error-campo" id="errorPropietario"></div>

        <label>Veterinario</label>
        <select name="veterinario_id" id="veterinario_id" onblur="validarVeterinario()">
            <option value="">-- Elige veterinario --</option>
            <?php while ($v = mysqli_fetch_assoc($res_vets)): ?>
                <option value="<?= $v['id'] ?>" <?= selSi($v['id'], $datos, 'veterinario_id') ?>>
                    <?= htmlspecialchars($v['nombre']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <div class="error-campo" id="errorVeterinario"></div>

        <button type="submit" onclick="return validarFormularioMascota()">Añadir Mascota</button>

        <p><a href="mascotas.php">← Volver al listado</a></p>
    </form>
</div>

<script src="../js/validacion_mascota.js"></script>
</body>
</html>
