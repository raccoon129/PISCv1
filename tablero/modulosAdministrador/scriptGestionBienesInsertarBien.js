//ESTA ES UNA FUNCIONALIDAD CRITICA.
document.addEventListener('DOMContentLoaded', function () {
    // Obtener referencias a los elementos del formulario
    const form = document.getElementById('formInsertarBien');
    const bienIDInput = document.getElementById('bienID');
    const imagenBienInput = document.getElementById('imagenBien');
    const bienIDIcon = document.getElementById('bienIDIcon');
    const btnGuardar = document.getElementById('btnGuardar');
    const loader = document.getElementById('loader');  // Obtener referencias a los elementos del formulario
    const inputs = document.querySelectorAll('#formInsertarBien input[type="text"], #formInsertarBien select');
    const ubicacionSelect = document.getElementById('ubicacion');

    // Evento que se dispara con cada entrada en el campo BienID
    bienIDInput.addEventListener('input', function() {
        verificarBienIDExistente(this.value.trim());
    });
    // Verifica si el BienID ya existe en la base de datos
    function verificarBienIDExistente(bienID) {
        if (bienID === '') {
            bienIDIcon.innerHTML = '';
            console.log('BienID está vacío');
            btnGuardar.disabled = true;
            return;
        }
        // Llamada al servidor para verificar la existencia del BienID
        fetch('consultaGestionBienesInsertarBien.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `accion=verificarBienID&bienID=${bienID}`
        })
        .then(response => response.json())
        .then(data => {
            console.log('BienID existe:', data.existe);
            if (data.existe) {
                bienIDIcon.innerHTML = '<i class="fas fa-times text-danger"></i>';
                btnGuardar.disabled = true;
            } else {
                bienIDIcon.innerHTML = '<i class="fas fa-check text-success"></i>';
                verificarCampos();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    // Verifica que todos los campos estén llenos y sean válidos antes de habilitar el botón de guardar
    function verificarCampos() {
        let todosLlenos = Array.from(inputs).every(input => input.value.trim() !== '');
        let ubicacionValida = ubicacionSelect.value !== "";
        btnGuardar.disabled = !todosLlenos || !ubicacionValida || bienIDIcon.innerHTML.includes('fa-times');
    }
     // Elimina espacios al inicio y al final en cada entrada y verifica los campos
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            input.value = input.value.trim(); // Elimina espacios al inicio y al final en cada entrada
            verificarCampos();
        });
    });
    ubicacionSelect.addEventListener('change', verificarCampos);

    // Envio del formulario
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        btnGuardar.disabled = true;
        loader.style.display = 'inline-block';  // Muestra el loader

        if (!ubicacionSelect.value) {
            alert('Seleccione una ubicación válida.');
            btnGuardar.disabled = false;
            loader.style.display = 'none';  // Oculta el loader
            return;
        }

        const formData = new FormData(form);

        if (imagenBienInput.files.length > 0) {
            const formDataImagen = new FormData();
            formDataImagen.append('imagenBien', imagenBienInput.files[0]);
            formDataImagen.append('bienID', bienIDInput.value.trim());

            fetch('procesarImagenes.php', {
                method: 'POST',
                body: formDataImagen
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Imagen procesada correctamente');
                    procesarFormularioCompleto(formData);
                } else {
                    console.error('Error procesando la imagen:', data.message);
                    alert('Error procesando la imagen: ' + data.message);
                    btnGuardar.disabled = false;
                    loader.style.display = 'none';  // Oculta el loader
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error procesando la imagen.');
                btnGuardar.disabled = false;
                loader.style.display = 'none';  // Oculta el loader
            });
        } else {
            procesarFormularioCompleto(formData);
        }
    });
    // Procesa el formulario completo después de la carga de la imagen
    function procesarFormularioCompleto(formData) {
        fetch('consultaGestionBienesInsertarBien.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Se han insertado los datos' + (imagenBienInput.files.length > 0 ? ' y procesado la imagen' : '') + ' de forma correcta.');
                window.opener.location.reload();
                window.close();
            } else {
                alert('Ocurrió un error al insertar los datos.');
                btnGuardar.disabled = false;
                loader.style.display = 'none';  // Oculta el loader
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al insertar los datos.');
            btnGuardar.disabled = false;
            loader.style.display = 'none';  // Oculta el loader
        });
    }

    verificarCampos();
});
