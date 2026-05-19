<?php
/*
  Página de listado y alta de mascotas.
  Muestra las mascotas registradas y permite añadir nuevas mascotas.
*/
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include '../includes/conexion.php';

// Mensajes flash que dejan los scripts de insertar/actualizar/eliminar
$error = isset($_SESSION['error_mascota']) ? $_SESSION['error_mascota'] : '';
$ok = isset($_SESSION['ok_mascota']) ? $_SESSION['ok_mascota'] : '';
unset($_SESSION['error_mascota'], $_SESSION['ok_mascota']);

$datos = isset($_SESSION['datos_mascota']) ? $_SESSION['datos_mascota'] : array();
unset($_SESSION['datos_mascota']);

// Cargamos los desplegables del formulario de alta. Lo hacemos aqui para
// no consultar la BD dentro de bucles HTML mas abajo.
$res_razas = mysqli_query($conn, "SELECT id, nombre FROM razas ORDER BY nombre ASC");
$razas_lista = mysqli_fetch_all($res_razas, MYSQLI_ASSOC);

$res_props = mysqli_query($conn, "SELECT id, nombre FROM propietarios ORDER BY nombre ASC");
$propietarios_lista = mysqli_fetch_all($res_props, MYSQLI_ASSOC);

$res_vets = mysqli_query($conn, "SELECT id, nombre FROM veterinarios ORDER BY nombre ASC");
$veterinarios_lista = mysqli_fetch_all($res_vets, MYSQLI_ASSOC);

// Filtros - isset() comprueba si existe la variable GET, trim() quita espacios
// ? --> es como un operador ternarios como if /else
$filtro_raza   = isset($_GET['raza_id']) ? trim($_GET['raza_id']) : '';
$filtro_sexo   = isset($_GET['sexo']) ? trim($_GET['sexo']) : '';

// Empezamos sin condiciones; cada filtro se sumara al array
$condiciones = [];

// Si hay filtros los añadimos a las condiciones
// Filtro por raza: comparamos el id numerico de la raza
// intval() convierte el valor a entero para evitar inyeccion SQL
if ($filtro_raza !== '') {
    $filtro_raza_safe = intval($filtro_raza);
    $condiciones[] = "m.raza_id = $filtro_raza_safe";
}

// Filtro por sexo: solo acepta valores M o F, cualquier otro se ignora
// in_array() comprueba que el valor este dentro de los valores permitidos
if ($filtro_sexo !== '' && in_array($filtro_sexo, ['M', 'F'])) {
    $filtro_sexo_safe = mysqli_real_escape_string($conn, $filtro_sexo);
    $condiciones[] = "m.sexo = '$filtro_sexo_safe'";
}

// Todos los filtros se concatenan con AND, por eso el filtro es "sumativo":
// cada campo adicional reduce los resultados mostrando solo los que cumplen
// TODOS los criterios a la vez.
$hay_filtros = count($condiciones) > 0;
$where_sql = $hay_filtros ? ' WHERE ' . implode(' AND ', $condiciones) : '';

// FK NOT NULL en mascotas, asi que INNER JOIN nos vale para todas las filas
$sql = "SELECT m.chip, m.nombre, m.sexo, m.especie, m.fecha_nacimiento,
               r.nombre AS raza_nombre,
               p.nombre AS propietario_nombre,
               v.nombre AS veterinario_nombre
        FROM mascotas m
        INNER JOIN razas r ON m.raza_id = r.id
        INNER JOIN propietarios p ON m.propietario_id = p.id
        INNER JOIN veterinarios v ON m.veterinario_id = v.id"
        . $where_sql .
        " ORDER BY m.fecha_registro DESC";

$resultado = mysqli_query($conn, $sql);
$mascotas = $resultado ? mysqli_fetch_all($resultado, MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mascotas - PerryatPerriatra</title>
    <link rel="stylesheet" href="../estilos/estilo.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="contenido">
    <div class="seccion-tabla">
        <h2>Mascotas</h2>

        <?php if ($error !== ''): ?><div class="error-general"><?= $error ?></div><?php endif; ?>
        <?php if ($ok !== ''): ?><div class="ok-general"><?= $ok ?></div><?php endif; ?>
        
        <!-- GET en lugar de POST para que el filtro quede en la URL -->
        <form method="GET" class="formulario-filtros">
            <label>Filtrar por raza:</label>
            <select name="raza_id">
                <option value="">-- Todas las razas --</option>
                <?php foreach ($razas_lista as $r) { ?>
                    <option value="<?= $r['id'] ?>" <?= ($filtro_raza === (string)$r['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($r['nombre']) ?>
                    </option>
                <?php } ?>
            </select>

            <label>Filtrar por sexo:</label>
            <select name="sexo">
                <option value="">-- Todos los sexos --</option>
                <option value="M" <?= ($filtro_sexo === 'M') ? 'selected' : '' ?>>Macho</option>
                <option value="F" <?= ($filtro_sexo === 'F') ? 'selected' : '' ?>>Hembra</option>
            </select>

            <button type="submit">Filtrar</button>
            <a href="mascotas.php">Limpiar filtros</a>
        </form>
        <br>
        <?php
            $total = count($mascotas);
            if ($hay_filtros) {
                // Construimos un mensaje descriptivo con los filtros activos
                $partes = [];
                if ($filtro_raza !== '') {
                    // Buscamos el nombre de la raza en la lista que ya tenemos cargada
                    $nombre_raza = '';
                    foreach ($razas_lista as $r) {
                        if ((string)$r['id'] === $filtro_raza) {
                            $nombre_raza = htmlspecialchars($r['nombre']);
                            break;
                        }
                    }
                    $partes[] = "raza \"$nombre_raza\"";
                }
                if ($filtro_sexo !== '') {
                    $partes[] = "sexo \"" . ($filtro_sexo === 'M' ? 'Macho' : 'Hembra') . "\"";
                }
                $descripcion = implode(' + ', $partes);
                $palabra = ($total === 1) ? 'mascota encontrada' : 'mascotas encontradas';
                echo "<p class='filtros-activos'>Se han encontrado <strong>$total</strong> $palabra con el filtro: $descripcion.</p>";
            }
            echo "<table class='tabla-datos'>
            <thead>
                <tr>
                    <th>Chip</th>
                    <th>Nombre</th>
                    <th>Sexo</th>
                    <th>Especie</th>
                    <th>F. Nacimiento</th>
                    <th>Raza</th>
                    <th>Propietario</th>
                    <th>Veterinario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>";

            if (count($mascotas) > 0) {
                foreach ($mascotas as $fila) {
                    $chip = htmlspecialchars($fila['chip']);
                    $nombre = htmlspecialchars($fila['nombre']);
                    $sexo = $fila['sexo'] === 'M' ? 'Macho' : 'Hembra';
                    $especie = htmlspecialchars($fila['especie']);
                    $fecha_nac = date('d/m/Y', strtotime($fila['fecha_nacimiento']));
                    $raza = htmlspecialchars($fila['raza_nombre']);
                    $propietario = htmlspecialchars($fila['propietario_nombre']);
                    $veterinario = htmlspecialchars($fila['veterinario_nombre']);
                    $chip_url = urlencode($fila['chip']);

                    echo "<tr>
                        <td>{$chip}</td>
                        <td>{$nombre}</td>
                        <td>{$sexo}</td>
                        <td>{$especie}</td>
                        <td>{$fecha_nac}</td>
                        <td>{$raza}</td>
                        <td>{$propietario}</td>
                        <td>{$veterinario}</td>
                        <td>
                            <a href='editar_mascota.php?chip={$chip_url}' class='btn-editar'>Editar</a>
                            <a href='../scripts/eliminar_mascota.php?chip={$chip_url}'
                               class='btn-eliminar'
                               onclick=\"return confirm('¿Seguro que quieres eliminar esta mascota?')\">Eliminar</a>
                        </td>
                    </tr>";
                }
            } else {
                // Diferenciamos mensaje: tabla vacia vs filtro sin resultados
                $mensaje_vacio = $hay_filtros ? "No hay mascotas que coincidan con el filtro." : "No hay mascotas registradas.";
                echo "<tr><td colspan='9' class='sin-datos'>$mensaje_vacio</td></tr>";
            }

            echo "</tbody></table>";
            if ($resultado) mysqli_free_result($resultado);
        ?>
    </div>

    <div class="seccion-form">
        <h2>Nueva Mascota</h2>
        <form action="../scripts/insertar_mascota.php" method="POST">
            <label>Chip</label>
            <input type="text" name="chip" id="chip" value="<?= htmlspecialchars($datos['chip'] ?? '') ?>" onblur="validarChip()">
            <div class="error-campo" id="errorChip"></div>

            <label>Nombre</label>
            <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>" onblur="validarNombre()">
            <div class="error-campo" id="errorNombre"></div>

            <label>Sexo</label>
            <select name="sexo" id="sexo" onblur="validarSexo()">
                <option value="">-- Sexo --</option>
                <option value="M" <?= (($datos['sexo'] ?? '') === 'M') ? 'selected' : '' ?>>Macho</option>
                <option value="F" <?= (($datos['sexo'] ?? '') === 'F') ? 'selected' : '' ?>>Hembra</option>
            </select>
            <div class="error-campo" id="errorSexo"></div>

            <label>Especie</label>
            <input type="text" name="especie" id="especie" value="<?= htmlspecialchars($datos['especie'] ?? '') ?>" onblur="validarEspecie()">
            <div class="error-campo" id="errorEspecie"></div>

            <label>Fecha de nacimiento</label>
            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" value="<?= htmlspecialchars($datos['fecha_nacimiento'] ?? '') ?>" onblur="validarFechaNac()">
            <div class="error-campo" id="errorFecha"></div>

            <label>Raza</label>
            <select name="raza_id" id="raza_id" onblur="validarRaza()">
                <option value="">-- Raza --</option>
                <?php foreach ($razas_lista as $r) { ?>
                    <option value="<?= $r['id'] ?>" <?= ((string)($datos['raza_id'] ?? '') === (string)$r['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($r['nombre']) ?>
                    </option>
                <?php } ?>
            </select>
            <div class="error-campo" id="errorRaza"></div>

            <label>Propietario</label>
            <select name="propietario_id" id="propietario_id" onblur="validarPropietario()">
                <option value="">-- Propietario --</option>
                <?php foreach ($propietarios_lista as $p) { ?>
                    <option value="<?= $p['id'] ?>" <?= ((string)($datos['propietario_id'] ?? '') === (string)$p['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nombre']) ?>
                    </option>
                <?php } ?>
            </select>
            <div class="error-campo" id="errorPropietario"></div>

            <label>Veterinario</label>
            <select name="veterinario_id" id="veterinario_id" onblur="validarVeterinario()">
                <option value="">-- Veterinario --</option>
                <?php foreach ($veterinarios_lista as $vv) { ?>
                    <option value="<?= $vv['id'] ?>" <?= ((string)($datos['veterinario_id'] ?? '') === (string)$vv['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($vv['nombre']) ?>
                    </option>
                <?php } ?>
            </select>
            <div class="error-campo" id="errorVeterinario"></div>

            <button type="submit" onclick="return validarFormularioMascota()">Agregar Mascota</button>
        </form>
    </div>
</div>
<script src="../js/validacion_mascota.js"></script>
</body>
</html>
