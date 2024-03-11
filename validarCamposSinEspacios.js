function validarCampoSinEspacios(formId, campoId) {
    var form = document.getElementById(formId);
    if (!form) return; // Si no encuentra el formulario, termina la función
    
    form.onsubmit = function(e) {
        var campo = document.getElementById(campoId);
        if (campo && campo.value.includes(" ")) {
            alert("El nombre de usuario no debe contener espacios en blanco.");
            e.preventDefault(); // Evita que el formulario se envíe
            return false;
        }
        return true;
    };
}
