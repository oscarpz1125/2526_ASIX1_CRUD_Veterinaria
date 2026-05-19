/* validacion_mascota.js
   Validaciones del formulario de alta/edición de mascotas.
   Cada campo se comprueba antes de enviar el formulario para evitar errores
   simples como campos vacíos, formatos incorrectos o fechas futuras.
*/

// Muestra el mensaje de error en la pantalla y marca el campo en rojo.
// Devuelve false cuando hay un error, true cuando el campo es válido.
function gestionarError(idCampo, idError, mensaje) {
    const campo = document.getElementById(idCampo);
    const contenedor = document.getElementById(idError);
    contenedor.textContent = mensaje;

    if (mensaje !== "") {
        campo.style.borderColor = "#ff0000";
        return false;
    } else {
        campo.style.borderColor = "#b0c8e8";
        return true;
    }
}

// Comprueba si el texto es numérico y no está vacío.
function esNumerico(texto) {
    return !isNaN(texto) && texto.trim() !== "";
}

// Valida el número de chip: obligatorio, solo números y mínimo 15 caracteres.
function validarChip() {
    const valor = document.getElementById("chip").value.trim();
    let mensaje = "";

    if (valor === "") {
        mensaje = "El chip es obligatorio.";
    } else if (isNaN(valor)) {
        mensaje = "El chip solo puede contener numeros.";
    } else if (valor.length < 15) {
        mensaje = "El chip debe tener minimo 15 caracteres.";
    }

    return gestionarError("chip", "errorChip", mensaje);
}

// Valida el nombre de la mascota: obligatorio, mínimo 3 caracteres y no numérico.
function validarNombre() {
    const valor = document.getElementById("nombre").value.trim();
    let mensaje = "";

    if (valor === "") {
        mensaje = "El nombre es obligatorio.";
    } else if (valor.length < 3) {
        mensaje = "El nombre debe tener minimo 3 caracteres.";
    } else if (esNumerico(valor)) {
        mensaje = "El nombre no puede ser un numero.";
    }

    return gestionarError("nombre", "errorNombre", mensaje);
}

// Valida que se haya elegido un sexo de la lista.
function validarSexo() {
    const valor = document.getElementById("sexo").value;
    let mensaje = "";

    if (valor === "") {
        mensaje = "Debes elegir un sexo.";
    }

    return gestionarError("sexo", "errorSexo", mensaje);
}

// Valida la especie: obligatorio, mínimo 3 caracteres y no numérico.
function validarEspecie() {
    const valor = document.getElementById("especie").value.trim();
    let mensaje = "";

    if (valor === "") {
        mensaje = "La especie es obligatoria.";
    } else if (valor.length < 3) {
        mensaje = "La especie debe tener minimo 3 caracteres.";
    } else if (esNumerico(valor)) {
        mensaje = "La especie no puede ser un numero.";
    }

    return gestionarError("especie", "errorEspecie", mensaje);
}

// Valida la fecha de nacimiento: obligatoria y no puede estar en el futuro.
function validarFechaNac() {
    const valor = document.getElementById("fecha_nacimiento").value;
    let mensaje = "";

    if (valor === "") {
        mensaje = "La fecha de nacimiento es obligatoria.";
    } else {
        const hoy = new Date();
        const fecha = new Date(valor);
        hoy.setHours(0, 0, 0, 0);
        if (fecha > hoy) {
            mensaje = "La fecha no puede ser futura.";
        }
    }

    return gestionarError("fecha_nacimiento", "errorFecha", mensaje);
}

// Valida que se haya seleccionado una raza.
function validarRaza() {
    const valor = document.getElementById("raza_id").value;
    let mensaje = "";

    if (valor === "") {
        mensaje = "Debes elegir una raza.";
    }

    return gestionarError("raza_id", "errorRaza", mensaje);
}

// Valida que se haya seleccionado un propietario.
function validarPropietario() {
    const valor = document.getElementById("propietario_id").value;
    let mensaje = "";

    if (valor === "") {
        mensaje = "Debes elegir un propietario.";
    }

    return gestionarError("propietario_id", "errorPropietario", mensaje);
}

// Valida que se haya seleccionado un veterinario.
function validarVeterinario() {
    const valor = document.getElementById("veterinario_id").value;
    let mensaje = "";

    if (valor === "") {
        mensaje = "Debes elegir un veterinario.";
    }

    return gestionarError("veterinario_id", "errorVeterinario", mensaje);
}

// Valida todo el formulario de mascota. Solo envía si todas las validaciones pasan.
function validarFormularioMascota() {
    const ok1 = validarChip();
    const ok2 = validarNombre();
    const ok3 = validarSexo();
    const ok4 = validarEspecie();
    const ok5 = validarFechaNac();
    const ok6 = validarRaza();
    const ok7 = validarPropietario();
    const ok8 = validarVeterinario();
    return ok1 && ok2 && ok3 && ok4 && ok5 && ok6 && ok7 && ok8;
}
