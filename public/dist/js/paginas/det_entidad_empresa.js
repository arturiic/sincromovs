var table = "";

$(document).ready(function () {
    cargarDetEntidadEmpresa();
});

function abrirModal() {
    limpiar();
    $('#lbltitulo').html('NUEVA CUENTA EMPRESA');
    $('#btnregistrar').removeClass('d-none');
    $('#btneditar').addClass('d-none');
    $('#mdldet_entidad_empresa').modal('show');
}

function limpiar() {
    $('#txtdescripcion').val('');
    $('#cmbestado').val('ACTIVO');
    $('#cmbent_bancaria').val('1');
}

function cargarDetEntidadEmpresa() {
    const url = URL_PY + 'detEntidadEmpresas/dtDetEntidadEmpresas';
    table = $('#tbldet_entidad_empresa').DataTable({
        "destroy": true,
        "language": {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        "autoWidth": true,
        "responsive": true,
        "ajax": {
            'method': 'GET',
            'url': url,
            'dataSrc': function (json) {
                //console.log(json);
                return json.data;
            }
        },
        "createdRow": function (row, data, dataIndex) {
            // Aplica fondo negro y texto blanco a cada fila generada
            $(row).css({
                'background-color': '#000000',
                'color': 'white'
            });
        },
        "columns": [
            { "data": "iddet_entidad_empresa", "visible": false },
            { "data": "descripcion" },
            {
                "data": "estado",
                "className": "text-center",
                "render": function (data) {
                    // Convertir valores y asignar color
                    if (data === 'ACTIVO') {
                        return '<span class="text-success font-weight-bold">ACTIVO</span>';
                    } else if (data === 'INACTIVO') {
                        return '<span class="text-danger font-weight-bold">INACTIVO</span>';
                    }
                    return data; // Por si hay otro valor no contemplado
                }
            },
            { "data": "entidad_bancaria" },
            { "data": "empresa", },
            {
                "data": null,
                "orderable": false, // Deshabilitar el ordenamiento en esta columna
                "render": function (data, type, row) {
                    return `<button class="btn btn-2 btn-warning btn-pill" onclick="mostrarDatosx('${data.iddet_entidad_empresa}')">
                                <i class="fas fa-pencil-alt"></i>&nbsp;
                            </button>`;
                }
            }
        ]
    });
}
function registrar() {
    var parametros = 'descripcion=' + $('#txtdescripcion').val() +
        '&estado=' + $('#cmbestado').val() +
        '&identidad_bancaria=' + $('#cmbent_bancaria').val() +
        '&idempresa=' + $('#cmbempresa').val();
    $.ajax({
        type: "POST",
        url: URL_PY + 'detEntidadEmpresas/registrar',
        data: parametros,
        success: function (response) {
            if (response.error) {
                Swal.fire({
                    icon: "error",
                    title: 'REGISTRO DE ENTIDAD EMPRESA',
                    text: response.error
                });
            }
            else {
                Swal.fire({
                    icon: 'success',
                    title: 'REGISTRO DE ENTIDAD EMPRESA',
                    text: response.message,
                }).then(function () {
                    var paginaActual = table.page.info().page;
                    table.ajax.reload();
                    setTimeout(function () {
                        table.page(paginaActual).draw('page');
                    }, 800);
                    limpiar();
                    $('#mdldet_entidad_empresa').modal('hide');
                });
            }
        }
    });
}

//MOSTRAR DATOS AL MOMENTO DE EDITAR LLAMANDOLOS POR SU ID
function mostrarDatosx(cod) {
    var parametros = 'cod=' + cod;
    const url = URL_PY + 'detEntidadEmpresas/det_entidad_empresasxcod';
    //console.log(parametros);
    $.ajax({
        type: "GET",
        url: url,
        data: parametros,
        success: function (response) {
            //console.log(response);
            $('#txtid').val(cod);
            $('#btnregistrar').addClass('d-none');
            $('#btneditar').removeClass('d-none');
            $('#txtdescripcion').val(response[0].descripcion);
            $('#cmbestado').val(response[0].estado);
            $('#cmbent_bancaria').val(response[0].identidad_bancaria);  
        }
    });
    $('#lbltitulo').html('EDITAR CUENTA EMPRESA');
    $('#mdldet_entidad_empresa').modal('show');
}

function editar() {
    var parametros = 'descripcion=' + $('#txtdescripcion').val() +
        '&estado=' + $('#cmbestado').val() +
        '&identidad_bancaria=' + $('#cmbent_bancaria').val() +
        '&cod=' + $('#txtid').val();
    $.ajax({
        type: "POST",
        url: URL_PY + 'detEntidadEmpresas/editar',
        data: parametros,
        success: function (response) {
            if (response.error) {
                Swal.fire({
                    icon: "error",
                    title: 'EDICIÓN DE ENTIDAD EMPRESA',
                    text: response.error
                });
            }
            else {
                Swal.fire({
                    icon: 'success',
                    title: 'EDICIÓN DE ENTIDAD EMPRESA',
                    text: response.message,
                }).then(function () {
                    var paginaActual = table.page.info().page;
                    table.ajax.reload();
                    setTimeout(function () {
                        table.page(paginaActual).draw('page');
                    }, 800);
                    limpiar();
                    $('#mdldet_entidad_empresa').modal('hide');
                });
            }
        }
    });
}