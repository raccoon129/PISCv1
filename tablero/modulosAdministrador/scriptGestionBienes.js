function abrirEditarBien() {
    var url = 'gestionBienesEditarBien.php';
    window.open(url, "Editar Bien", "width=1100,height=600");
}

function abrirFormularioNuevoBien() {
    var url = 'gestionBienesInsertarBien.php';
    window.open(url, "Nuevo Bien", "width=400,height=800");
}

function abrirExportarDatosTabla() {
    var url = 'gestionBienesExportarTabla.php';
    window.open(url, "Exportar datos de la tabla", "width=1100,height=500");
}

function confirmDelete(inventarioID) {
    var confirmacion = confirm("¿Estás seguro de que deseas eliminar este registro?");
    if (confirmacion) {
        window.location.href = "gestionBienesEliminarRegistro.php?id=" + inventarioID;
    }
}

$(document).ready(function() {
    $('#tablaBienes').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        },
        "pagingType": "full_numbers",
        "order": [
            [0, "desc"]
        ] // Configuración para ordenar los datos de forma descendente en la carga inicial
    });
});