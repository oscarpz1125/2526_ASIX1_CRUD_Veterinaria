<?php
/*
  Página principal de la aplicación.
  Solo es accesible con sesión iniciada y enlaza con las secciones principales.
*/
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: procesos/login.php");
    exit;
}
$usuario = $_SESSION['usuario'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PerryatPerriatra</title>
    <link rel="stylesheet" href="estilos/estilo.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="bienvenida">
    <h2>Hola, <?= htmlspecialchars($usuario) ?>!</h2>
    <p>Bienvenido a PerryatPerriatra. Elige una seccion para gestionar:</p>
    <div class="botones-crud">
        <a href="procesos/propietarios.php" class="btn-crud">Propietarios</a>
        <a href="procesos/mascotas.php" class="btn-crud">Mascotas</a>
        <a href="procesos/veterinarios.php" class="btn-crud">Veterinarios</a>
        <a href="procesos/razas.php" class="btn-crud">Razas</a>
    </div>
</div>

</body>
</html>
