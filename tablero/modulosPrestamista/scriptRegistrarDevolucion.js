$(document).ready(function() {
    $('#prestamosActivos').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        },
        "pagingType": "full_numbers",
        "order": [
            [0, "desc"]
        ] // Configuración para ordenar los datos de forma descendente en la carga inicial
    });
});
$(document).on('click', '.registrar-devolucion', function() {
    var numeroVale = $(this).data('numero-vale');
    abrirFormularioConfirmarDevolucion(numeroVale); // Pasar numeroVale como argumento
});

function abrirFormularioConfirmarDevolucion(numeroVale) {
    var url = 'confirmarDevolucion.php?numeroVale=' + numeroVale;
    window.open(url, 'Confirmar Devolución', 'width=700,height=700');
}