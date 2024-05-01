function abrirEditarBien() {
    var url = 'gestionBienesEditarBien.php';
    window.open(url, "Editar Bien", "width=1250,height=600");
}

function abrirFormularioNuevoBien() {
    var url = 'gestionBienesInsertarBien.php';
    window.open(url, "Nuevo Bien", "width=1100,height=600");
}

function abrirExportarDatosTabla() {
    var url = 'gestionBienesExportarTabla.php';
    window.open(url, "Exportar datos de la tabla", "width=1100,height=500");
}

function abrirAuditarBajas() {
    var url = 'gestionBienesAuditarBajas.php';
    window.open(url, "Exportar datos de la tabla", "width=1100,height=500");
}

function abrirVolcarDatos() {
    var url = 'gestionBienesVolcarDatosBienes.php';
    window.open(url, "Volcado masivo de datos", "width=1300,height=700");
}

function verFoto(bienID) {
    var url = "gestionBienesVisualizarFotoBien.php?bienID=" + bienID;
    window.open(url, "_blank", "width=1250,height=600");
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