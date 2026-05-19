// Muestra el mensaje de error junto al campo y pinta el borde de rojo si hay fallo.
// Si no hay error, deja el borde con el estilo neutro original.
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

// Valida que el campo usuario no esté vacío.
function validarUsuario() {
    const valor = document.getElementById("usuario").value.trim();
    let mensaje = "";

    if (valor === "") {
        mensaje = "El usuario es obligatorio.";
    }

    return gestionarError("usuario", "errorUsuario", mensaje);
}

// Valida que el campo contraseña no esté vacío.
function validarPassword() {
    const valor = document.getElementById("password").value.trim();
    let mensaje = "";

    if (valor === "") {
        mensaje = "La contrasena es obligatoria.";
    }

    return gestionarError("password", "errorPassword", mensaje);
}

// Ejecuta todas las validaciones del login y devuelve true solo si todas pasan.
function validarLogin() {
    const ok1 = validarUsuario();
    const ok2 = validarPassword();
    return ok1 && ok2;
}
