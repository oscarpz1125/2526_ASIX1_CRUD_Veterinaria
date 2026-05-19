<?php
/*
  Página para editar los datos de una mascota existente.
  Carga la mascota desde la base de datos usando el chip y precarga los campos en el formulario.
*/
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include '../includes/conexion.php';

// Sin chip en la URL no hay nada que editar
if (!isset($_GET['chip']) || empty($_GET['chip'])) {
    header("Location: mascotas.php");
    exit();
}

$chip = $_GET['chip'];

// Sentencia preparada para evitar SQL injection en el SELECT
$sql_buscar = "SELECT * FROM mascotas WHERE chip = ?";
$stmt_b = mysqli_prepare($conn, $sql_buscar);
mysqli_stmt_bind_param($stmt_b, "s", $chip);
mysqli_stmt_execute($stmt_b);
$resultado = mysqli_stmt_get_result($stmt_b);
$mascota = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt_b);

if (!$mascota) {
    die("Mascota no encontrada.");
}

// Catalogos para los desplegables
$res_razas = mysqli_query($conn, "SELECT id, nombre FROM razas ORDER BY nombre ASC");
$res_props = mysqli_query($conn, "SELECT id, nombre FROM propietarios ORDER BY nombre ASC");
$res_vets = mysqli_query($conn, "SELECT id, nombre FROM veterinarios ORDER BY nombre ASC");

$error = isset($_SESSION['error_mascota']) ? $_SESSION['error_mascota'] : '';
unset($_SESSION['error_mascota']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Mascota - PerryatPerriatra</title>
    <link rel="stylesheet" href="../estilos/estilo.css">
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="contenedor-form">
    <h2>Editar Mascota</h2>

    <?php if ($error !== ''): ?>
        <div class="error-general"><?= $error ?></div>
    <?php endif; ?>

    <form action="../scripts/actualizar_mascota.php" method="POST">

        <!-- Guardamos el chip original para poder localizar la fila aunque cambien el chip -->
        <input type="hidden" name="chip_original" value="<?= htmlspecialchars($mascota['chip']) ?>">

        <label>Chip</label>
        <input type="text" name="chip" id="chip" value="<?= htmlspecialchars($mascota['chip']) ?>" onblur="validarChip()" minlength="15" maxlength="15" inputmode="numeric">
        <div class="error-campo" id="errorChip"></div>

        <label>Nombre</label>
        <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($mascota['nombre']) ?>" onblur="validarNombre()">
        <div class="error-campo" id="errorNombre"></div>

        <label>Sexo</label>
        <select name="sexo" id="sexo" onblur="validarSexo()">
            <option value="">-- Elige sexo --</option>
            <option value="M" <?= $mascota['sexo'] === 'M' ? 'selected' : '' ?>>Macho</option>
            <option value="F" <?= $mascota['sexo'] === 'F' ? 'selected' : '' ?>>Hembra</option>
        </select>
        <div class="error-campo" id="errorSexo"></div>

        <label>Especie</label>
        <input type="text" name="especie" id="especie" value="<?= htmlspecialchars($mascota['especie']) ?>" onblur="validarEspecie()">
        <div class="error-campo" id="errorEspecie"></div>

        <label>Fecha de nacimiento</label>
        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" value="<?= htmlspecialchars($mascota['fecha_nacimiento']) ?>" onblur="validarFechaNac()">
        <div class="error-campo" id="errorFecha"></div>

        <label>Raza</label>
        <select name="raza_id" id="raza_id" onblur="validarRaza()">
            <option value="">-- Elige raza --</option>
            <?php while ($r = mysqli_fetch_assoc($res_razas)): ?>
                <option value="<?= $r['id'] ?>" <?= (int)$mascota['raza_id'] === (int)$r['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($r['nombre']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <div class="error-campo" id="errorRaza"></div>

        <label>Propietario</label>
        <select name="propietario_id" id="propietario_id" onblur="validarPropietario()">
            <option value="">-- Elige propietario --</option>
            <?php while ($p = mysqli_fetch_assoc($res_props)): ?>
                <option value="<?= $p['id'] ?>" <?= (int)$mascota['propietario_id'] === (int)$p['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['nombre']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <div class="error-campo" id="errorPropietario"></div>

        <label>Veterinario</label>
        <select name="veterinario_id" id="veterinario_id" onblur="validarVeterinario()">
            <option value="">-- Elige veterinario --</option>
            <?php while ($v = mysqli_fetch_assoc($res_vets)): ?>
                <option value="<?= $v['id'] ?>" <?= (int)$mascota['veterinario_id'] === (int)$v['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($v['nombre']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <div class="error-campo" id="errorVeterinario"></div>

        <button type="submit" onclick="return validarFormularioMascota()">Guardar cambios</button>

        <p><a href="mascotas.php">← Volver al listado</a></p>
    </form>
</div>

<script src="../js/validacion_mascota.js"></script>
</body>
</html>
