document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.toggle-password').forEach(function(item) {
        item.addEventListener('click', function(e) {
            let input = this.closest('.input-group').querySelector('input');
            if (input.type === "password") {
                input.type = "text";
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye');
            }
        });
    });
});

function eliminarUsuario(id) {
    if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
        $.ajax({
            type: 'POST',
            url: './accionesAdmonUsuarios.php',
            data: {
                id: id,
                accion: 'eliminar_usuario'
            },
            success: function(response) {
                alert(response);
                location.reload(); // Recarga la página para reflejar los cambios
            },
            error: function() {
                alert('Error al eliminar el usuario.');
            }
        });
    }
}

function restablecerContrasena(username) {
    var nuevaContrasena = prompt("Por favor, ingresa la nueva contraseña para el usuario: " + username);
    if (nuevaContrasena !== null && nuevaContrasena !== "") {
        // Envía la nueva contraseña al servidor para actualizarla
        $.post("accionesAdmonUsuarios.php", 
            { 
                accion: "restablecer_contrasena", 
                username: username, 
                password: nuevaContrasena 
            }, 
            function(result) {
                alert(result); // Muestra un mensaje con el resultado
                window.close();
            }
        );
    }
}

function validarCamposUsuarioSinEspacios() {
    var form = document.getElementById("formActualizarUsuario");
    if (!form) return; // Si no encuentra el formulario, termina la función

    form.onsubmit = function(e) {
        // Selecciona todos los campos de nombre de usuario
        var camposUsuario = document.querySelectorAll("input[name='username[]']");
        
        for (var i = 0; i < camposUsuario.length; i++) {
            if (camposUsuario[i].value.includes(" ")) {
                alert("Los nombres de usuario no deben contener espacios en blanco.");
                e.preventDefault(); // Evita que el formulario se envíe
                return false;
            }
        }
        return true;
    };
}
document.addEventListener("DOMContentLoaded", function() {
    validarCamposUsuarioSinEspacios();
});
