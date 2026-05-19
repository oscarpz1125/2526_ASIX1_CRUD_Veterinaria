<?php
/*
  Página de registro de usuarios.
  Permite crear una cuenta nueva y valida los datos antes de enviarlos.
*/
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - PerryatPerriatra</title>
    <link rel="stylesheet" href="../estilos/estilo.css">
</head>
<body>
<div class="contenedor-form">
    <h2>PerryatPerriatra</h2>
    <h3>Crear Cuenta</h3>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-general"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <form action="../scripts/registro_usuario.php" method="POST">
        <label>Nombre de usuario</label>
        <input type="text" name="usuario" id="usuario" onblur="validarUsuario()">
        <div class="error-campo" id="errorUsuario"></div>
        <label>Email</label>
        <input type="email" name="email" id="email" onblur="validarEmail()">
        <div class="error-campo" id="errorEmail"></div>
        <label>Contrasena (min 8 caracteres, una mayuscula y un numero)</label>
        <input type="password" name="password" id="password" onblur="validarPassword()">
        <div class="error-campo" id="errorPassword"></div>
        <label>Confirmar contrasena</label>
        <input type="password" name="confirm_password" id="confirm_password" onblur="validarConfirm()">
        <div class="error-campo" id="errorConfirm"></div>
        <button type="submit" onclick="return validarFormularioRegistro()">Registrarse</button>
        <p><a href="login.php">Volver a login</a></p>
    </form>
</div>
<script src="../js/validacion_registro.js"></script>
</body>
</html>
