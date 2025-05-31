var Español = {
  "sProcessing": "Procesando...",
  "sLengthMenu": "Mostrar _MENU_ registros",
  "sZeroRecords": "No se encontraron resultados",
  "sEmptyTable": "Ningún dato disponible en esta tabla =(",
  "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
  "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
  "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
  "sInfoPostFix": "",
  "sSearch": "Buscar:",
  "sUrl": "",
  "sInfoThousands": ",",
  "sLoadingRecords": "Cargando...",
  "oPaginate": {

    "sNext": ">",
    "sPrevious": "<"
  },
  "oAria": {
    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
  },
  "buttons": {
    "copy": "Copiar",
    "colvis": "Visibilidad"
  }
}
document.addEventListener('DOMContentLoaded', function () {
  // Agregar un evento de escucha a todo el documento
  document.addEventListener('input', function (event) {
    // Verificar si el elemento que disparó el evento es un input o textarea
    if ((event.target.tagName === 'INPUT' || event.target.tagName === 'TEXTAREA') &&

      event.target.id
      !== 'txtclave' &&
      event.target.id
      !== 'txtpassword') { // Ignorar el campo con id 'txtclave'
      // Convertir el valor del input/textarea a mayúsculas
      event.target.value = event.target.value.toUpperCase();
    }
  });
});

document.addEventListener('contextmenu', function (e) {
  e.preventDefault();
});
(function ($) {
  $.ajaxblock = function () {
    $("body").prepend(`
          <div id='ajax-overlay'>
             <div id='ajax-overlay-body' class='text-center'>
                <img src="${URL_PY}public/dist/assets/img/carga2.gif" alt="Cargando..." />
                <p class='mt-1' style='font-size: 30px;'>Cargando Datos...</p>
             </div>
          </div>
       `);

    // Estilos para el overlay
    $("#ajax-overlay").css({
      position: 'fixed',
      top: '0',
      left: '0',
      width: '100%',
      height: '100%',
      background: 'rgba(39, 38, 46, 0.67)',
      color: '#FFFFFF',
      'text-align': 'center',
      'z-index': '9999'
    });

    // Estilos para el contenido
    $("#ajax-overlay-body").css({
      position: 'absolute',
      top: '40%',
      left: '50%',
      transform: 'translate(-50%, -50%)',
      width: 'auto',
      height: 'auto',
      '-webkit-border-radius': '10px',
      '-moz-border-radius': '10px',
      'border-radius': '10px'
    });

    // Mostrar el overlay
    $("#ajax-overlay").fadeIn(50);
  };

  $.ajaxunblock = function () {
    $("#ajax-overlay").fadeOut(100, function () {
      $(this).remove(); // Asegúrate de que se elimine correctamente
    });
  };
})(jQuery);

function abrirModalEmpresa() {
  var myModal = new bootstrap.Modal(document.getElementById('mdlcambio'));
  myModal.show();
}

if (codalmacen == 'NL') {
  abrirModalEmpresa();
};

$(document).ready(function () {
  cargarEmpresas();
});
$("#cmbempresas").change(function () {
  var empresaSeleccionada = $(this).val();
  cargarSucursalesXempresa(empresaSeleccionada);
});
$("#cmbsucursal").change(function () {
  var sucursalSeleccionada = $(this).val();
  cargarAlmacenXsucursal(sucursalSeleccionada);
});

function cargarEmpresas() {
  var url = URL_PY + 'cambio/empresa';
  $.ajax({
    type: "POST",
    url: url,
    success: function (response) {
      //console.log(response)
      if (response.success) {
        const empresaSelect = $('#cmbempresas');
        empresaSelect.empty(); // Limpia el select existente
        // Llena el select con las sucursales
        $.each(response.empresas, function (index, empresa) {
          empresaSelect.append(
            $('<option>', { value: empresa.idempresa, text: empresa.descripcion })
          );
        });
        // Llenar almacén basado en la primera sucursal
        cargarSucursalesXempresa(empresaSelect.val());
      } else {
        alert('No hay empresas');
      }
    },
    error: function (jqXHR, textStatus) {
      console.log('Error: ' + textStatus);
    }
  });
}
function cargarSucursalesXempresa(empresa) {
  var url = URL_PY + 'cambio/sucursal';
  $.ajax({
    type: "POST",
    url: url,
    data: { empresa },
    success: function (response) {
      //console.log(response)
      if (response.success) {
        const sucursalSelect = $('#cmbsucursal');
        sucursalSelect.empty(); // Limpia el select existente

        // Llena el select con las sucursales
        $.each(response.sucursales, function (index, sucursal) {
          sucursalSelect.append(
            $('<option>', { value: sucursal.idsucursal, text: sucursal.descripcion })
          );
        });

        // Llenar almacén basado en la primera sucursal
        cargarAlmacenXsucursal(sucursalSelect.val());
      } else {
        alert('No hay sucursales');
      }
    },
    error: function (jqXHR, textStatus) {
      //console.log('Error: ' + textStatus);
    }
  });
}
function cargarAlmacenXsucursal(sucursal) {
  var url = URL_PY + 'cambio/almacen';
  $.ajax({
    type: "POST",
    url: url,
    data: { sucursal },
    success: function (response) {
      //console.log(response)
      if (response.success) {
        const almacenSelect = $('#cmbalmacen');
        almacenSelect.empty(); // Limpia el select existente

        // Llena el select con los almacenes
        $.each(response.almacenes, function (index, almacen) {
          //console.log(almacen)
          almacenSelect.append(
            $('<option>', { value: almacen.idalmacen, text: almacen.descripcion })
          );
        });
      } else {
        alert('No hay almacenes');
      }
    },
    error: function (jqXHR, textStatus) {
      //console.log('Error: ' + textStatus);
    }
  });
}

function cambioEmpresa() {
  var empresa = $('#cmbempresas').val();
  var sucursal = $('#cmbsucursal').val();
  var almacen = $('#cmbalmacen').val();

  var parametros = 'idempresa=' + empresa +
    '&idsucursal=' + sucursal + '&idalmacen=' + almacen;

  $.ajax({
    type: "post",
    url: URL_PY + 'cambio/ingresar',
    data: parametros,
    success: function (response) {
      //console.log(response);
      if (response.mensaje) {
        Swal.fire({
          icon: "error",
          title: "INICIO DE SESION",
          text: response.mensaje
        });
      } else {
        location.reload();
      }
    }
  });
}

function cambioUsuario(nombreUsuario) {
  Swal.fire({
    title: '¿Cerrar sesión?',
    html: `Estás a punto de salir de la sesión de <b>${nombreUsuario || 'Usuario'}</b>`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'SÍ, SALIR',
    cancelButtonText: 'CANCELAR',
    allowOutsideClick: false
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = URL_PY + 'login/salir';
    }
  });
}