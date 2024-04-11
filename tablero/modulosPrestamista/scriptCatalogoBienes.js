function verFotoBien(bienID) {
    window.open("../modulosAdministrador/gestionBienesVisualizarFotoBien.php?bienID=" + bienID, "_blank", "width=600,height=400");
}

$(document).ready(function () {
    $('#tablaBienes').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        },
        "pageLength": 10,
        "lengthMenu": [5, 10, 15, 20, 25, 50, 100],
        "ordering": true,
        "searching": true,
        "pagingType": "full_numbers",
        "order": [
            [0, "desc"]
        ] // Configuración para ordenar los datos de forma descendente en la carga inicial   
    });
}); 