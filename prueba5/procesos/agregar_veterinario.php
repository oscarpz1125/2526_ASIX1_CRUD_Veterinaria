<?php
/*
  Página para añadir un nuevo veterinario.
  Solo se accede si el usuario ya ha iniciado sesión.
*/
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$error = isset($_SESSION['error_veterinario']) ? $_SESSION['error_veterinario'] : '';
unset($_SESSION['error_veterinario']);
$datos = isset($_SESSION['datos_veterinario']) ? $_SESSION['datos_veterinario'] : array();
unset($_SESSION['datos_veterinario']);

function valorAnterior($campo, $datos) {
    return isset($datos[$campo]) ? htmlspecialchars($datos[$campo]) : '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Veterinario - PerryatPerriatra</title>
    <link rel="stylesheet" href="../estilos/estilo.css">
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="contenedor-form">
    <h2>Nuevo Veterinario</h2>
    <h3>Rellena los datos del veterinario</h3>

    <?php if ($error !== ''): ?>
        <div class="error-general"><?= $error ?></div>
    <?php endif; ?>

    <form action="../scripts/insertar_veterinario.php" method="POST">

        <label>Nombre completo</label>
        <input type="text" name="nombre" id="nombre" value="<?= valorAnterior('nombre', $datos) ?>" onblur="validarNombre()">
        <div class="error-campo" id="errorNombre"></div>

        <label>Email</label>
        <input type="email" name="email" id="email" value="<?= valorAnterior('email', $datos) ?>" onblur="validarEmail()">
        <div class="error-campo" id="errorEmail"></div>

        <label>Teléfono</label>
        <input type="text" name="telefono" id="telefono" maxlength="9" value="<?= valorAnterior('telefono', $datos) ?>" onblur="validarTelefono()">
        <div class="error-campo" id="errorTelefono"></div>

        <label>Especialidad</label>
        <input type="text" name="especialidad" id="especialidad" value="<?= valorAnterior('especialidad', $datos) ?>" onblur="validarEspecialidad()">
        <div class="error-campo" id="errorEspecialidad"></div>

        <label>Salario (€)</label>
        <input type="number" step="0.01" name="salario" id="salario" value="<?= valorAnterior('salario', $datos) ?>" onblur="validarSalario()">
        <div class="error-campo" id="errorSalario"></div>

        <button type="submit" onclick="return validarFormularioVeterinario()">Añadir Veterinario</button>

        <p><a href="veterinarios.php">← Volver al listado</a></p>
    </form>
</div>

<script src="../js/validacion_veterinario.js"></script>
</body>
</html>
