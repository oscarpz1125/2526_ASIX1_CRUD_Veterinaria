<?php
// Detectamos si estamos dentro de /procesos para ajustar las rutas relativas
$ruta_actual = $_SERVER['SCRIPT_NAME'];
$en_subcarpeta = (strpos($ruta_actual, '/procesos/') !== false);
$base = $en_subcarpeta ? '../' : '';
?>

<div class="cabecera">

    <!-- Click en el logo siempre vuelve al dashboard -->
    <div class="cabecera-logo">
        <a href="<?= $base ?>index.php" class="logo-link">
            <h1>PerryatPerriatra</h1>
        </a>
    </div>

    <nav class="nav-menu">
        <a href="<?= $base ?>inicio.php">Inicio</a>
        <a href="<?= $base ?>procesos/propietarios.php">Propietarios</a>
        <a href="<?= $base ?>procesos/mascotas.php">Mascotas</a>
        <a href="<?= $base ?>procesos/veterinarios.php">Veterinarios</a>
        <a href="<?= $base ?>procesos/razas.php">Razas</a>
    </nav>

    <div class="info-usuario">
        <span>Hola, <strong><?= htmlspecialchars($_SESSION['usuario']) ?></strong></span>
        <a href="<?= $base ?>logout.php" class="btn-logout">Cerrar Sesion</a>
    </div>

</div>
