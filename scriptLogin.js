document.addEventListener("DOMContentLoaded", function () {
    var logo = document.querySelector(".contenedor-logo img");
    var overlay = document.getElementById("overlay");

    function removerOverlay() {
        overlay.classList.remove("show");
    }

    logo.addEventListener("click", function () {
        overlay.classList.add("show");
        setTimeout(function () {
            window.location.href = "about.html";
        }, 1000);
    });

    removerOverlay();

    var botonIngresar = document.getElementById("botonIngresar");
    var formularioLogin = document.getElementById("formularioLogin");
    var campoNombreUsuario = document.getElementById("nombreUsuario");
    var campoContrasena = document.getElementById("contrasena");

    // Función para manejar la lógica de ingreso
    function ingresarAlSistema() {
        if (campoNombreUsuario.value.trim() !== "" && campoContrasena.value.trim() !== "") {
            overlay.classList.add("show");
            setTimeout(function () {
                formularioLogin.submit();
            }, 1000);
        } else {
            //alert("Por favor, complete todos los campos.");
        }
    }

    // Evento clic en el botón ingresar
    botonIngresar.addEventListener("click", ingresarAlSistema);

    // Evento keydown para el formulario
    document.addEventListener("keydown", function (event) {
        if (event.key === "Enter") {
            ingresarAlSistema();
        }
    });
});


// Se activa cuando se le da clic en el botón "Ingresar al sistema"
var botonIngresar = document.getElementById("botonIngresar");
var formularioLogin = document.getElementById("formularioLogin");
var campoNombreUsuario = document.getElementById("nombreUsuario");
var campoContrasena = document.getElementById("contrasena");

botonIngresar.addEventListener("click", function () {
    // Verificar si los campos de usuario y contraseña han sido completados
    if (campoNombreUsuario.value.trim() !== "" && campoContrasena.value.trim() !== "") {
        // Añade la clase 'show' al overlay para mostrarlo
        overlay.classList.add("show");

        // Espera un poco antes de enviar el formulario para que la animación se complete
        setTimeout(function () {
            formularioLogin.submit();
        }, 1000); // Tiempo en milisegundos (1 segundo en este caso para la animacion del overlay)
    } else {
        // Mostrar un mensaje de error si algún campo está vacío
        alert("Por favor, complete todos los campos.");
    }
});