<?php
/*
  Página para editar la información de una raza existente.
  Usa un statement preparado para cargar la raza de forma segura.
*/
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include '../includes/conexion.php';

if (!isset($_GET['id'])) {
    header("Location: razas.php");
    exit();
}

$id = (int) $_GET['id'];

$sql = "SELECT * FROM razas WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$raza = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);

if (!$raza) {
    die("Raza no encontrada.");
}

$error = isset($_SESSION['error_raza']) ? $_SESSION['error_raza'] : '';
unset($_SESSION['error_raza']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Raza - PerryatPerriatra</title>
    <link rel="stylesheet" href="../estilos/estilo.css">
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="contenedor-form">
    <h2>Editar Raza</h2>

    <?php if ($error !== ''): ?>
        <div class="error-general"><?= $error ?></div>
    <?php endif; ?>

    <form action="../scripts/actualizar_raza.php" method="POST">

        <input type="hidden" name="id" value="<?= $raza['id'] ?>">

        <label>Nombre de la raza</label>
        <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($raza['nombre']) ?>" onblur="validarNombre()">
        <div class="error-campo" id="errorNombre"></div>

        <label>Peso (kg)</label>
        <input type="number" step="0.01" name="peso" id="peso" value="<?= htmlspecialchars($raza['peso']) ?>" onblur="validarPeso()">
        <div class="error-campo" id="errorPeso"></div>

        <label>Altura (cm)</label>
        <input type="number" step="0.01" name="altura" id="altura" value="<?= htmlspecialchars($raza['altura']) ?>" onblur="validarAltura()">
        <div class="error-campo" id="errorAltura"></div>

        <label>Temperamento <span style="color:#888; font-size:0.8rem">(opcional)</span></label>
        <input type="text" name="temperamento" id="temperamento" value="<?= htmlspecialchars($raza['temperamento']) ?>" onblur="validarTemperamento()">
        <div class="error-campo" id="errorTemperamento"></div>

        <button type="submit" onclick="return validarFormularioRaza()">Guardar cambios</button>

        <p><a href="razas.php">← Volver al listado</a></p>
    </form>
</div>

<script src="../js/validacion_raza.js"></script>
</body>
</html>
