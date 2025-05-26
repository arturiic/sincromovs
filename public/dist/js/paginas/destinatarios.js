var table = "";

$(document).ready(function () {
    cargaDestinatarios();
});

function abrirModal() {
    limpiar();
    $('#lbltitulo').html('Nuevo Destinatario');
    $('#btnregistrar').removeClass('d-none');
    $('#btneditar').addClass('d-none');
    var myModal = new bootstrap.Modal(document.getElementById('mdldestinatario'));
    myModal.show();
}

function limpiar() {
    $('#txtnombre').val('');
    $('#cmbestado').val('ACTIVO');
}

function cargaDestinatarios() 
{
    const url = URL_PY + 'destinatarios/dtdestinatarios';    
    table = $('#tbldestinatarios').DataTable({
        "destroy": true,
        "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"},
        "autoWidth": true,
        "responsive": true,
        "ajax": {
            'method': 'GET',
            'url': url,
            'dataSrc': function (json) {
                //console.log(json); // Verifica que los datos sean correctos
                return json.data; 
            }
        },
        "columns": [
            {"data": "iddestinatario", "visible": false},
            {"data": "nombre"},           
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

            {
                "data": null,
                "orderable": false, // Deshabilitar el ordenamiento en esta columna
                "className": "text-center",
                "render": function(data, type, row) {
                    return `<button class="btn btn-2 btn-warning btn-pill" onclick="mostrarDatosx('${data.iddestinatario}')">
                                <i class="fas fa-pencil-alt"></i>&nbsp;
                            </button>`;
                }
            }
        ]
    });  
}

function registrar() {
    var parametros = 'nombre=' + $('#txtnombre').val() +
            '&fecha_creacion=' + $('#datefecha').val() +
            '&estado=' + $('#cmbestado').val();
    $.ajax({
        type: "POST",
        url: URL_PY + 'destinatarios/registrar',
        data: parametros,        
        success: function (response) {
            if (response.error) {
                Swal.fire({
                      icon: "error",
                      title: 'REGISTRO DE DESTINATARIO',
                      text: response.error
                });     
            }
            else
            {
                Swal.fire({
                    icon: 'success',
                    title: 'REGISTRO DE DESTINATARIO',
                    text: response.message,            
                    }).then(function() {
                      var paginaActual = table.page.info().page;
                      table.ajax.reload();
                      setTimeout(function () {
                          table.page(paginaActual).draw('page');
                      }, 800);                          
                     limpiar();
                     $('#mdldestinatario').modal('hide');
            });     
            }
        }
    });
}

function mostrarDatosx(cod) {
    var parametros = 'cod='+cod;
    const url = URL_PY + 'destinatarios/destinatarioxcod';  
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
            $('#txtnombre').val(response[0].nombre);
            $('#cmbestado').val(response[0].estado);
        }
    });
    $('#lbltitulo').html('Editar Destinatario');
    var myModal = new bootstrap.Modal(document.getElementById('mdldestinatario'));
    myModal.show();
}

function editar() {
    var parametros = 'nombre=' + $('#txtnombre').val() +
            '&estado=' + $('#cmbestado').val() +
            '&cod='+ $('#txtid').val();
    $.ajax({
        type: "POST",
        url: URL_PY + 'destinatarios/editar',
        data: parametros,       
        success: function (response) {
            if (response.error) {
                Swal.fire({
                      icon: "error",
                      title: 'EDICIÓN DE DESTINATARIO',
                      text: response.error
                });
            }
            else
            {
                Swal.fire({
                    icon: 'success',
                    title: 'EDICIÓN DE DESTINATARIO',
                    text: response.message,            
                    }).then(function() {
                      var paginaActual = table.page.info().page;
                      table.ajax.reload();
                      setTimeout(function () {
                          table.page(paginaActual).draw('page');
                      }, 800);                          
                     limpiar();
                     $('#mdldestinatario').modal('hide');
            });     
            }
        }
    });
}