<?php
/*
  Página para crear un propietario nuevo.
  Comprueba sesión y muestra el formulario con datos previos cuando hay errores.
*/
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// Si el script de insertar rebota por error, recuperamos lo escrito
$error = isset($_SESSION['error_propietario']) ? $_SESSION['error_propietario'] : '';
unset($_SESSION['error_propietario']);
$datos = isset($_SESSION['datos_propietario']) ? $_SESSION['datos_propietario'] : array();
unset($_SESSION['datos_propietario']);

function valorAnterior($campo, $datos) {
    return isset($datos[$campo]) ? htmlspecialchars($datos[$campo]) : '';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Propietario - PerryatPerriatra</title>
    <link rel="stylesheet" href="../estilos/estilo.css">
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="contenedor-form">
    <h2>Nuevo Propietario</h2>
    <h3>Rellena los datos del propietario</h3>

    <?php if ($error !== ''): ?>
        <div class="error-general"><?= $error ?></div>
    <?php endif; ?>

    <form action="../scripts/insertar_propietario.php" method="POST">

        <label>Nombre completo</label>
        <input type="text" name="nombre" id="nombre" value="<?= valorAnterior('nombre', $datos) ?>" onblur="validarNombre()">
        <div class="error-campo" id="errorNombre"></div>

        <label>Email</label>
        <input type="email" name="email" id="email" value="<?= valorAnterior('email', $datos) ?>" onblur="validarEmail()">
        <div class="error-campo" id="errorEmail"></div>

        <label>Teléfono</label>
        <input type="text" name="telefono" id="telefono" maxlength="9" value="<?= valorAnterior('telefono', $datos) ?>" onblur="validarTelefono()">
        <div class="error-campo" id="errorTelefono"></div>

        <label>Dirección <span style="color:#888; font-size:0.8rem">(opcional)</span></label>
        <input type="text" name="direccion" id="direccion" value="<?= valorAnterior('direccion', $datos) ?>" onblur="validarDireccion()">
        <div class="error-campo" id="errorDireccion"></div>

        <button type="submit" onclick="return validarFormularioPropietario()">Añadir Propietario</button>

        <p><a href="propietarios.php">← Volver al listado</a></p>
    </form>
</div>

<script src="../js/validacion_propietario.js"></script>
</body>
</html>
