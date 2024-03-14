$(document).ready(function() {
    // Variables temporales para almacenar los datos del bien seleccionado
    let tempBienID = '';
    let tempDescripcion = '';
    let tempUbicacion = '';

    //acá se actualiza el campo oculto abajo de la tabla para los datos

    function actualizarCampoOculto() {
        var bienesID = [];
        $("#tablaBienes tbody tr").each(function() {
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
    
    


    $("#descripcionBien").keyup(function() {
        var descripcion = $(this).val();
        if (descripcion != '') {
            $.ajax({
                url: "buscarBienNuevoPrestamo.php",
                method: "GET",
                data: {
                    descripcion: descripcion
                },
                success: function(data) {
                    $('#listaBienes').fadeIn().html(data);
                }
            });
        } else {
            $('#listaBienes').fadeOut().html("");
        }
    });

    $(document).on('click', '.bien-item', function() {
        tempDescripcion = $(this).data('descripcion');
        tempBienID = $(this).data('bienid');
        tempUbicacion = $(this).data('ubicacion'); // Capturar la ubicación del bien seleccionado
        $('#descripcionBien').val(tempDescripcion);
        $('#listaBienes').fadeOut();

        $('#ubicacionBien').val(tempUbicacion);
    });

    $("#agregarBien").click(function() {
        var bienYaAgregado = false;
        $("#tablaBienes tbody tr").each(function() {
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

    $(document).on('click', '.removeBien', function() {
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
        $("#tablaBienes tbody tr").each(function() {
            var bienID = $(this).find("td:nth-child(2)").text(); // Asume que el Bien ID está en la segunda columna

            // Itera sobre cada elemento en la lista de bienes y compara los IDs
            $("#listaBienes .bien-item").each(function() {
                var listItemID = $(this).data("id"); // Asume que usas data-id para identificar cada bien

                if(listItemID == bienID) {
                    $(this).css("color", "red"); // Marca el elemento de la lista con color rojo
                }
            });
        });
    }
});


$("form").submit(function(e) { //Para ver en consola lo que se manda mientras lo refactorizo
    console.log($("#bienesID").val());
});


//contar caracteres para evitar que las observaciones sean extensas y evitar errores en la DB (Varchar 255)
$(document).ready(function() {
    $('#observaciones').on('input', function() {
        // Contar los caracteres de las observaciones
        var caracteres = $(this).val().length;
        
        // Si excede los 240 caracteres, deshabilita el botón
        if (caracteres > 240) {
            $('#btnRegistrarPrestamo').prop('disabled', true);
        } else {
            // De lo contrario, habilitar
            $('#btnRegistrarPrestamo').prop('disabled', false);
        }
    });
});
