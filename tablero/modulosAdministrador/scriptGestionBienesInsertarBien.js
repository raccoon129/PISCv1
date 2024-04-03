const inputs = document.querySelectorAll('#formInsertarBien input[type="text"], #formInsertarBien select');
const btnGuardar = document.getElementById('btnGuardar');

function verificarCampos() {
    let todosLlenos = true;
    inputs.forEach(input => {
        if (input.value.trim() === '') {
            todosLlenos = false;
        }
    });

    if (todosLlenos) {
        btnGuardar.disabled = false;
    } else {
        btnGuardar.disabled = true;
    }
}

inputs.forEach(input => input.addEventListener('input', verificarCampos));

// Inicialmente verificar los campos en caso de que el navegador rellene automáticamente los campos.
verificarCampos();

document.getElementById('formInsertarBien').addEventListener('submit', function (event) {
    var ubicacion = document.getElementById('ubicacion').value;
    if (ubicacion === "...") {
        alert("Selecciona una ubicación del bien.");
        event.preventDefault(); // Prevenir el envío del formulario
    }
});

// Aqui empieza otra parte
document.getElementById('formInsertarBien').addEventListener('submit', function (event) {
    const bienID = document.getElementById('bienID').value.trim();
    const descripcion = document.getElementById('descripcion').value.trim();
    const marca = document.getElementById('marca').value.trim();
    const noSerie = document.getElementById('noSerie').value.trim();
    const modelo = document.getElementById('modelo').value.trim();
    const imagenBien = document.getElementById('imagenBien').value; // Verificar si hay un archivo seleccionado

    if (!bienID || !descripcion || !marca || !noSerie || !modelo) {
        event.preventDefault(); // Prevenir el envío del formulario si hay errores
        alert('Por favor, rellene todos los campos.');
    } else {
        // Mostrar el loader
        document.getElementById('loader').style.display = 'inline-block';
        document.getElementById('btnGuardar').disabled = true; // Opcional: deshabilitar el botón para evitar múltiples envíos
    }

    var formData = new FormData(this);
    formData.append('bienID', bienID);
    formData.append('descripcion', descripcion);
    formData.append('marca', marca);
    formData.append('noSerie', noSerie);
    formData.append('modelo', modelo);
    formData.append('imagenBien', imagenBien);

    fetch('procesarImagenes.php', {
        method: 'POST',
        body: formData
    })
        .then(response => {
            if (response.ok) {
                return response.text();
            }
            throw new Error('Algo salió mal al procesar la imagen.');
        })
        .then(data => {
            console.log(data);
            alert('El bien y la imagen se han guardado correctamente.');
            window.opener.location.reload(); // Recargar la página padre
            window.close(); // Cerrar la ventana actual
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al guardar la imagen.');
        });
});