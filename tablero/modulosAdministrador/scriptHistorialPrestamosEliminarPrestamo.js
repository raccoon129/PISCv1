document.getElementById('eliminarPrestamoBtn').addEventListener('click', function() {
    Swal.fire({
        title: 'Confirmación',
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Eliminar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Enviar el formulario
            document.getElementById('eliminarPrestamoForm').submit();
        }
    });
});