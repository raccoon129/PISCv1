
function abrirFormularioInsertarUsuario() {
    var url = 'admonUsuariosInsertarUsuario.php';
    window.open(url, "Insertar Usuario", "width=500,height=600");
}
function abrirEditorUsuario(username) {
    var url = "editarUsuarioAdmonUsuarios.php?username=" + encodeURIComponent(username);
    window.open(url, '_blank', 'height=500,width=800,left=200,top=200,resizable=yes');
}
/*

ESTA PARTE SE HA DESCARTADO DEBIDO A CUESTIONES DE SEGURIDAD en admonusuarios.php - Las contrasenas no se mostrarán 
dentro del módulo. 
EN EL CASO QUE SE DESEE IMPLEMENTAR MOSTRAR LAS CONTRASENAS, DESCOMENTAREAR Y MANDAR A LLAMAR LAS FUNCIONES EN admonUsuarios.php 
Y editarUsuarioAdmonUsuarios.php

function abrirFormularioEditarUsuario() {
    var url = 'editarUsuarioAdmonUsuarios.php';
    window.open(url, "Editar Bien", "width=1100,height=600");
}
$(document).ready(function () {
    $('#addUserForm').on('submit', function (e) {
        e.preventDefault(); // Evitar el envío normal del formulario

        var data = {
            username: $('#add-username').val(),
            password: $('#add-password').val(),
            rol: $('#add-role').val(),
            nombrecompleto: $('#add-fullname').val(),
            accion: 'nuevo_usuario'
        };

        $.ajax({
            type: 'POST',
            url: 'accionesAdmonUsuarios.php',
            data: data,
            success: function (response) {
                alert("Usuario añadido exitosamente");
                $('#addUserModal').modal('hide'); // Ocultar el modal
                location.reload(); // Opcional: recargar la página para ver los cambios
            },
            error: function () {
                alert("Error al añadir usuario");
            }
        });
    });
});


// para hacer el ojo, la cosa para ocultar la contraseña
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.toggle-password').forEach(function (toggle) {
        toggle.addEventListener('click', function () {
            var input = this.closest('.input-group').querySelector('.password');
            if (input.type === 'password') {
                input.type = 'text';
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                this.classList.add('fa-eye-slash');
                this.classList.add('fa-eye');
            }
        });
    });
});
*/