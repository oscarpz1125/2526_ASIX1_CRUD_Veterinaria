<?php
/*
  Página de listado y alta de veterinarios.
  Muestra los veterinarios existentes y permite crear nuevos registros.
*/
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include '../includes/conexion.php';

// Mensajes flash que dejan los scripts
$error = isset($_SESSION['error_veterinario']) ? $_SESSION['error_veterinario'] : '';
$ok = isset($_SESSION['ok_veterinario']) ? $_SESSION['ok_veterinario'] : '';
unset($_SESSION['error_veterinario'], $_SESSION['ok_veterinario']);

$datos = isset($_SESSION['datos_veterinario']) ? $_SESSION['datos_veterinario'] : array();
unset($_SESSION['datos_veterinario']);

function formatoTelefono($tel) {
    $solo = preg_replace('/\D/', '', $tel);
    if (strlen($solo) === 9) {
        return substr($solo, 0, 3) . '-' . substr($solo, 3, 2) . '-' . substr($solo, 5, 2) . '-' . substr($solo, 7, 2);
    }
    return htmlspecialchars($tel);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinarios - PerryatPerriatra</title>
    <link rel="stylesheet" href="../estilos/estilo.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="contenido">
    <div class="seccion-tabla">
        <h2>Veterinarios</h2>

        <?php if ($error !== ''): ?><div class="error-general"><?= $error ?></div><?php endif; ?>
        <?php if ($ok !== ''): ?><div class="ok-general"><?= $ok ?></div><?php endif; ?>

        <?php
            $sql = "SELECT id, nombre, email, telefono, especialidad, salario FROM veterinarios ORDER BY id DESC";
            $resultado = mysqli_query($conn, $sql);

            if ($resultado) {
                $veterinarios = mysqli_fetch_all($resultado, MYSQLI_ASSOC);

                echo "<table class='tabla-datos'>";
                echo "<thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Telefono</th>
                            <th>Especialidad</th>
                            <th>Salario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>";
                echo "<tbody>";

                if (count($veterinarios) > 0) {
                    foreach ($veterinarios as $fila) {
                        $nombre = htmlspecialchars($fila['nombre']);
                        $email = htmlspecialchars($fila['email']);
                        $telefono = formatoTelefono($fila['telefono']);
                        $especialidad = htmlspecialchars($fila['especialidad']);
                        $salario = number_format($fila['salario'], 2, ',', '.');

                        echo "<tr>
                            <td>{$nombre}</td>
                            <td>{$email}</td>
                            <td>{$telefono}</td>
                            <td>{$especialidad}</td>
                            <td>{$salario} EUR</td>
                            <td>
                                <a href='editar_veterinario.php?id={$fila['id']}' class='btn-editar'>Editar</a>
                                <a href='../scripts/eliminar_veterinario.php?id={$fila['id']}'
                                   class='btn-eliminar'
                                   onclick=\"return confirm('¿Seguro que quieres eliminar este veterinario?')\">Eliminar</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='sin-datos'>No hay veterinarios registrados.</td></tr>";
                }

                echo "</tbody></table>";
                mysqli_free_result($resultado);
            }
        ?>
    </div>

    <div class="seccion-form">
        <h2>Nuevo Veterinario</h2>
        <form action="../scripts/insertar_veterinario.php" method="POST">
            <label>Nombre completo</label>
            <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>" onblur="validarNombre()">
            <div class="error-campo" id="errorNombre"></div>

            <label>Email</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($datos['email'] ?? '') ?>" onblur="validarEmail()">
            <div class="error-campo" id="errorEmail"></div>

            <label>Telefono</label>
            <input type="text" name="telefono" id="telefono" maxlength="9" value="<?= htmlspecialchars($datos['telefono'] ?? '') ?>" onblur="validarTelefono()">
            <div class="error-campo" id="errorTelefono"></div>

            <label>Especialidad</label>
            <input type="text" name="especialidad" id="especialidad" value="<?= htmlspecialchars($datos['especialidad'] ?? '') ?>" onblur="validarEspecialidad()">
            <div class="error-campo" id="errorEspecialidad"></div>

            <label>Salario (EUR)</label>
            <input type="number" step="0.01" name="salario" id="salario" value="<?= htmlspecialchars($datos['salario'] ?? '') ?>" onblur="validarSalario()">
            <div class="error-campo" id="errorSalario"></div>

            <button type="submit" onclick="return validarFormularioVeterinario()">Agregar Veterinario</button>
        </form>
    </div>
</div>
<script src="../js/validacion_veterinario.js"></script>
</body>
</html>
