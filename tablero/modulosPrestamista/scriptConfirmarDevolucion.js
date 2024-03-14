$(document).ready(function () {
    $('#btnConfirmarDevolucion').click(function () {
        var numeroVale = $('#numeroVale').val();
        var personaRecibe = $('#usuarioConfirmaDevolucion').val().replace(/ /g, "_"); // Reemplazar espacios por guiones bajos

        Swal.fire({
            title: 'Atención',
            text: "Confirma que los bienes han sido devueltos en un estado especificado y que el préstamo se dará por finalizado. La acción no se puede revertir.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Correcto'
        }).then((result) => {
            if (result.isConfirmed) {
                // Crear un formulario y enviarlo, incluyendo el nombre completo del usuario reemplazado con guiones bajos.
                var form = $('<form action="actualizacionConfirmarDevolucion.php" method="post">' +
                    '<input type="hidden" name="numeroVale" value="' + numeroVale + '">' +
                    '<input type="hidden" name="usuarioConfirmaDevolucion" value="' + personaRecibe + '">' +
                    '</form>');
                $('body').append(form);
                form.submit();
            }
        });
    });
});

