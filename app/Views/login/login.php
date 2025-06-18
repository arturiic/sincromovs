<!doctype html>
<html lang="es">
<!--begin::Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>SincroMovimientos</title>
    <!-- Favicon para icono pestaña -->
    <link rel="icon" href="<?= base_url('public/dist/assets/img/favicon-32x32.png') ?>" sizes="32x32" />
    <link rel="icon" href="<?= base_url('public/dist/assets/img/favicon-192x192.png') ?>" sizes="192x192" />
    <link rel="apple-touch-icon" href="<?= base_url('public/dist/assets/img/apple-touch-icon.png') ?>" />
    <meta name="msapplication-TileImage" content="<?= base_url('public/dist/assets/img/ms-tile-144x144.png') ?>" />
    <!--begin::CSS Required Plugin(AdminLTE)-->
    <link href="<?= base_url('public/dist/css/adminlte.css') ?>" rel="stylesheet">
    <link href="<?= base_url('public/dist/libs/fontawesome/css/fontawesome.css') ?>" rel="stylesheet">
    <link href="<?= base_url('public/dist/libs/fontawesome/css/solid.css') ?>" rel="stylesheet">
    <link href="<?= base_url('public/dist/libs/sweetalert2/dist/sweetalert2.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('public/dist/libs/toastr/build/toastr.min.css') ?>" rel="stylesheet" />
    <!--end:: CSS Required Plugin(AdminLTE)-->
    <!--begin::Primary Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="title" content="SINCROMOVS" />
    <meta name="author" content="Grupo ASIU" />
    <meta
        name="description"
        content="Sincronizacion y control de movimientos" />
    <meta
        name="keywords"
        content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard" />
    <!--end::Primary Meta Tags-->
    <!--begin::Fonts-->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
        crossorigin="anonymous" />
    <!--end::Fonts-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css"
        integrity="sha256-tZHrRjVqNSRyWg2wbppGnT833E/Ys0DHWGwT04GiqQg="
        crossorigin="anonymous" />
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI="
        crossorigin="anonymous" />
    <!--end::Third Party Plugin(Bootstrap Icons)-->
    <!-- Agrega Material Icons y estilos para animación Material -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <style>
        .fade-in-material {
            opacity: 0;
            transform: translateY(40px) scale(0.98);
            animation: fadeInMaterial 0.7s cubic-bezier(.4, 0, .2, 1) forwards;
        }

        @keyframes fadeInMaterial {
            to {
                opacity: 1;
                transform: none;
            }
        }

        .material-login-card {
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(60, 60, 60, 0.12);
            padding: 2.5rem 2rem 2rem 2rem;
            background: #fff;
            max-width: 370px;
            margin: 48px auto;
        }

        .material-login-title {
            font-size: 2rem;
            font-weight: 700;
            color: rgb(0, 0, 0);
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .material-login-logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .material-login-logo img {
            max-width: 100%;
            height: auto;
            /* Asegura que la imagen no sea más ancha que el contenedor */
            width: 50px;
            /* Ajusta este valor según sea necesario */
        }

        .btn-login-custom {
            width: 90%;
            max-width: 300px;
            /* Cambia este valor para el ancho máximo */
            height: 40px;
            /* Cambia este valor para el alto */
            margin: 0 auto;
            display: block;
            font-size: 1.15rem;
            /* Opcional: tamaño de letra */
        }

        .material-form-group {
            position: relative;
            margin-bottom: 1.5rem;
            /* Aumenta el ancho de los campos */
            max-width: 380px;
            /* antes 340px */
            margin-left: auto;
            margin-right: auto;
        }

        .material-form-group label {
            position: absolute;
            top: 12px;
            left: 44px;
            color: #888;
            font-size: 1rem;
            pointer-events: none;
            transition: 0.2s;
        }

        .material-form-group input:focus+label,
        .material-form-group input:not(:placeholder-shown)+label,
        .material-form-group select:focus+label,
        .material-form-group select:not([value=""])+label {
            top: -10px;
            left: 40px;
            font-size: 0.85rem;
            color: #1976d2;
            background: #fff;
            padding: 0 4px;
        }

        .material-form-group .material-icons {
            position: absolute;
            top: 12px;
            left: 12px;
            color: rgb(0, 0, 0);
        }

        .material-form-group input,
        .material-form-group select {
            width: 99%;
            /* antes 96% */
            padding: 12px 12px 12px 40px;
            border: 1px solid #ccc;
            border-radius: 8px;
            outline: none;
            font-size: 1rem;
            background: #f9f9f9;
            transition: border-color 0.2s;
            min-width: 260px;
            /* antes 240px */
            box-sizing: border-box;
        }

        .material-form-group input:focus,
        .material-form-group select:focus {
            border-color: #1976d2;
            background: #fff;
        }

        .material-login-actions {
            display: flex;
            gap: 12px;
            margin-top: 1.5rem;
        }

        .material-btn {
            border: none;
            border-radius: 8px;
            padding: 0.7rem 0;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
            text-align: center;
            /* Asegura centrado horizontal */
            display: flex;
            align-items: center;
            /* Centrado vertical */
            justify-content: center;
            /* Centrado horizontal */
        }

        .material-btn-primary {
            background: #1976d2;
            color: #fff;
        }

        .material-btn-primary:hover {
            background: #125ea7;
        }

        .material-btn-danger {
            background: #e53935;
            color: #fff;
            /* Quita subrayado en enlaces */
            text-decoration: none !important;
        }

        .material-btn-danger:hover {
            background: #b71c1c;
            text-decoration: none !important;
        }

        @media (max-width: 480px) {
            .material-login-card {
                padding: 1.5rem 0.5rem 1.5rem 0.5rem;
                max-width: 88vw;
                width: 108vw;
            }

            .material-form-group input,
            .material-form-group select {
                min-width: 0;
                width: 100%;
            }
        }
    </style>
</head>
<!--end::Head-->
<!--begin::Body-->

<body class="login-page bg-body-secondary">
    <div class="fade-in-material">
        <div class="material-login-card">
            <div class="material-login-logo">
                <a href="https://grupoasiu.com/" title="Ir a grupoasiu.com">
                    <img src="<?= base_url('public/dist/assets/img/logoaisu.webp') ?>" alt="Logo ASIU"
                        style="width: 260px; height: 60px; object-fit: contain;" />
                </a>
            </div>
            <div id="loginForm" autocomplete="off">
                <div class="material-form-group">
                    <span class="material-icons">person</span>
                    <select id="cmbusuario" name="cmbusuario" required>
                        <?php foreach ($usuarios as $usuariosreg): ?>
                            <option value="<?= esc($usuariosreg['idusuarios']); ?>">
                                <?= esc($usuariosreg['usuario']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="cmbusuario">Usuario</label>
                </div>
                <div class="material-form-group" style="position: relative;">
                    <span class="material-icons">lock</span>
                    <input type="password" id="txtpassword" name="txtpassword" placeholder=" " required autocomplete="current-password" style="padding-right:44px;">
                    <label for="txtpassword">Contraseña</label>
                    <!-- OJITO PARA MOSTRAR/OCULTAR CONTRASEÑA -->
                    <span style="position:absolute;top:10px;right:10px;cursor:pointer;z-index:2;">
                        <a href="#" class="toggle-password" title="Mostrar Contraseña" tabindex="-1">
                            <!-- Icono de ojo abierto -->
                            <svg id="eye-open" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;">
                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                            </svg>
                            <!-- Icono de ojo tachado (oculto por defecto) -->
                            <svg id="eye-closed" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;vertical-align:middle;">
                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                <line x1="3" y1="3" x2="21" y2="21" stroke="currentColor" stroke-width="2" />
                            </svg>
                        </a>
                    </span>
                </div>
                <!-- Recordar contraseña -->
                <div style="display: flex; align-items: center; justify-content: flex-start; margin-bottom: 0.5rem; margin-top: -1rem;">
                    <input type="checkbox" id="rememberMe" name="rememberMe" style="margin-right: 8px; margin-left: 12px;">
                    <label for="rememberMe" style="margin: 0; font-size: 1rem; color: #1976d2; cursor: pointer;">Recordar contraseña</label>
                </div>
                <div class="material-login-actions" style="justify-content: center;">
                    <button type="button"
                        onclick="loguearSistema()"
                        class="material-btn material-btn-primary btn-login-custom">
                        INGRESAR
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- /.login-box -->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script
        src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"
        integrity="sha256-dghWARbRe2eLlIJ56wNB+b760ywulqK3DzZYEpsg2fQ="
        crossorigin="anonymous"></script>
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
        src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
        crossorigin="anonymous"></script>
    <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
    <script>
        const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
        const Default = {
            scrollbarTheme: 'os-theme-light',
            scrollbarAutoHide: 'leave',
            scrollbarClickScroll: true,
        };
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
            if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
                OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                    scrollbars: {
                        theme: Default.scrollbarTheme,
                        autoHide: Default.scrollbarAutoHide,
                        clickScroll: Default.scrollbarClickScroll,
                    },
                });
            }
        });
    </script>
    <script>
        const URL_PY = "<?= base_url(); ?>";
    </script>
    <script src="<?= base_url('public/dist/libs/jquery/jquery-3.7.1.min.js') ?>"></script>
    <script src="<?= base_url('public/dist/libs/toastr/build/toastr.min.js') ?>"></script>
    <script src="<?= base_url('public/dist/libs/sweetalert2/dist/sweetalert2.js') ?>"></script>
    <script src="<?= base_url('public/dist/js/adminlte.js') ?>"></script>
    <script src="<?= base_url('public/dist/js/paginas/login.js?v=' . getenv('VERSION')) ?>"></script>
    <!--end::OverlayScrollbars Configure-->
    <!--end::Script-->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('txtpassword');
            const toggle = document.querySelector('.toggle-password');
            const eyeOpen = toggle.querySelector('#eye-open');
            const eyeClosed = toggle.querySelector('#eye-closed');
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    eyeOpen.style.display = 'none';
                    eyeClosed.style.display = '';
                } else {
                    passwordInput.type = 'password';
                    eyeOpen.style.display = '';
                    eyeClosed.style.display = 'none';
                }
            });
        });
    </script>
</body>
<!--end::Body-->

</html>