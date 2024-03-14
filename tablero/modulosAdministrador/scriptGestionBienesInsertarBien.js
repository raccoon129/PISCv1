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

document.getElementById('formInsertarBien').addEventListener('submit', function(event) {
    var ubicacion = document.getElementById('ubicacion').value;
    if (ubicacion === "...") {
        alert("Selecciona una ubicación del bien. Presiona enter.");
        event.preventDefault(); // Prevenir el envío del formulario
    }
});

// Aqui empieza otra parte
document.getElementById('formInsertarBien').addEventListener('submit', function(event) {
    const bienID = document.getElementById('bienID').value.trim();
    const descripcion = document.getElementById('descripcion').value.trim();
    const marca = document.getElementById('marca').value.trim();
    const noSerie = document.getElementById('noSerie').value.trim();
    const modelo = document.getElementById('modelo').value.trim();

    if (!bienID || !descripcion || !marca || !noSerie || !modelo) {
        event.preventDefault(); 
        alert('Por favor, rellene todos los campos de manera adecuada.');
    }
});