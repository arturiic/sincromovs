<!--begin::Sidebar-->
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand">
        <!--begin::Brand Link-->
        <a href="<?= base_url('dashboard') ?>" class="nav-link">
            <!--begin::Brand Image-->
            <img
                src="<?= base_url('public/dist/assets/img/logogasiub.png') ?>"
                class="brand-image"
                width="120"
                height="auto" />
            <!--end::Brand Image-->
            <!--begin::Brand Text-->
            <span class="brand-text fw-light"></span>
            <!--end::Brand Text-->
        </a>
        <!--end::Brand Link-->
    </div>
    <!--end::Sidebar Brand-->
    <!--begin::Sidebar Wrapper-->
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fa-solid fa-gear"></i>
                        <p>CONFIGURACION<i class="nav-arrow bi bi-chevron-right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url('config/sincromovimi') ?>" class="nav-link">
                                <i class="nav-icon fa-solid fa-circle-check"></i>
                                <p>Sincro. Movimientos</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item mt-2 has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fa-solid fa-money-bill-transfer"></i>
                        <p>MOVIMIENTOS<i class="nav-arrow bi bi-chevron-right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url('movimientos/destinatarios') ?>" class="nav-link">
                                <i class="nav-icon fa-solid fa-circle-check"></i>
                                <p>Destinatarios</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('movimientos/det_entidad_empresa') ?>" class="nav-link">
                                <i class="nav-icon fa-solid fa-circle-check"></i>
                                <p>Cuentas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('movimientos/movimientos') ?>" class="nav-link">
                                <i class="nav-icon fa-solid fa-circle-check"></i>
                                <p>Movimientos</p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <!--end::Sidebar Menu-->
        </nav>
    </div>
    <!--end::Sidebar Wrapper-->
</aside>
<!--end::Sidebar-->