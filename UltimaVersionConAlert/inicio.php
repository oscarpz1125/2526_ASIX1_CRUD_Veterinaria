<?php
/*
  Pantalla de bienvenida del sistema.
  Muestra una introducción al usuario y describe brevemente los servicios.
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
    <title>Inicio - PerryatPerriatra</title>
    <link rel="stylesheet" href="estilos/estilo.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="pagina-inicio">
    <h2>Bienvenido a PerryatPerriatra</h2>

    <img src="estilos/img/veterinaria.jpg" alt="Nuestra veterinaria" class="foto-veterinaria">

    <p>
        PerryatPerriatra es una clinica veterinaria especializada en el cuidado
        y bienestar de tus mascotas. Contamos con un equipo de profesionales
        apasionados por los animales, listos para atender a tu peludito
        con todo el amor y la experiencia que se merece.
    </p>
    <p>
        Nuestros servicios incluyen consultas generales, vacunacion,
        cirugia, odontologia veterinaria y mucho mas. Tu mascota
        esta en las mejores manos.
    </p>
    <p>
        Usa el menu de arriba para gestionar propietarios, mascotas,
        veterinarios y razas.
    </p>
</div>

</body>
</html>
