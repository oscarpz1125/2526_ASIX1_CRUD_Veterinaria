<?php
/*
  Página para editar los datos de un propietario.
  Carga al propietario desde la base de datos y muestra un formulario con sus valores.
*/
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include '../includes/conexion.php';

if (!isset($_GET['id'])) {
    header("Location: propietarios.php");
    exit();
}

$id = (int) $_GET['id'];

// Statement preparado para que el id de la URL no se concatene a pelo en el SQL
$sql = "SELECT * FROM propietarios WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$propietario = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);

if (!$propietario) {
    die("Propietario no encontrado.");
}

$error = isset($_SESSION['error_propietario']) ? $_SESSION['error_propietario'] : '';
unset($_SESSION['error_propietario']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Propietario - PerryatPerriatra</title>
    <link rel="stylesheet" href="../estilos/estilo.css">
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="contenedor-form">
    <h2>Editar Propietario</h2>

    <?php if ($error !== ''): ?>
        <div class="error-general"><?= $error ?></div>
    <?php endif; ?>

    <form action="../scripts/actualizar_propietario.php" method="POST">

        <input type="hidden" name="id" value="<?= $propietario['id'] ?>">

        <label>Nombre completo</label>
        <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($propietario['nombre']) ?>" onblur="validarNombre()">
        <div class="error-campo" id="errorNombre"></div>

        <label>Email</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($propietario['email']) ?>" onblur="validarEmail()">
        <div class="error-campo" id="errorEmail"></div>

        <label>Teléfono</label>
        <input type="text" name="telefono" id="telefono" maxlength="9" value="<?= htmlspecialchars($propietario['telefono']) ?>" onblur="validarTelefono()">
        <div class="error-campo" id="errorTelefono"></div>

        <label>Dirección <span style="color:#888; font-size:0.8rem">(opcional)</span></label>
        <input type="text" name="direccion" id="direccion" value="<?= htmlspecialchars($propietario['direccion']) ?>" onblur="validarDireccion()">
        <div class="error-campo" id="errorDireccion"></div>

        <button type="submit" onclick="return validarFormularioPropietario()">Guardar cambios</button>

        <p><a href="propietarios.php">← Volver al listado</a></p>
    </form>
</div>

<script src="../js/validacion_propietario.js"></script>
</body>
</html>
