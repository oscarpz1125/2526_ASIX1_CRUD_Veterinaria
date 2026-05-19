<?php
/*
  Página de listado y alta de razas.
  Muestra las razas disponibles y permite agregar una nueva raza.
*/
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include '../includes/conexion.php';

// Mensajes flash que dejan los scripts
$error = isset($_SESSION['error_raza']) ? $_SESSION['error_raza'] : '';
$ok = isset($_SESSION['ok_raza']) ? $_SESSION['ok_raza'] : '';
unset($_SESSION['error_raza'], $_SESSION['ok_raza']);

$datos = isset($_SESSION['datos_raza']) ? $_SESSION['datos_raza'] : array();
unset($_SESSION['datos_raza']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Razas - PerryatPerriatra</title>
    <link rel="stylesheet" href="../estilos/estilo.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="contenido">
    <div class="seccion-tabla">
        <h2>Razas</h2>

        <?php if ($error !== ''): ?><div class="error-general"><?= $error ?></div><?php endif; ?>
        <?php if ($ok !== ''): ?><div class="ok-general"><?= $ok ?></div><?php endif; ?>

        <?php
            $sql = "SELECT id, nombre, peso, altura, temperamento FROM razas ORDER BY id DESC";
            $resultado = mysqli_query($conn, $sql);

            if ($resultado) {
                $razas = mysqli_fetch_all($resultado, MYSQLI_ASSOC);

                echo "<table class='tabla-datos'>";
                echo "<thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Peso (kg)</th>
                            <th>Altura (cm)</th>
                            <th>Temperamento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>";
                echo "<tbody>";

                if (count($razas) > 0) {
                    foreach ($razas as $fila) {
                        $nombre = htmlspecialchars($fila['nombre']);
                        // Formato europeo: coma decimal, punto de millares
                        $peso = number_format($fila['peso'], 2, ',', '.');
                        $altura = number_format($fila['altura'], 2, ',', '.');
                        $temperamento = $fila['temperamento'] !== '' ? htmlspecialchars($fila['temperamento']) : '-';

                        echo "<tr>
                            <td>{$nombre}</td>
                            <td>{$peso}</td>
                            <td>{$altura}</td>
                            <td>{$temperamento}</td>
                            <td>
                                <a href='editar_raza.php?id={$fila['id']}' class='btn-editar'>Editar</a>
                                <a href='../scripts/eliminar_raza.php?id={$fila['id']}'
                                   class='btn-eliminar'
                                   onclick=\"return confirm('¿Seguro que quieres eliminar esta raza?')\">Eliminar</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='sin-datos'>No hay razas registradas.</td></tr>";
                }

                echo "</tbody></table>";
                mysqli_free_result($resultado);
            }
        ?>
    </div>

    <div class="seccion-form">
        <h2>Nueva Raza</h2>
        <form action="../scripts/insertar_raza.php" method="POST">
            <label>Nombre de la raza</label>
            <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>" onblur="validarNombre()">
            <div class="error-campo" id="errorNombre"></div>

            <label>Peso (kg)</label>
            <input type="number" step="0.01" name="peso" id="peso" value="<?= htmlspecialchars($datos['peso'] ?? '') ?>" onblur="validarPeso()">
            <div class="error-campo" id="errorPeso"></div>

            <label>Altura (cm)</label>
            <input type="number" step="0.01" name="altura" id="altura" value="<?= htmlspecialchars($datos['altura'] ?? '') ?>" onblur="validarAltura()">
            <div class="error-campo" id="errorAltura"></div>

            <label>Temperamento (opcional)</label>
            <input type="text" name="temperamento" id="temperamento" value="<?= htmlspecialchars($datos['temperamento'] ?? '') ?>" onblur="validarTemperamento()">
            <div class="error-campo" id="errorTemperamento"></div>

            <button type="submit" onclick="return validarFormularioRaza()">Agregar Raza</button>
        </form>
    </div>
</div>
<script src="../js/validacion_raza.js"></script>
</body>
</html>
