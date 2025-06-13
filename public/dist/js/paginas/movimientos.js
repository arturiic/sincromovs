let terminoActualEntrada = '';
let paginaActualEntrada = 1;
let terminoActualSalida = '';
let paginaActualSalida = 1;
let filaSeleccionada = null;
let tableMovimientos = null;
let currentTab = 'entrada';

var table = "";

$(document).ready(function () {

    // 1. Evento para limpiar campos al cambiar de pestaña
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr('href');
        if (target === '#tabentrada') {
            currentTab = 'entrada';
        } else if (target === '#tabsalida') {
            currentTab = 'salida';
        }
        actualizarTabla();
    });
    $('#txtnoperacion, #txtnoperacion2, #txtnoperacionS').on('keypress', function (e) {
        const char = String.fromCharCode(e.which);
        // Bloquea solo letras, permite cualquier otro símbolo
        if (/[a-zA-Z]/.test(char)) {
            e.preventDefault();
        }
    });
    $('#txtmonto, #txtmonto2, #txtsaldo').on('keypress', function (e) {
        const char = String.fromCharCode(e.which);
        // Permite solo números, puntos y comas
        if (!/[0-9.,]/.test(char)) {
            e.preventDefault();
        }
    });
    $('#txtdestinatario').on('keyup', function () {
        let termino = $(this).val();
        terminoActualEntrada = termino;
        paginaActualEntrada = 1;
        if (termino.length >= 3) {
            buscarDestinatariosInline(termino, 1, 'entrada', false);
        } else {
            $('#resultados_destinatario').html('');
            $('#cargarMas_destinatario').hide();
            if (termino.length === 0) {
                $('#txtiddest').val('');
            }
        }
    });

    $('#cargarMas_destinatario').on('click', function () {
        paginaActualEntrada++;
        buscarDestinatariosInline(terminoActualEntrada, paginaActualEntrada, 'entrada', true);
    });

    $('#txtdestinatario2').on('keyup', function () {
        let termino = $(this).val();
        terminoActualSalida = termino;
        paginaActualSalida = 1;
        if (termino.length >= 3) {
            buscarDestinatariosInline(termino, 1, 'salida', false);
        } else {
            $('#resultados_destinatario2').html('');
            $('#cargarMas_destinatario2').hide();
            if (termino.length === 0) {
                $('#txtiddest2').val('');
            }
        }
    });

    $('#cargarMas_destinatario2').on('click', function () {
        paginaActualSalida++;
        buscarDestinatariosInline(terminoActualSalida, paginaActualSalida, 'salida', true);
    });

    // Evento para seleccionar destinatario ENTRADA
    $(document).on('click', '.escogerDestinatarioInline', function () {
        let iddestinatario = $(this).data('id');
        let nombreDestinatario = $(this).data('nombre');
        $('#txtiddest').val(iddestinatario);
        $('#txtdestinatario').val(nombreDestinatario);
        $('#resultados_destinatario').html('');
        $('#cargarMas_destinatario').hide();
    });

    // Evento para seleccionar destinatario SALIDA
    $(document).on('click', '.escogerDestinatarioInline2', function () {
        let iddestinatario = $(this).data('id');
        let nombreDestinatario = $(this).data('nombre');
        $('#txtiddest2').val(iddestinatario);
        $('#txtdestinatario2').val(nombreDestinatario);
        $('#resultados_destinatario2').html('');
        $('#cargarMas_destinatario2').hide();
    });

    // Eventos para filtros
    $("#datefecha, #cmbdetentempresa").change(function () {
        if (currentTab === 'entrada') actualizarTabla();
    });

    $("#datefecha2, #cmbdetentempresa2").change(function () {
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
            "data": function () {
                return getCurrentParams();
            }
        },
        "columns": [
            {
                "data": "destinatario",
                "width": "30%",
            },
            {
                "data": "observacion",
                "width": "30%",
            },
            {
                "data": "fecha",
                "width": "10%",
            },
            {
                "data": "monto",
                "width": "10%",
                "render": function (data, type, row) {
                    return currentTab === 'entrada' ?
                        `<span class="text-success">+${data}</span>` :
                        `<span class="text-danger">-${data}</span>`;
                }
            },
            {
                "data": "noperacion",
                "width": "10%",
            },
            {
                "data": "idmov_finanzas",
                "orderable": false,
                "className": "text-center",
                "width": "15%",
                "render": function (data, type, row) {
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

function abrirModalPDF() {
    $('#lbltitulos').html('GENERAR REPORTE');
    $('#mdlpdf').modal('show');
}

function abrirModalSaldo() {
    $('#lbltitulo3').html('INGRESAR NUEVO SALDO');
    $('#mdlingsaldo').modal('show');
    limpiarSaldo();
}

function buscarDestinatariosInline(termino, pagina, tipo, append = false) {
    $.ajax({
        url: URL_PY + "destinatarios/busc_destinatarios",
        method: "GET",
        data: { q: termino, page: pagina, limite: 5 },
        dataType: "json",
        success: function (data) {
            let ulId = tipo === 'entrada' ? '#resultados_destinatario' : '#resultados_destinatario2';
            let btnId = tipo === 'entrada' ? '#cargarMas_destinatario' : '#cargarMas_destinatario2';
            if (!append) $(ulId).html('');
            if (data.destinatarios && data.destinatarios.length > 0) {
                $.each(data.destinatarios, function (index, destinatarios) {
                    $(ulId).append(`
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><strong>${destinatarios.nombre}</strong></span>
                            <button class="btn btn-6 btn-primary active btn-pill w-10 ${tipo === 'entrada' ? 'escogerDestinatarioInline' : 'escogerDestinatarioInline2'}"
                                data-id="${destinatarios.iddestinatario}"
                                data-nombre="${destinatarios.nombre}">
                                <i class="fas fa-check"></i>
                            </button>
                        </li>
                    `);
                });
                let totalPaginas = Math.ceil(data.total / data.limite);
                if (pagina < totalPaginas) {
                    $(btnId).show();
                } else {
                    $(btnId).hide();
                }
            } else if (!append) {
                $(ulId).html('<li class="list-group-item text-center text-muted">EL DESTINATARIO INGRESADO NO EXISTE, REGÍSTRALO</li>');
                $(btnId).hide();
            }
        }
    });
}

function registrarMovEntrada() {
    var destinatario = $('#txtiddest').val();
    var observacion = $('#txtobservacion').val();
    var monto = $('#txtmonto').val();
    var noperacion = $('#txtnoperacion').val();
    if (!destinatario) {
        Swal.fire({
            icon: 'warning',
            title: 'ERROR AL REGISTRAR',
            text: 'Debe seleccionar un destinatario.'
        });
        return;
    }
    if (!observacion) {
        Swal.fire({
            icon: 'warning',
            title: 'ERROR AL REGISTRAR',
            text: 'Debe ingresar una observación.'
        });
        return;
    }
    if (!monto) {
        Swal.fire({
            icon: 'warning',
            title: 'ERROR AL REGISTRAR',
            text: 'Debe ingresar un monto.'
        });
        return;
    }
    if (!noperacion) {
        Swal.fire({
            icon: 'warning',
            title: 'ERROR AL REGISTRAR',
            text: 'Debe ingresar el número de operación.'
        });
        return;
    }
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
    var destinatario = $('#txtiddest2').val();
    var observacion = $('#txtobservacion2').val();
    var monto = $('#txtmonto2').val();
    var noperacion = $('#txtnoperacion2').val();
    if (!destinatario) {
        Swal.fire({
            icon: 'warning',
            title: 'ERROR AL REGISTRAR',
            text: 'Debe seleccionar un destinatario.'
        });
        return;
    }
    if (!observacion) {
        Swal.fire({
            icon: 'warning',
            title: 'ERROR AL REGISTRAR',
            text: 'Debe ingresar una observación.'
        });
        return;
    }
    if (!monto) {
        Swal.fire({
            icon: 'warning',
            title: 'ERROR AL REGISTRAR',
            text: 'Debe ingresar un monto.'
        });
        return;
    }
    if (!noperacion) {
        Swal.fire({
            icon: 'warning',
            title: 'ERROR AL REGISTRAR',
            text: 'Debe ingresar el número de operación.'
        });
        return;
    }
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
function registrarDestinatario() {
    var inputId = currentTab === 'entrada' ? '#txtdestinatario' : '#txtdestinatario2';
    var inputIdHidden = currentTab === 'entrada' ? '#txtiddest' : '#txtiddest2';
    var ulResultados = currentTab === 'entrada' ? '#resultados_destinatario' : '#resultados_destinatario2';
    var btnCargarMas = currentTab === 'entrada' ? '#cargarMas_destinatario' : '#cargarMas_destinatario2';
    var nombre = $(inputId).val();
    var parametros = 'nombre=' + nombre + '&estado=ACTIVO';
    $.ajax({
        type: "POST",
        url: URL_PY + 'destinatarios/registrar',
        data: parametros,
        success: function (response) {
            if (response.error) {
                Swal.fire({
                    icon: "error",
                    title: 'ERROR AL REGISTRAR',
                    text: response.error
                });
            } else {
                $.ajax({
                    type: "GET",
                    url: URL_PY + 'destinatarios/busc_destinatarios',
                    data: { q: nombre, page: 1 },
                    success: function (resp) {
                        if (resp.destinatarios && resp.destinatarios.length > 0) {
                            var encontrado = resp.destinatarios.find(function (d) { return d.nombre === nombre; });
                            if (encontrado) {
                                $(inputIdHidden).val(encontrado.iddestinatario);
                                $(inputId).val(encontrado.nombre);
                                $(ulResultados).html('');
                                $(btnCargarMas).hide();
                            }
                        }
                    },
                    complete: function () {
                        Swal.fire({
                            icon: 'success',
                            title: 'DESTINATARIO REGISTRADO',
                            text: response.message,
                        });
                    }
                });
            }
        }
    });
}