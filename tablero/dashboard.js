
//ESTE SCRIPT ES CRITICO PARA EL FUNCIONAMIENTO DEL DASHBOARD Y LA PLATAFORMA EN GENERAL.
//DOCUMENTADOD DE FORMA VERBAL

// Se ejecuta cuando el contenido del DOM está completamente cargado.
document.addEventListener('DOMContentLoaded', function () {
    // Carga el contenido basado en la URL actual al cargar la página.
    cargarContenidoSegunURL();

    // Selecciona todos los enlaces dentro del menú.
    var elementosMenu = document.querySelectorAll('.menu li a');
    elementosMenu.forEach(function (elemento) {
        // Añade un listener de evento click a cada enlace.
        elemento.addEventListener('click', function (evento) {
            evento.preventDefault(); // Previene la acción por defecto del enlace (navegación directa).
            // Remueve la clase 'seleccionado' de todos los elementos para asegurar que solo el clickeado la tenga.
            elementosMenu.forEach(function (el) {
                el.classList.remove('seleccionado');
            });
            // Añade la clase 'seleccionado' al elemento clickeado para marcarlo visualmente.
            evento.target.classList.add('seleccionado');
        });
    });
});

// Función para cargar un módulo dentro del iframe.
function cargarModulo(ref, realRef) {
    const iframe = document.getElementById('contenidoIframe'); // Selecciona el iframe.
    iframe.src = 'loader1.html'; // Muestra el loader inicialmente.

    // Se ejecuta cuando el loader (o cualquier contenido) dentro del iframe ha terminado de cargar.
    iframe.onload = function () {
        // Actualiza la URL en la barra de direcciones sin recargar la página.
        window.history.pushState({ path: ref }, '', realRef);
        iframe.src = ref; // Cambia el src del iframe al módulo deseado.

        // Llama a la función para marcar el botón correspondiente en el menú.
        marcarBotonCorrespondiente(realRef);

        // Elimina el manejador onload para evitar que se ejecute de nuevo después de cargar el módulo deseado.
        iframe.onload = null;
    };
}

// Función para marcar el botón correspondiente en el menú basado en la URL.
function marcarBotonCorrespondiente(hash) {
    var elementosMenu = document.querySelectorAll('.menu li a');
    elementosMenu.forEach(function (elemento) {
        // Compara el href del elemento con el hash de la URL.
        if (elemento.getAttribute('href') === hash) {
            elemento.classList.add('seleccionado'); // Marca el elemento si coincide.
        } else {
            elemento.classList.remove('seleccionado'); // Remueve la marca si no coincide.
        }
    });
}

// Función para cargar el contenido basado en el hash de la URL actual.
function cargarContenidoSegunURL() {
    const path = window.location.hash; // Obtiene el hash de la URL.
    if (path) {
        // Intenta seleccionar el elemento del menú que corresponda al hash.
        const elemento = document.querySelector(`.menu li a[href='${path}']`);
        if (elemento) {
            elemento.click(); // Simula un click si el elemento existe.
        } else {
            // Si no hay un elemento que coincida, carga un módulo predeterminado.
            cargarModulo('default.php', '#inicio');
        }
    } else {
        // Carga un módulo predeterminado si no hay hash en la URL.
        cargarModulo('default.php', '#inicio');
    }
}

// Escucha el evento de cambio de estado de navegación (como clics en adelante/atrás del navegador).
window.addEventListener('popstate', function (event) {
    cargarContenidoSegunURL(); // Carga el contenido basado en el hash actual de la URL.
});