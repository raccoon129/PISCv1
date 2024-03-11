$(document).ready(function() {
    $('#prestamosActivos').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        },
        "pagingType": "full_numbers",
        "order": [
            [0, "desc"]
        ]
    });
});

function abrirDetallesPrestamo(numeroVale) {
    var url = '../modulosPrestamista/detallesPrestamo.php?numeroVale=' + numeroVale;
    window.open(url, 'Detalles del Préstamo', 'width=700,height=700');
}
/*
function generarVale(numeroVale) {
    var url = 'generarVale.php?numeroVale=' + numeroVale;
    window.open(url, 'Generar Vale', 'width=700,height=700');
}
*/
function abrirGenerarVale(numeroVale) {
    var url = "../modulosPrestamista/generarVale.php";
    var form = document.createElement("form");
    form.target = "_blank"; // Para abrir en una nueva ventana
    form.method = "POST";
    form.action = url;
    form.style.display = "none";

    var input = document.createElement("input");
    input.type = "hidden";
    input.name = "noNuevoVale";
    input.value = numeroVale;
    form.appendChild(input);

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function eliminarPrestamoFormulario(numeroVale) {
    var url = 'admonHistorialPrestamosEliminarPrestamo.php?numeroVale=' + numeroVale;
    window.open(url, 'Detalles del Préstamo', 'width=700,height=400');
}