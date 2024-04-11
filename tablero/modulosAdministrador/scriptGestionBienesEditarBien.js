document.addEventListener('DOMContentLoaded', function() {
    var fileInput = document.getElementById('imagenBien');
    var uploadButton = document.getElementById('uploadButton');
    var loader = document.getElementById('loader');
    var form = document.getElementById('formCargarImagen');

    fileInput.addEventListener('change', function() {
        uploadButton.disabled = this.files.length === 0;
    });

    form.addEventListener('submit', function(event) {
        event.preventDefault(); // Evitar el envío automático del formulario

        loader.style.display = 'inline-block';
        uploadButton.disabled = true;
        uploadButton.textContent = 'Procesando... No cierre esta ventana.';

        var formData = new FormData(form);
        
        fetch('procesarImagenes.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            loader.style.display = 'none';
            uploadButton.disabled = true;
            uploadButton.textContent = 'Hecho';

            // Mostrar un mensaje más legible en el alert
            alert(data.message);
            
            // Refrescar la ventana después de aceptar el alert
            if (data.success) {
                console.log('Imagen cargada correctamente');
                window.location.reload();
            } else {
                console.error('Error cargando la imagen:', data.message);
            }
        })
        .catch(error => {
            loader.style.display = 'none';
            uploadButton.disabled = false;
            uploadButton.textContent = 'Cargar nuevamente';
            console.error('Error:', error);
            alert('Error en la carga de la imagen.');
        });
    });
});
