$(document).ready(function () {

    //Para evitar que se introduzcan espacios en blanco
    $('#btnRegistrarPrestamo').click(function (event) {
        var isValid = true;
        var campos = ['#nombreDeudor', '#area', '#unidad', '#responsable'];
    
        campos.forEach(function (campo) {
            var valor = $(campo).val().trim();
            if (!valor) {
                isValid = false;
                alert('Por favor, rellene todos los campos. Los espacios en blanco no son válidos.');
                $(campo).focus();
                event.preventDefault(); // Prevenir la acción de submit si hay un campo no válido
                return; // Salir de esta iteración del ciclo forEach
            }
        });
    
        if (!isValid) {
            event.preventDefault(); // Prevenir la acción de submit si hay un campo no válido
        }
    });
    

    //Este es el contador de caracteres

    $('#observaciones').on('input', function () {
        var caracteres = $(this).val().length;
        $('#contadorCaracteres').text(caracteres); // Actualiza el contador

        // Si excede los 240 caracteres, muestra un aviso 
        if (caracteres > 255) {
            alert('El texto no debe exceder los 240 caracteres.');
            // Truncar el valor a 255 caracteres
            $(this).val($(this).val().substring(0, 240));
            $('#contadorCaracteres').text(240);
        }
    });



    // Variables temporales para almacenar los datos del bien seleccionado
    let tempBienID = '';
    let tempDescripcion = '';
    let tempUbicacion = '';

    //acá se actualiza el campo oculto abajo de la tabla para los datos

    function actualizarCampoOculto() {
        var bienesID = [];
        $("#tablaBienes tbody tr").each(function () {
            var bienID = $(this).find("td:nth-child(2)").text(); // Captura la segunda columna de bienID
            bienesID.push(bienID);
        });
        $("#bienesID").val(JSON.stringify(bienesID)); // Actualiza el campo oculto con el array de bienesID

        // Habilitar o deshabilitar el botón "Registrar Préstamo" basado en si hay bienes agregados o no
        if (bienesID.length > 0) {
            $("#btnRegistrarPrestamo").prop('disabled', false);
        } else {
            $("#btnRegistrarPrestamo").prop('disabled', true);
        }
    }

    // Llama a actualizarCampoOculto() inicialmente por si hay datos preexistentes
    actualizarCampoOculto();



    // Animacion cuando se escribe en el input de descripcionBien
    $("#descripcionBien").keyup(function () {
        var descripcion = $(this).val();
        if (descripcion != '') {
            $.ajax({
                url: "buscarBienNuevoPrestamo.php",
                method: "GET",
                data: {
                    descripcion: descripcion
                },
                success: function (data) {
                    $('#listaBienes').html(data).slideDown('slow'); // Usa slideDown para mostrar los resultados con animación
                }
            });
        } else {
            $('#listaBienes').slideUp('slow'); // Usa slideUp para ocultar los resultados con animación
        }
    });
    

    $(document).on('click', '.bien-item', function () {
        tempDescripcion = $(this).data('descripcion');
        tempBienID = $(this).data('bienid');
        tempUbicacion = $(this).data('ubicacion'); // Capturar la ubicación del bien seleccionado
        $('#descripcionBien').val(tempDescripcion);
        $('#listaBienes').fadeOut();

        $('#ubicacionBien').val(tempUbicacion);
    });

    $("#agregarBien").click(function () {
        var bienYaAgregado = false;
        $("#tablaBienes tbody tr").each(function () {
            var bienIDEnLista = $(this).find("td:nth-child(2)").text();
            if (bienIDEnLista === tempBienID) {
                bienYaAgregado = true;
                return false; // Salir del bucle si el bien ya fue agregado
            }
        });

        if (bienYaAgregado) {
            alert("Este bien ya ha sido agregado a la lista.");
        } else if (tempDescripcion !== "" && tempBienID !== "" && tempUbicacion !== "") {
            var newRow = `<tr>
                <td>${tempDescripcion}</td>
                <td>${tempBienID}</td>
                <td>${tempUbicacion}</td> <!-- Añadir la ubicación en la nueva columna -->
                <td><button type="button" class="btn btn-danger removeBien">Quitar</button></td>
              </tr>`;
            $("#tablaBienes tbody").append(newRow);

            // Limpiar variables temporales y campos de entrada
            tempDescripcion = '';
            tempBienID = '';
            tempUbicacion = '';
            $("#descripcionBien").val("");
            $("#ubicacionBien").val("");

            if ($("#tablaBienes tbody tr").length >= 6) {
                $("#agregarBien").prop('disabled', true);
            }
            actualizarEstilosBienesSeleccionados();
            actualizarCampoOculto();

        } else {
            alert("Debe seleccionar un bien de la lista. Escriba en el campo 'Descripcion del Bien'.");
        }
    });

    $(document).on('click', '.removeBien', function () {
        $(this).closest('tr').remove();
        if ($("#tablaBienes tbody tr").length < 6) {
            $("#agregarBien").prop('disabled', false);
        }
        actualizarEstilosBienesSeleccionados();
        actualizarCampoOculto();
    });

    // Función para actualizar los estilos de los bienes ya seleccionados
    function actualizarEstilosBienesSeleccionados() {
        // Itera sobre cada bien en la tabla de bienes agregados
        $("#tablaBienes tbody tr").each(function () {
            var bienID = $(this).find("td:nth-child(2)").text(); // Asume que el Bien ID está en la segunda columna

            // Itera sobre cada elemento en la lista de bienes y compara los IDs
            $("#listaBienes .bien-item").each(function () {
                var listItemID = $(this).data("id"); // Asume que usas data-id para identificar cada bien

                if (listItemID == bienID) {
                    $(this).css("color", "red"); // Marca el elemento de la lista con color rojo
                }
            });
        });
    }
});


$("form").submit(function (e) { //Para ver en consola lo que se manda mientras lo refactorizo
    console.log($("#bienesID").val());
});


$(document).ready(function () {
    $('#btnRegistrarPrestamo').click(function (event) {
        var isValid = true;
        var campos = ['#nombreDeudor', '#area', '#unidad', '#responsable'];

        // Validación de campos vacíos
        campos.forEach(function (campo) {
            var valor = $(campo).val().trim();
            if (!valor) {
                isValid = false;
                alert('Por favor, rellene todos los campos. Los espacios en blanco no son válidos.');
                $(campo).focus();
                event.preventDefault(); // Prevenir la acción de submit si hay un campo no válido
                return; // Salir de esta iteración del ciclo forEach
            }
        });

        // Comprobar si hay bienes seleccionados
        var bienesID = JSON.parse($('#bienesID').val());
        if (bienesID.length === 0) {
            alert("Debe agregar al menos un bien para registrar el préstamo.");
            event.preventDefault(); // Prevenir la acción de submit si no hay bienes
            isValid = false;
        }

        if (!isValid) {
            event.preventDefault(); // Prevenir la acción de submit
        }
    });

    // Contador de caracteres en observaciones
    $('#observaciones').on('input', function () {
        var caracteres = $(this).val().length;
        $('#contadorCaracteres').text(caracteres);

        if (caracteres > 240) {
            alert('El texto no debe exceder los 240 caracteres.');
            $(this).val($(this).val().substring(0, 240));
            $('#contadorCaracteres').text(240);
            $('#btnRegistrarPrestamo').prop('disabled', true);
        } else {
            $('#btnRegistrarPrestamo').prop('disabled', false);
        }
    });
});


//Para rectificar las fechas 

document.addEventListener('DOMContentLoaded', function() {
    const formulario = document.querySelector('form'); 

    formulario.addEventListener('submit', function(event) {
        const fechaSolicitud = document.getElementById('fechaSolicitud').value;
        const fechaDevolucionPrevista = document.getElementById('fechaDevolucionPrevista').value;

        if (fechaDevolucionPrevista < fechaSolicitud) {
            alert('La fecha de devolución prevista no puede ser menor que la fecha de solicitud.');
            event.preventDefault(); // Prevenir que el formulario se envíe
            return;
        }
        
        if (fechaSolicitud > fechaDevolucionPrevista){
            alert('La fecha de solicitud no puede ser mayor a la fecha de devolución prevista.');
            event.preventDefault();
            return;
        }
    });
});
