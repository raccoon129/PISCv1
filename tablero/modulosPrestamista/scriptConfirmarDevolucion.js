
$(document).ready(function () {
    $('#btnConfirmarDevolucion').click(function () {
        var numeroVale = $('#numeroVale').val(); // Asegúrate de obtener el número de vale correctamente
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
                // Crear un formulario y enviarlo
                var form = $('<form action="actualizacionConfirmarDevolucion.php" method="post">' +
                    '<input type="hidden" name="numeroVale" value="' + numeroVale + '">' +
                    '</form>');
                $('body').append(form);
                form.submit();
            }
        });
    });
});
/*   $('#btnConfirmarDevolucion').click(function() {
       var numeroVale = $('#numeroVale').val();
       if (numeroVale === undefined || numeroVale === '') {
           alert('El número de vale no está definido o es vacío.');
           return; // Detener la ejecución si no hay un número de vale válido
       }

       var confirmar = confirm("Confirma que los bienes han sido devueltos en un estado especificado y que el préstamo se dará por finalizado. La acción no se puede revertir.");
       if (confirmar) {
           var form = $('<form action="actualizacionConfirmarDevolucion.php" method="post">' +
               '<input type="hidden" name="numeroVale" value="' + numeroVale + '">' +
               '</form>');
           $('body').append(form);
           form.submit();
       }
   });
   */