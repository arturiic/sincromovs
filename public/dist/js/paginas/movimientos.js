let terminoActual = "";
let filaSeleccionada = null;
let pagina = 1;
let tableMovimientos = null;
let currentTab = 'entrada';

var table = "";

$(document).ready(function () {
    $("#buscador").keyup(function () {
        terminoActual = $(this).val();
        pagina = 1; // Reiniciar la paginación
        buscarDestinatarios(true);
    });
    // 1. Evento para limpiar campos al cambiar de pestaña
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
        const target = $(e.target).attr('href');
        if (target === '#tabentrada') {
            currentTab = 'entrada';
        } else if (target === '#tabsalida') {
            currentTab = 'salida';
        }
        actualizarTabla();
    });

    $("#cargarMas").click(function () {
        pagina++; // Aumentar la página
        buscarDestinatarios(false);
    });
    // Eventos para filtros
    $("#datefecha, #cmbdetentempresa").change(function() {
        if (currentTab === 'entrada') actualizarTabla();
    });

    $("#datefecha2, #cmbdetentempresa2").change(function() {
        if (currentTab === 'salida') actualizarTabla();
    });
    inicializarTabla();
});

function inicializarTabla() {
    tableMovimientos = $('#tblmovimientos').DataTable({
        "language": {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        "responsive": true,
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": URL_PY + 'movimientos/movEntradaXfecha',
            "type": "GET",
            "data": function() {
                return getCurrentParams();
            }
        },
        "columns": [
            { "data": "destinatario",
                "width": "30%",
             },
            { "data": "observacion",
                "width": "30%",
             },
            { "data": "fecha",
                "width": "10%",
             },
            { 
                "data": "monto",
                "width": "10%",
                "render": function(data, type, row) {
                    return currentTab === 'entrada' ? 
                        `<span class="text-success">+${data}</span>` : 
                        `<span class="text-danger">-${data}</span>`;
                }
            },
            { "data": "noperacion",
                "width": "10%",
             },
            {
                "data": "idmov_finanzas",
                "orderable": false,
                "className": "text-center",
                "width": "15%",
                "render": function(data, type, row) {
                    return `
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-warning mx-1" 
                            onclick="mostrarMovimientosX(${data}, '${currentTab}')"
                            title="Editar">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button class="btn btn-sm btn-danger mx-1" 
                            onclick="eliminarMovimiento(${data}, '${row.noperacion}', '${currentTab}')"
                            title="Eliminar">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>`;
                }
            }
        ]
    });
}

function getCurrentParams() {
    if (currentTab === 'entrada') {
        return {
            "fecha": $('#datefecha').val(),
            "entidad": $('#cmbdetentempresa').val()
        };
    } else {
        return {
            "fecha": $('#datefecha2').val(),
            "entidad": $('#cmbdetentempresa2').val()
        };
    }
}

function actualizarTabla() {
    if (tableMovimientos) {
        tableMovimientos.ajax.url(
            currentTab === 'entrada' ? 
            URL_PY + 'movimientos/movEntradaXfecha' : 
            URL_PY + 'movimientos/movSalidaXfecha'
        ).load();
    }
}

function eliminarMovimiento(idmov_finanzas, noperacion) {
    Swal.fire({
        title: `¿Estás seguro de eliminar el movimiento N° ${noperacion}?`,
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "SÍ, ELIMINAR",
        cancelButtonText: "CANCELAR"
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
                            actualizarTabla();
                        });
                    }
                },
            });
        }
    });
}

function elegirDestinatario() {
    filaSeleccionada = $(this).closest("tr");
    $('#buscador').val('');
    $('#resultados').html('');
    let totalPaginas = 0;
    $("#cargarMas").toggle(pagina < totalPaginas);
    $('#mdleledestinatario').modal('show');
}

function abrirModalPDF() {
    $('#lbltitulos').html('GENERAR REPORTE');
    $('#mdlpdf').modal('show');
}

function abrirModalSaldo() {
    $('#lbltitulo3').html('INGRESAR NUEVO SALDO');
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
                    limpiar();
                    actualizarTabla();
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
                    limpiar2();
                    actualizarTabla();
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
    const fechaInicio = $('#dtpfechaini').val();
    const fechaFin = $('#dtpfechafin').val();

    if (fechaInicio > fechaFin) {
        Swal.fire({
            icon: 'warning',
            title: 'ERROR EN LA BÚSQUEDA',
            text: 'La fecha de inicio no puede ser mayor a la fecha de fin',
        });
        return;
    }

    const url = `${URL_PY}movimientos/reporte_movimientos.pdf?i=${fechaInicio}&f=${fechaFin}`;
    window.open(url, '_blank');
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
                    actualizarTabla();
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
    const fechaInicio = $('#dtpfechaini').val();
    const fechaFin = $('#dtpfechafin').val();

    if (fechaInicio > fechaFin) {
        Swal.fire({
            icon: 'warning',
            title: 'ERROR EN LA BUSQUEDA',
            text: 'La fecha de inicio no puede ser mayor a la fecha de fin',
        });
        return;
    }

    const url = `${URL_PY}movimientos/reporte_excel_movimientos?i=${fechaInicio}&f=${fechaFin}`;

    window.location.href = url;
}

function mostrarMovimientosX(cod) {
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
    $('#lbltitulo4').html('AGREGAR MOTIVO');
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
                    actualizarTabla();
                });
            }
        }
    });
}
