$(document).ready(function () {
    $('#txtpassword').keyup(function (e) {
        if (e.keyCode == 13) {
            loguear_sistema();
        }
    });
});

function loguear_sistema() {
    var usu = $('#cmbusuario').val();
    var pass = $('#txtpassword').val();

    if (pass === '') {
        if ($('#cmbusuario').val() == '') {
            toastr.error('Necesita ingresar usuario !!', 'LOGIN');
            $('#cmbusuario').focus();
        }
        else if ($('#txtpassword').val() == '') {
            toastr.error('Necesita ingresar clave !!', 'LOGIN');
            $('#txtpassword').focus();
        }
    } else {
        var parametros = 'password=' + pass + '&idusuario=' + usu;

        $.ajax({
            type: "POST",
            url: URL_PY + 'login/ingresar',
            data: parametros,
            success: function (response) {
                if (response.mensaje) {
                    toastr.error(response.mensaje, 'INICIO DE SESION');
                } else {
                    // Redirección al dashboard si no hay mensaje de error
                    window.location.href = 'dashboard';
                }
            },
        });
    }
}