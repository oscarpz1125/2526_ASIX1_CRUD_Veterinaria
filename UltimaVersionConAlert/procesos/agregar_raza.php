<?php
/*
  Página para registrar una nueva raza.
  Verifica que el usuario esté autenticado antes de mostrar el formulario.
*/
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$error = isset($_SESSION['error_raza']) ? $_SESSION['error_raza'] : '';
unset($_SESSION['error_raza']);
$datos = isset($_SESSION['datos_raza']) ? $_SESSION['datos_raza'] : array();
unset($_SESSION['datos_raza']);

function valorAnterior($campo, $datos) {
    return isset($datos[$campo]) ? htmlspecialchars($datos[$campo]) : '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Raza - PerryatPerriatra</title>
    <link rel="stylesheet" href="../estilos/estilo.css">
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="contenedor-form">
    <h2> Nueva Raza</h2>
    <h3>Rellena los datos de la raza</h3>

    <?php if ($error !== ''): ?>
        <div class="error-general"><?= $error ?></div>
        <script>
            window.addEventListener('load', function() {
                alert(<?= json_encode($error) ?>);
            });
        </script>
    <?php endif; ?>

    <form action="../scripts/insertar_raza.php" method="POST">

        <label>Nombre de la raza</label>
        <input type="text" name="nombre" id="nombre" value="<?= valorAnterior('nombre', $datos) ?>" onblur="validarNombre()">
        <div class="error-campo" id="errorNombre"></div>

        <label>Peso (kg)</label>
        <input type="number" step="0.01" name="peso" id="peso" value="<?= valorAnterior('peso', $datos) ?>" onblur="validarPeso()">
        <div class="error-campo" id="errorPeso"></div>

        <label>Altura (cm)</label>
        <input type="number" step="0.01" name="altura" id="altura" value="<?= valorAnterior('altura', $datos) ?>" onblur="validarAltura()">
        <div class="error-campo" id="errorAltura"></div>

        <label>Temperamento <span style="color:#888; font-size:0.8rem">(opcional)</span></label>
        <input type="text" name="temperamento" id="temperamento" value="<?= valorAnterior('temperamento', $datos) ?>" onblur="validarTemperamento()">
        <div class="error-campo" id="errorTemperamento"></div>

        <button type="submit" onclick="return validarFormularioRaza()">Añadir Raza</button>

        <p><a href="razas.php">← Volver al listado</a></p>
    </form>
</div>

<script src="../js/validacion_raza.js"></script>
</body>
</html>
