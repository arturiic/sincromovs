let terminoActual = "";
let filaSeleccionada = null;
let pagina = 1;

var table = "";

$(document).ready(function () {
    $("#buscador").keyup(function () {
        terminoActual = $(this).val();
        pagina = 1; // Reiniciar la paginación
        buscarDestinatarios(true); 
    });
    // 1. Evento para limpiar campos al cambiar de pestaña
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        var targetTab = $(e.target).attr('href'); // Obtiene el ID del tab activo

        // 2. Decide qué función de limpieza ejecutar según el tab
        if (targetTab === '#tabentrada') {
            obtenerMovimientos(); // Recarga datos si es necesario
        } else if (targetTab === '#tabsalida') {
            obtenerMovimientoSalida(); // Recarga datos si es necesario
        }
    });
    $("#cargarMas").click(function () {
        pagina++; // Aumentar la página
        buscarDestinatarios(false);
    });
    $("#datefecha").change(function () {
        obtenerMovimientos();
    });
    $("#datefecha2").change(function () {
        obtenerMovimientoSalida();
    });
    $("#cmbdetentempresa").change(function () {
        obtenerMovimientos();
    });
    $("#cmbdetentempresa2").change(function () {
        obtenerMovimientoSalida();
    });
    obtenerMovimientos();
});
function elegirDestinatario() {
    filaSeleccionada = $(this).closest("tr");
    $('#buscador').val('');
    $('#resultados').html('');
    let totalPaginas = 0;
    $("#cargarMas").toggle(pagina < totalPaginas);
    $('#mdleledestinatario').modal('show');
}

function abrirModalPDF() {
    $('#lbltitulos').html('Generar reporte');
    $('#mdlpdf').modal('show');
}

function abrirModalSaldo() {
    $('#lbltitulo3').html('Ingresar Saldo');
    $('#mdlingsaldo').modal('show');
    limpiarSaldo();
}

function buscarDestinatarios(limpiar) {
    if (terminoActual.length >= 1) {
        $.ajax({
            url: URL_PY + "destinatarios/busc_destinatarios",
            method: "GET",
            data: { q: terminoActual, page: pagina },
            dataType: "json",
            success: function (data) {
                //console.log(data);

                if (limpiar) $("#resultados").html(""); // Limpiar resultados si es una nueva búsqueda

                $.each(data.destinatarios, function (index, destinatarios) {
                    $("#resultados").append(`
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><strong>${destinatarios.nombre}</strong> <br> 
                                
                            </span>
                            <button class="btn btn-6 btn-primary active btn-pill w-10 escogerDestinatario" 
                                data-id="${destinatarios.iddestinatario}" 
                                data-nombre="${destinatarios.nombre}">
                                <i class="fas fa-check"></i>
                            </button>
                        </li>
                    `);
                });

                let totalPaginas = Math.ceil(data.total / data.limite);
                $("#cargarMas").toggle(pagina < totalPaginas);
            },
            error: function () {
                //console.error("Error en la búsqueda.");
            }
        });
    }
}

$(document).on("click", ".escogerDestinatario", function () {
    let iddestinatario = $(this).data("id");
    let nombreDestinatario = $(this).data("nombre");
    $("#txtiddest").val(iddestinatario);
    $("#txtdestinatario").val(nombreDestinatario);
    $("#mdleledestinatario").modal('hide');
});

$(document).on("click", ".escogerDestinatario", function () {
    let iddestinatario = $(this).data("id");
    let nombreDestinatario = $(this).data("nombre");
    $("#txtiddest2").val(iddestinatario);
    $("#txtdestinatario2").val(nombreDestinatario);
    $("#mdleledestinatario").modal('hide');
});

function registrarMovEntrada() {
    var parametros =
        'Destinatario=' + $('#txtiddest').val() +
        '&Cuenta=' + $('#cmbdetentempresa').val() +
        '&Observacion=' + $('#txtobservacion').val() +
        '&Fecha=' + $('#datefecha').val() +
        '&Monto=' + $('#txtmonto').val() +
        '&Tipo=ENTRADA' +
        '&Noperacion=' + $('#txtnoperacion').val();
    $.ajax({
        type: "POST",
        url: URL_PY + 'movimientos/registrar_xml',
        data: parametros,
        success: function (response) {
            //console.log(response);
            if (response.includes('MOVIMIENTO FINANCIERO REGISTRADO')) {
                Swal.fire({
                    icon: 'success',
                    title: 'REGISTRO DE MOVIMIENTOS',
                    text: response,
                }).then(function () {
                    obtenerMovimientos();
                    limpiar();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'ERROR AL REGISTRAR',
                    text: response
                });
            }
        },
    });
}

function registrarMovSalida() {
    var parametros =
        'Destinatario=' + $('#txtiddest2').val() +
        '&Cuenta=' + $('#cmbdetentempresa2').val() +
        '&Observacion=' + $('#txtobservacion2').val() +
        '&Fecha=' + $('#datefecha2').val() +
        '&Monto=' + $('#txtmonto2').val() +
        '&Tipo=SALIDA' +
        '&Noperacion=' + $('#txtnoperacion2').val();
    $.ajax({
        type: "POST",
        url: URL_PY + 'movimientos/registrar_xml',
        data: parametros,
        success: function (response) {
            //console.log(response);
            if (response.includes('MOVIMIENTO FINANCIERO REGISTRADO')) {
                Swal.fire({
                    icon: 'success',
                    title: 'REGISTRO DE MOVIMIENTOS',
                    text: response
                }).then(function () {
                    obtenerMovimientoSalida();
                    limpiar2();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'ERROR AL REGISTRAR',
                    text: response
                });
            }
        },
    });
}

function obtenerMovimientos() {
    var fecha = $('#datefecha').val();
    var entidad = $('#cmbdetentempresa').val();

    $.ajax({
        type: "GET",
        url: URL_PY + 'movimientos/movEntradaXfecha',
        data: { fecha: fecha, entidad: entidad },
        success: function (response) {

            //console.log(response);
            var movimientos = response[0];
            var table = $('#tblmovimientos').DataTable();
            
            // Destruye la instancia existente de DataTable si existe
            if ($.fn.DataTable.isDataTable('#tblmovimientos')) {
                table.destroy();
            }
            //console.log('Respuesta completa del servidor:', response);
            $('#tblmovimientos tbody').empty();
            if (Array.isArray(movimientos)) {
                movimientos.forEach(function (mov) {
                    var fila = '<tr>' +
                        '<td>' + mov.destinatario + '</td>' +
                        '<td>' + mov.observacion + '</td>' +
                        '<td>' + mov.fecha + '</td>' +
                        '<td>' + mov.monto + '</td>' +
                        '<td>' + mov.noperacion + '</td>' +
                        '<td style="width: 1%; white-space: nowrap; padding-right: 0; text-align: center;">' +
                        '<div class="dropdown" style="display: inline-block;">' +
                        '<button class="btn btn-sm btn-primary dropdown-toggle" type="button" ' +
                        'style="padding: 0.25rem 0.5rem; min-width: 30px;" ' +
                        'id="dropdownMenuButton' + mov.idmov_finanzas + '" ' +
                        'data-bs-toggle="dropdown" aria-expanded="false">' +
                        '<i class="fa-solid fa-ellipsis-vertical"></i>' +
                        '</button>' +
                        '<ul class="dropdown-menu" ' +
                        'style="min-width: 120px; font-size: 0.875rem; padding: 0.25rem 0;" ' +
                        'aria-labelledby="dropdownMenuButton' + mov.idmov_finanzas + '">' +
                        '<li><a class="dropdown-item" href="#" style="padding: 0.25rem 1rem; color:rgb(226, 230, 105);" ' +
                        'onclick="mostrarMovSalidaX(' + mov.idmov_finanzas + ')">' +
                        '<i class="fa-solid fa-square-plus me-2"></i>Editar</a></li>' +
                        '<li><a class="dropdown-item" href="#" style="padding: 0.25rem 1rem; color: #dc3545;" ' +
                        'onclick="eliminarEntrada(' + mov.idmov_finanzas + ')">' +
                        '<i class="fa-solid fa-trash me-2"></i>Eliminar</a></li>' +
                        '</ul>' +
                        '</div>' +
                        '</td>' +
                        '</tr>';
                    $('#tblmovimientos').append(fila);
                });
                // Inicializa DataTables con las opciones
                $('#tblmovimientos').DataTable({
                    "language": {
                        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                    },
                    "retrieve": true, // Permite recuperar la instancia existente
                    "destroy": true // Permite destruir la instancia anterior si existe
                });
            }
        },
        error: function (xhr, status, error) {
            //console.error("Error en la solicitud:", status, error);
        }
    });
}

function obtenerMovimientoSalida() {
    var fecha = $('#datefecha2').val();
    var entidad = $('#cmbdetentempresa2').val();

    $.ajax({
        type: "GET",
        url: URL_PY + 'movimientos/movSalidaXfecha',
        data: { fecha: fecha, entidad: entidad },
        success: function (response) {

            //console.log(response);
            var movimientos = response[0];
            var table = $('#tblmovimientos').DataTable();
            
            // Destruye la instancia existente de DataTable si existe
            if ($.fn.DataTable.isDataTable('#tblmovimientos')) {
                table.destroy();
            }
            $('#tblmovimientos tbody').empty();
            if (Array.isArray(movimientos)) {
                movimientos.forEach(function (mov) {
                    var fila = '<tr>' +
                        '<td>' + mov.destinatario + '</td>' +
                        '<td>' + mov.observacion + '</td>' +
                        '<td>' + mov.fecha + '</td>' +
                        '<td>' + mov.monto + '</td>' +
                        '<td>' + mov.noperacion + '</td>' +
                        '<td style="width: 1%; white-space: nowrap; padding-right: 0; text-align: center;">' +
                        '<div class="dropdown" style="display: inline-block;">' +
                        '<button class="btn btn-sm btn-primary dropdown-toggle" type="button" ' +
                        'style="padding: 0.25rem 0.5rem; min-width: 30px;" ' +
                        'id="dropdownMenuButton' + mov.idmov_finanzas + '" ' +
                        'data-bs-toggle="dropdown" aria-expanded="false">' +
                        '<i class="fa-solid fa-ellipsis-vertical"></i>' +
                        '</button>' +
                        '<ul class="dropdown-menu" ' +
                        'style="min-width: 120px; font-size: 0.875rem; padding: 0.25rem 0;" ' +
                        'aria-labelledby="dropdownMenuButton' + mov.idmov_finanzas + '">' +
                        '<li><a class="dropdown-item" href="#" style="padding: 0.25rem 1rem; color:rgb(226, 230, 105);" ' +
                        'onclick="mostrarMovSalidaX(' + mov.idmov_finanzas + ')">' +
                        '<i class="fa-solid fa-square-plus me-2"></i>Editar</a></li>' +
                        '<li><a class="dropdown-item" href="#" style="padding: 0.25rem 1rem; color: #dc3545;" ' +
                        'onclick="eliminarEntrada(' + mov.idmov_finanzas + ')">' +
                        '<i class="fa-solid fa-trash me-2"></i>Eliminar</a></li>' +
                        '</ul>' +
                        '</div>' +
                        '</td>' +
                        '</tr>';
                    $('#tblmovimientos').append(fila);
                });
                // Inicializa DataTables con las opciones
                $('#tblmovimientos').DataTable({
                    "language": {
                        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                    },
                    "retrieve": true, // Permite recuperar la instancia existente
                    "destroy": true // Permite destruir la instancia anterior si existe
                });
            }
        },
        error: function (xhr, status, error) {
            //console.error("Error en la solicitud:", status, error);
        }
    });
}

function eliminarEntrada(idmov_finanzas) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: URL_PY + 'movimientos/eliminar',
                data: { idmovfinanzas: idmov_finanzas },
                success: function (response) {
                    if (response.error) {
                        Swal.fire({
                            icon: "error",
                            title: "ELIMINACIÓN FALLIDA",
                            text: response.error
                        });
                    } else {
                        Swal.fire({
                            icon: "success",
                            title: "REGISTRO ELIMINADO",
                            text: response.message
                        }).then(() => {
                            var tabActiva = $('.nav-tabs .active').attr('href');

                            // Recargamos la tabla correspondiente
                            if (tabActiva === '#tabentrada') {
                                obtenerMovimientos(); // Recarga tabla de ENTRADAS
                            } else if (tabActiva === '#tabsalida') {
                                obtenerMovimientoSalida(); // Recarga tabla de SALIDAS
                            }
                        });
                    }
                },
            });
        }
    });
}

function limpiar() {
    $('#txtiddest').val('');
    $('#txtdestinatario').val('');
    $('#txtmonto').val('');
    $('#txtobservacion').val('');
    $('#txtnoperacion').val('');
}

function limpiar2() {
    $('#txtiddest2').val('');
    $('#txtdestinatario2').val('');
    $('#txtmonto2').val('');
    $('#txtobservacion2').val('');
    $('#txtnoperacion2').val('');
}

function limpiarSaldo() {
    $('#txtobservacionS').val('');
    $('#txtsaldo').val('');
    $('#txtnoperacionS').val('');
}

function reportePDFmovimientos() {
    // Obtener fechas
    const fechaInicio = $('#dtpfechaini').val();
    const fechaFin = $('#dtpfechafin').val();

    // Validar que fecha inicio no sea mayor a fecha fin
    if (fechaInicio > fechaFin) {
        Swal.fire({
            icon: 'warning',
            title: 'ERROR EN LA BUSQUEDA',
            text: 'La fecha de inicio no puede ser mayor a la fecha de fin',
        });
        return;
    }
    // Crear un formulario temporal
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = URL_PY + 'movimientos/reporte_movimientos', 
        form.target = '_blank';

    // Crear campos de formulario para los datos
    const inputInicio = document.createElement('input');
    inputInicio.type = 'hidden';
    inputInicio.name = 'i';
    inputInicio.value = $('#dtpfechaini').val();
    form.appendChild(inputInicio);

    const inputFin = document.createElement('input');
    inputFin.type = 'hidden';
    inputFin.name = 'f';
    inputFin.value = $('#dtpfechafin').val();
    form.appendChild(inputFin);

    // Agregar el formulario al documento y enviarlo
    document.body.appendChild(form);
    form.submit();

    // Eliminar el formulario después de enviarlo
    document.body.removeChild(form);
}

function registrarMovSaldo() {
    var parametros =
        'Observacion=' + $('#txtobservacionS').val() +
        '&Cuenta=' + $('#cmbdetentempresa').val() +
        '&Fecha=' + $('#datefecha').val() +
        '&Saldo=' + $('#txtsaldo').val() +
        '&Tipo=SALDO' +
        '&Noperacion=' + $('#txtnoperacionS').val();
    $.ajax({
        type: "POST",
        url: URL_PY + 'movimientos/registrar_saldo',
        data: parametros,
        success: function (response) {
            //console.log(response);
            if (response.includes('MOVIMIENTO FINANCIERO REGISTRADO')) {
                Swal.fire({
                    icon: 'success',
                    title: 'REGISTRO DE MOVIMIENTOS',
                    text: response,
                }).then(function () {
                    obtenerMovimientos();
                    $("#mdlingsaldo").modal('hide')
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'ERROR AL REGISTRAR',
                    text: response
                });
            }
        },
    });
}

function reporteExcelMovimientos() {
    // Obtener fechas
    const fechaInicio = $('#dtpfechaini').val();
    const fechaFin = $('#dtpfechafin').val();

    // Validar que fecha inicio no sea mayor a fecha fin
    if (fechaInicio > fechaFin) {
        Swal.fire({
            icon: 'warning',
            title: 'ERROR EN LA BUSQUEDA',
            text: 'La fecha de inicio no puede ser mayor a la fecha de fin',
        });
        return;
    }
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = URL_PY + 'movimientos/reporte_excel_movimientos';
    form.target = '_blank';

    const inputInicio = document.createElement('input');
    inputInicio.type = 'hidden';
    inputInicio.name = 'i';
    inputInicio.value = $('#dtpfechaini').val();
    form.appendChild(inputInicio);

    const inputFin = document.createElement('input');
    inputFin.type = 'hidden';
    inputFin.name = 'f';
    inputFin.value = $('#dtpfechafin').val();
    form.appendChild(inputFin);

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function mostrarMovSalidaX(cod) {
    var parametros = 'cod=' + cod;
    const url = URL_PY + 'movimientos/editar_salida';
    // console.log(parametros);
    $.ajax({
        type: "GET",
        url: url,
        data: parametros,
        success: function (response) {
            //console.log(response);
            $('#txtidmovsalida').val(cod);
            $('#txtidoperacion').val(response[0].noperacion);
            $('#txtmotivo').val(response[0].observacion);
            $('#txtenviadoa').val(response[0].enviado_a);
            $('#txtmontomotivo').val(response[0].monto)
        }
    });
    $('#lbltitulo4').html('Agregar Motivo');
    var myModal = new bootstrap.Modal(document.getElementById('mdlmotivo'));
    myModal.show();
}

function editar() {
    var parametros = 'observacion=' + $('#txtmotivo').val() +
        '&cod=' + $('#txtidmovsalida').val();
    $.ajax({
        type: "POST",
        url: URL_PY + 'movimientos/actualizar',
        data: parametros,
        success: function (response) {
            if (response.error) {
                Swal.fire({
                    icon: "error",
                    title: 'ACTUALIZAR MOVIMIENTO',
                    text: response.error
                });
            }
            else {
                Swal.fire({
                    icon: 'success',
                    title: 'ACTUALIZAR MOVIMIENTO',
                    text: response.message,
                }).then(function () {
                    $('#mdlmotivo').modal('hide');
                    // Verificamos qué pestaña está activa
                    var tabActiva = $('.nav-tabs .active').attr('href');

                    // Recargamos la tabla correspondiente
                    if (tabActiva === '#tabentrada') {
                        obtenerMovimientos(); // Recarga tabla de ENTRADAS
                    } else if (tabActiva === '#tabsalida') {
                        obtenerMovimientoSalida(); // Recarga tabla de SALIDAS
                    }

                });
            }
        }
    });
}
