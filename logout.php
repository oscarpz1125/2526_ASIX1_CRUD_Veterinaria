<?php
/*
  Script para cerrar sesión.
  Destruye la sesión actual y redirige al formulario de login.
*/
session_start();
session_destroy();

header("Location: procesos/login.php");
exit;
?>
