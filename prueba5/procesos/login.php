<?php
/*
  Página de inicio de sesión.
  Muestra el formulario para entrar con usuario y contraseña.
  Usa validación JavaScript para no enviar el formulario si falta algún dato.
*/
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PerryatPerriatra</title>
    <link rel="stylesheet" href="../estilos/estilo.css">
</head>
<body class="body-login">
<div class="contenedor-form">
    <h2>PerryatPerriatra</h2>
    <h3>Iniciar Sesion</h3>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-general"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <form action="../scripts/autenticacion.php" method="POST">
        <label>Usuario</label>
        <input type="text" name="usuario" id="usuario">
        <div class="error-campo" id="errorUsuario"></div>
        <label>Contrasena</label>
        <input type="password" name="password" id="password">
        <div class="error-campo" id="errorPassword"></div>
        <button type="submit" onclick="return validarLogin()">Entrar</button>
        <p><a href="registro.php">No tienes cuenta? Registrate</a></p>
    </form>
</div>
<script src="../js/validacion_login.js"></script>
</body>
</html>
