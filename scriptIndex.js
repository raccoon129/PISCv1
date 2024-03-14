document.addEventListener("DOMContentLoaded", function() {
    validarCampoSinEspacios('formCrearUsuario', 'username');
});

// Aqui se agrtega un listener al formulario para deshabilitar el boton después de hacerle click
var form = document.getElementById('formCrearUsuario');
form.addEventListener('submit',function(){
    form.querySelector('button[type="submit"]').setAttribute('disabled', 'disabled');
});
