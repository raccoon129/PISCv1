document.addEventListener("DOMContentLoaded", function() {
    validarCampoSinEspacios('formInsertarUsuario', 'username');
});
$(document).ready(function() {
$('#formInsertarUsuario').on('submit', function() {
    $('#saveButton').prop('disabled', true);
    $('#buttonText').hide();
    $('#spinner').show();
});
});