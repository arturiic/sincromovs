$(document).ready(function () {
    $('#txtpassword').keyup(function (e) {
        if (e.keyCode == 13) {
            loguearSistema();
        }
    });

    // Recuperar usuario y contraseña de cookies solo si el usuario existe en el select
    var savedUser = getCookie('rememberUser');
    var savedPass = getCookie('rememberPass');
    var rememberChecked = getCookie('rememberChecked') === 'true';

    if (rememberChecked && savedUser) {
        // Verifica si el usuario existe en el select
        if ($('#cmbusuario option[value="' + savedUser + '"]').length > 0) {
            $('#cmbusuario').val(savedUser);
            $('#rememberMe').prop('checked', true);
            if (savedPass) $('#txtpassword').val(savedPass);
        } else {
            // Si el usuario guardado no existe, limpia cookies
            eraseCookie('rememberUser');
            eraseCookie('rememberPass');
            eraseCookie('rememberChecked');
            $('#rememberMe').prop('checked', false);
        }
    }

    // Limpia la contraseña si el usuario cambia
    $('#cmbusuario').on('change', function () {
        $('#txtpassword').val('');
    });
});

function loguearSistema() {
    var usu = $('#cmbusuario').val();
    var pass = $('#txtpassword').val();
    var recordar = $('#rememberMe').is(':checked');

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
        // Guardar en localStorage si el checkbox está marcado
        if (recordar) {
    setCookie('rememberUser', usu, 30);
    setCookie('rememberPass', pass, 30);
    setCookie('rememberChecked', 'true', 30);
} else {
    eraseCookie('rememberUser');
    eraseCookie('rememberPass');
    eraseCookie('rememberChecked');
}

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
function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + encodeURIComponent(value) + expires + "; path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return decodeURIComponent(c.substring(nameEQ.length,c.length));
    }
    return null;
}

function eraseCookie(name) {   
    document.cookie = name+'=; Max-Age=-99999999; path=/';  
}