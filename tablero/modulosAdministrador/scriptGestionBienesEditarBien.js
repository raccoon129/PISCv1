// ESTE SCRIPT ES UNICAMENTE PARA AJUSTAR EL BOTON DE LA CARGA DE IMAGENES 
// EN EL CASO QUE NO SE SELECCIONE ALGUNA PARA PREVENIR ERRORES
document.addEventListener('DOMContentLoaded', function() {
    var fileInput = document.getElementById('imagenBien');
    var uploadButton = document.getElementById('uploadButton');
    var loader = document.getElementById('loader');
    var form = document.getElementById('formCargarImagen');

    fileInput.addEventListener('change', function() {
        uploadButton.disabled = this.files.length === 0;
    });

    form.addEventListener('submit', function() {
        // Mostrar el loader dentro del botón
        loader.style.display = 'inline-block';
        uploadButton.disabled = true;
        uploadButton.textContent = 'Procesando... No cierre esta ventana.'; 
    });
});

