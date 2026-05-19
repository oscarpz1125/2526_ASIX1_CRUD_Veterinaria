// Muestra el mensaje de error y cambia el borde del campo cuando falla la validación.
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

// Valida el nombre de usuario: obligatorio y al menos 3 caracteres.
function validarUsuario() {
    const valor = document.getElementById("usuario").value.trim();
    let mensaje = "";

    if (valor === "") {
        mensaje = "El nombre de usuario es obligatorio.";
    } else if (valor.length < 3) {
        mensaje = "El usuario debe tener minimo 3 caracteres.";
    }

    return gestionarError("usuario", "errorUsuario", mensaje);
}

// Valida el email con un patrón mínimo aceptable.
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

// Valida la contraseña: obligatoria, mínimo 8 caracteres, una mayúscula y un número.
function validarPassword() {
    const valor = document.getElementById("password").value;
    let mensaje = "";

    if (valor.trim() === "") {
        mensaje = "La contrasena es obligatoria.";
    } else if (valor.length < 8) {
        mensaje = "La contrasena debe tener minimo 8 caracteres.";
    } else if (!/[A-Z]/.test(valor)) {
        mensaje = "La contrasena debe tener al menos una mayuscula.";
    } else if (!/[0-9]/.test(valor)) {
        mensaje = "La contrasena debe tener al menos un numero.";
    }

    return gestionarError("password", "errorPassword", mensaje);
}

// Valida que la confirmación de contraseña no esté vacía y coincida con la original.
function validarConfirm() {
    const password = document.getElementById("password").value;
    const confirm = document.getElementById("confirm_password").value;
    let mensaje = "";

    if (confirm.trim() === "") {
        mensaje = "Debes confirmar la contrasena.";
    } else if (password !== confirm) {
        mensaje = "Las contrasenas no coinciden.";
    }

    return gestionarError("confirm_password", "errorConfirm", mensaje);
}

// Valida todo el formulario de registro y devuelve true si todo está bien.
function validarFormularioRegistro() {
    const ok1 = validarUsuario();
    const ok2 = validarEmail();
    const ok3 = validarPassword();
    const ok4 = validarConfirm();
    return ok1 && ok2 && ok3 && ok4;
}
