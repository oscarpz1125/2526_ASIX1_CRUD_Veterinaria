// Muestra el mensaje de error y marca el campo con rojo cuando algo no es válido.
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

// Comprueba si el texto es un número válido.
function esNumerico(texto) {
    return !isNaN(texto) && texto.trim() !== "";
}

// Valida el nombre de la raza: obligatorio, mínimo 3 caracteres y no solo números.
function validarNombre() {
    const valor = document.getElementById("nombre").value.trim();
    let mensaje = "";

    if (valor === "") {
        mensaje = "El nombre de la raza es obligatorio.";
    } else if (valor.length < 3) {
        mensaje = "El nombre debe tener minimo 3 caracteres.";
    } else if (esNumerico(valor)) {
        mensaje = "El nombre no puede ser un numero.";
    }

    return gestionarError("nombre", "errorNombre", mensaje);
}

// Valida el peso: obligatorio y debe ser mayor que cero.
function validarPeso() {
    const valor = document.getElementById("peso").value;
    let mensaje = "";

    if (valor === "") {
        mensaje = "El peso es obligatorio.";
    } else if (isNaN(valor) || parseFloat(valor) <= 0) {
        mensaje = "El peso debe ser un numero mayor que 0.";
    }

    return gestionarError("peso", "errorPeso", mensaje);
}

// Valida la altura: obligatoria y estrictamente positiva.
function validarAltura() {
    const valor = document.getElementById("altura").value;
    let mensaje = "";

    if (valor === "") {
        mensaje = "La altura es obligatoria.";
    } else if (isNaN(valor) || parseFloat(valor) <= 0) {
        mensaje = "La altura debe ser un numero mayor que 0.";
    }

    return gestionarError("altura", "errorAltura", mensaje);
}

// Valida el temperamento solo si se ha escrito algo, evitando números puros.
function validarTemperamento() {
    const valor = document.getElementById("temperamento").value.trim();
    let mensaje = "";

    if (valor !== "" && valor.length < 3) {
        mensaje = "El temperamento debe tener minimo 3 caracteres.";
    } else if (valor !== "" && esNumerico(valor)) {
        mensaje = "El temperamento no puede ser un numero.";
    }

    return gestionarError("temperamento", "errorTemperamento", mensaje);
}

// Valida todos los campos del formulario de raza.
function validarFormularioRaza() {
    const ok1 = validarNombre();
    const ok2 = validarPeso();
    const ok3 = validarAltura();
    const ok4 = validarTemperamento();
    return ok1 && ok2 && ok3 && ok4;
}
