// Muestra el error junto al campo y pinta el borde en rojo cuando hay fallo.
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

// Comprueba si la cadena es un número válido y no está vacía.
function esNumerico(texto) {
    return !isNaN(texto) && texto.trim() !== "";
}

// Valida el nombre del veterinario: obligatorio, mínimo 3 caracteres y no numérico.
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

// Valida el email con un patrón básico de correo.
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

// Valida el teléfono: obligatorio, solo dígitos y 9 caracteres.
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

// Valida la especialidad: obligatoria, mínimo 3 caracteres y no un número.
function validarEspecialidad() {
    const valor = document.getElementById("especialidad").value.trim();
    let mensaje = "";

    if (valor === "") {
        mensaje = "La especialidad es obligatoria.";
    } else if (valor.length < 3) {
        mensaje = "La especialidad debe tener minimo 3 caracteres.";
    } else if (esNumerico(valor)) {
        mensaje = "La especialidad no puede ser un numero.";
    }

    return gestionarError("especialidad", "errorEspecialidad", mensaje);
}

// Valida el salario: obligatorio y mayor que cero.
function validarSalario() {
    const valor = document.getElementById("salario").value;
    let mensaje = "";

    if (valor === "") {
        mensaje = "El salario es obligatorio.";
    } else if (isNaN(valor) || parseFloat(valor) <= 0) {
        mensaje = "El salario debe ser un numero mayor que 0.";
    }

    return gestionarError("salario", "errorSalario", mensaje);
}

// Valida todo el formulario de veterinario y devuelve true si no hay errores.
function validarFormularioVeterinario() {
    const ok1 = validarNombre();
    const ok2 = validarEmail();
    const ok3 = validarTelefono();
    const ok4 = validarEspecialidad();
    const ok5 = validarSalario();
    return ok1 && ok2 && ok3 && ok4 && ok5;
}
