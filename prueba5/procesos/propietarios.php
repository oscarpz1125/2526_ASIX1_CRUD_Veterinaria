<?php
/*
  Página de listado y alta de propietarios.
  Presenta los propietarios existentes y permite crear nuevos registros.
*/
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include '../includes/conexion.php';

// Mensajes flash que dejan los scripts (insertar/actualizar/eliminar)
$error = isset($_SESSION['error_propietario']) ? $_SESSION['error_propietario'] : '';
$ok = isset($_SESSION['ok_propietario']) ? $_SESSION['ok_propietario'] : '';
unset($_SESSION['error_propietario'], $_SESSION['ok_propietario']);

$datos = isset($_SESSION['datos_propietario']) ? $_SESSION['datos_propietario'] : array();
unset($_SESSION['datos_propietario']);

// Pinta el telefono como XXX-XX-XX-XX si tiene 9 digitos, sino lo deja tal cual
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
    <title>Propietarios - PerryatPerriatra</title>
    <link rel="stylesheet" href="../estilos/estilo.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="contenido">

    <div class="seccion-tabla">
        <h2>Propietarios</h2>

        <?php if ($error !== ''): ?><div class="error-general"><?= $error ?></div><?php endif; ?>
        <?php if ($ok !== ''): ?><div class="ok-general"><?= $ok ?></div><?php endif; ?>

        <?php
            $sql = "SELECT id, nombre, email, telefono, direccion FROM propietarios ORDER BY id DESC";
            $resultado = mysqli_query($conn, $sql);

            if ($resultado) {
                $propietarios = mysqli_fetch_all($resultado, MYSQLI_ASSOC);

                echo "<table class='tabla-datos'>";
                echo "<thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Telefono</th>
                            <th>Direccion</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>";
                echo "<tbody>";

                if (count($propietarios) > 0) {
                    foreach ($propietarios as $fila) {
                        $nombre = htmlspecialchars($fila['nombre']);
                        $email = htmlspecialchars($fila['email']);
                        $telefono = formatoTelefono($fila['telefono']);
                        // Direccion es opcional: si esta vacia, pintamos un guion
                        $direccion = $fila['direccion'] !== '' ? htmlspecialchars($fila['direccion']) : '-';

                        echo "<tr>
                            <td>{$nombre}</td>
                            <td>{$email}</td>
                            <td>{$telefono}</td>
                            <td>{$direccion}</td>
                            <td>
                                <a href='editar_propietario.php?id={$fila['id']}' class='btn-editar'>Editar</a>
                                <a href='../scripts/eliminar_propietario.php?id={$fila['id']}'
                                   class='btn-eliminar'
                                   onclick=\"return confirm('¿Seguro que quieres eliminar este propietario?\\n\\nSe eliminaran tambien sus mascotas asociadas.')\">Eliminar</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='sin-datos'>No hay propietarios registrados.</td></tr>";
                }

                echo "</tbody></table>";
                mysqli_free_result($resultado);
            }
        ?>
    </div>

    <div class="seccion-form">
        <h2>Nuevo Propietario</h2>
        <form action="../scripts/insertar_propietario.php" method="POST">

            <label>Nombre completo</label>
            <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>" onblur="validarNombre()">
            <div class="error-campo" id="errorNombre"></div>

            <label>Email</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($datos['email'] ?? '') ?>" onblur="validarEmail()">
            <div class="error-campo" id="errorEmail"></div>

            <label>Telefono</label>
            <input type="text" name="telefono" id="telefono" maxlength="9" value="<?= htmlspecialchars($datos['telefono'] ?? '') ?>" onblur="validarTelefono()">
            <div class="error-campo" id="errorTelefono"></div>

            <label>Direccion (opcional)</label>
            <input type="text" name="direccion" id="direccion" value="<?= htmlspecialchars($datos['direccion'] ?? '') ?>" onblur="validarDireccion()">
            <div class="error-campo" id="errorDireccion"></div>

            <button type="submit" onclick="return validarFormularioPropietario()">Agregar Propietario</button>
        </form>
    </div>

</div>

<script src="../js/validacion_propietario.js"></script>
</body>
</html>
