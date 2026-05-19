// Muestra el error junto al campo y pinta el borde si hay fallo.
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

// Detecta si una cadena es un número válido y no está vacía.
function esNumerico(texto) {
    return !isNaN(texto) && texto.trim() !== "";
}

// Valida el nombre del propietario: obligatorio, al menos 3 caracteres y no numérico.
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

// Valida el email con un patrón simple: texto@texto.texto.
function validarEmail() {
    const valor = document.getElementById("email").value.trim();
    const formatoValido = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valor);
    let mensaje = "";

    if (valor === "") {
        mensaje = "El email es obligatorio.";
    } else if (!formatoValido) {
        mensaje = "El email no tiene un formato valido.";
    }

    return gestionarError("email", "errorEmail", mensaje);
}

// Valida el teléfono: obligatorio, solo números y exactamente 9 dígitos.
function validarTelefono() {
    const valor = document.getElementById("telefono").value.trim();
    let mensaje = "";

    if (valor === "") {
        mensaje = "El telefono es obligatorio.";
    } else if (isNaN(valor)) {
        mensaje = "El telefono solo puede contener numeros.";
    } else if (valor.length !== 9) {
        mensaje = "El telefono debe tener exactamente 9 digitos.";
    }

    return gestionarError("telefono", "errorTelefono", mensaje);
}

// Valida la dirección solo si el usuario escribe algo en ese campo.
function validarDireccion() {
    const valor = document.getElementById("direccion").value.trim();
    let mensaje = "";

    if (valor !== "" && valor.length < 3) {
        mensaje = "La direccion debe tener minimo 3 caracteres.";
    } else if (valor !== "" && esNumerico(valor)) {
        mensaje = "La direccion no puede ser solo un numero.";
    }

    return gestionarError("direccion", "errorDireccion", mensaje);
}

// Valida todo el formulario de propietario y devuelve true si no hay errores.
function validarFormularioPropietario() {
    const ok1 = validarNombre();
    const ok2 = validarEmail();
    const ok3 = validarTelefono();
    const ok4 = validarDireccion();
    return ok1 && ok2 && ok3 && ok4;
}
