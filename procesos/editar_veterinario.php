<?php
/*
  Página para editar los datos de un veterinario.
  Carga el registro seleccionado y lo muestra en el formulario para su edición.
*/
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include '../includes/conexion.php';

if (!isset($_GET['id'])) {
    header("Location: veterinarios.php");
    exit();
}

$id = (int) $_GET['id'];

$sql = "SELECT * FROM veterinarios WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$veterinario = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);

if (!$veterinario) {
    die("Veterinario no encontrado.");
}

$error = isset($_SESSION['error_veterinario']) ? $_SESSION['error_veterinario'] : '';
unset($_SESSION['error_veterinario']);
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Veterinario - PerryatPerriatra</title>
    <link rel="stylesheet" href="../estilos/estilo.css">
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="contenedor-form">
    <h2>Editar Veterinario</h2>

    <?php if ($error !== ''): ?>
        <div class="error-general"><?= $error ?></div>
    <?php endif; ?>

    <form action="../scripts/actualizar_veterinario.php" method="POST">

        <input type="hidden" name="id" value="<?= $veterinario['id'] ?>">

        <label>Nombre completo</label>
        <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($veterinario['nombre']) ?>" onblur="validarNombre()">
        <div class="error-campo" id="errorNombre"></div>

        <label>Email</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($veterinario['email']) ?>" onblur="validarEmail()">
        <div class="error-campo" id="errorEmail"></div>

        <label>Teléfono</label>
        <input type="text" name="telefono" id="telefono" maxlength="9" value="<?= htmlspecialchars($veterinario['telefono']) ?>" onblur="validarTelefono()">
        <div class="error-campo" id="errorTelefono"></div>

        <label>Especialidad</label>
        <input type="text" name="especialidad" id="especialidad" value="<?= htmlspecialchars($veterinario['especialidad']) ?>" onblur="validarEspecialidad()">
        <div class="error-campo" id="errorEspecialidad"></div>

        <label>Salario (€)</label>
        <input type="number" step="0.01" name="salario" id="salario" value="<?= htmlspecialchars($veterinario['salario']) ?>" onblur="validarSalario()">
        <div class="error-campo" id="errorSalario"></div>

        <button type="submit" onclick="return validarFormularioVeterinario()">Guardar cambios</button>

        <p><a href="veterinarios.php">← Volver al listado</a></p>
    </form>
</div>

<script src="../js/validacion_veterinario.js"></script>
</body>
</html>
