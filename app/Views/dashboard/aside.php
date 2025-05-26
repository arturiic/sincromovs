<!--begin::Sidebar-->
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand">
        <!--begin::Brand Link-->
        <a href="http://localhost/sincromov/dashboard/" class="brand-link">
            <!--begin::Brand Image-->
            <img
                src="<?= base_url('public/dist/assets/img/logoaisu.webp') ?>"
                class="brand-image" />
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
                <?php
                $urls = session()->get('urls');
                // Primero los elementos padre (nivel 0)
                foreach ($urls as $url) {
                    if ($url['padre'] == 0 && $url['tipo'] != 'GT') {
                        // Verificar si tiene hijos para determinar si mostrar la flecha
                        $hasChildren = false;
                        foreach ($urls as $potentialChild) {
                            if ($potentialChild['padre'] == $url['idbarras_perfil']) {
                                $hasChildren = true;
                                break;
                            }
                        }
                        echo '<li class="nav-item' . ($hasChildren ? ' has-treeview' : '') . '">';
                        echo '<a href="' . ($hasChildren ? '#' : base_url($url['ruta'])) . '" class="nav-link">';
                        echo '<i class="nav-icon ' . $url['logo'] . '"></i>';
                        echo '<p>' . $url['descripcion'];
                        if ($hasChildren) {
                            echo '<i class="nav-arrow bi bi-chevron-right"></i>';
                        }
                        echo '</p>';
                        echo '</a>';

                        // Si tiene hijos, generar el submenú
                        if ($hasChildren) {
                            echo '<ul class="nav nav-treeview">';
                            foreach ($urls as $childUrl) {
                                if ($childUrl['padre'] == $url['idbarras_perfil']) {
                                    echo '<li class="nav-item">';
                                    echo '<a href="' . base_url($childUrl['ruta']) . '" class="nav-link">';
                                    echo '<i class="nav-icon ' . $childUrl['logo'] . '"></i>';
                                    echo '<p>' . $childUrl['descripcion'] . '</p>';
                                    echo '</a>';
                                    echo '</li>';
                                }
                            }
                            echo '</ul>';
                        }

                        echo '</li>';
                    } elseif ($url['tipo'] == 'GT') {
                        // Elementos de tipo 'GT' (grupo/título)
                        echo '<li class="nav-header">' . $url['descripcion'] . '</li>';
                    }
                }
                ?>
            </ul>
            <!--end::Sidebar Menu-->
        </nav>
    </div>
    <!--end::Sidebar Wrapper-->
</aside>
<!--end::Sidebar-->