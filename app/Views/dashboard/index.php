<?php
$this->extend('dashboard/template.php'); ?>
<?= $this->section('titulo_pagina'); ?>
<h3 class="mb-0">DASHBOARD</h3>
<?= $this->endsection() ?>
<?= $this->section('contenido_template'); ?>
<div class="app-content">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <!-- /.col-md-6 -->
            <div class="col-lg-12">
                <div class="card mb-4">
                    <div class="card-header border-0">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">MOVIMIENTOS POR MES - <?= session()->get('nempresa') ?></h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            <p class="d-flex flex-column">
                                <span class="fw-bold fs-5">S/ <?= number_format($totalEntradas ?? 0.00, 2, '.', ',') ?></span>
                                <span>TOTAL ENTRADAS</span>
                            </p>
                            <p class="ms-auto d-flex flex-column text-end">
                                <span class="fw-bold fs-5">S/ <?= number_format($totalSalidas ?? 0.00, 2, '.', ',') ?></span>
                                <span>TOTAL SALIDAS</span>
                            </p>
                        </div>
                        <div class="position-relative mb-4" style="height: 300px;">
                            <canvas id="movements-chart"></canvas>
                        </div>

                        <div class="d-flex flex-wrap justify-content-center">
                            <?php foreach ($meses as $numMes => $nombreMes):
                                $indice = $numMes - 1;
                                $mostrarEntradas = isset($totalesEntradas[$indice]) && $totalesEntradas[$indice] > 0;
                                $mostrarSalidas = isset($totalesSalidas[$indice]) && $totalesSalidas[$indice] > 0;

                                if ($mostrarEntradas || $mostrarSalidas): ?>
                                    <div class="me-3 mb-2 d-flex align-items-center">
                                        <?php if ($mostrarEntradas): ?>
                                            <span class="badge me-2" style="background-color: rgba(0, 123, 255, 0.6); width: 15px; height: 15px;"></span>
                                        <?php endif; ?>
                                        <?php if ($mostrarEntradas && $mostrarSalidas): ?>
                                        <?php endif; ?>
                                        <?php if ($mostrarSalidas): ?>
                                            <span class="badge me-2" style="background-color: rgba(220, 53, 69, 0.6); width: 15px; height: 15px;"></span>
                                        <?php endif; ?>
                                    </div>
                            <?php endif;
                            endforeach; ?>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const ctx = document.getElementById('movements-chart').getContext('2d');

                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: <?= json_encode(array_values($meses)) ?>,
                                datasets: [{
                                        label: 'ENTRADAS',
                                        data: <?= json_encode($totalesEntradas) ?>,
                                        backgroundColor: 'rgba(0, 123, 255, 0.6)',
                                        borderColor: 'rgba(0, 123, 255, 1)',
                                        borderWidth: 1
                                    },
                                    {
                                        label: 'SALIDAS',
                                        data: <?= json_encode($totalesSalidas) ?>,
                                        backgroundColor: 'rgba(220, 53, 69, 0.6)',
                                        borderColor: 'rgba(220, 53, 69, 1)',
                                        borderWidth: 1
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top',
                                        labels: {
                                            boxWidth: 15, // Tamaño del cuadradito en la leyenda
                                            boxHeight: 15, // Altura del cuadradito
                                            padding: 10,
                                            usePointStyle: false, // true para circulito
                                            pointStyle: 'rect' // Tipo de marcador (rect, circle, triangle, etc.)
                                        }
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                return context.dataset.label + ': S/ ' + context.raw.toLocaleString('es-PE', {
                                                    minimumFractionDigits: 2,
                                                    maximumFractionDigits: 2
                                                });
                                            },
                                            title: function(context) {
                                                return context[0].label;
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function(value) {
                                                return 'S/ ' + value.toLocaleString('es-PE');
                                            }
                                        },
                                        grid: {
                                            drawBorder: false
                                        }
                                    },
                                    x: {
                                        grid: {
                                            display: false
                                        }
                                    }
                                }
                            }
                        });
                    });
                </script>
                <!-- Script del gráfico -->
            </div>
            <!-- /.col-md-6 -->
        </div>
        <!--end::Row-->
    </div>
    <!--end::Container-->
</div>
<!--begin::Script-->
<!--begin::Third Party Plugin(OverlayScrollbars)-->
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
<!--end::OverlayScrollbars Configure-->
<!-- OPTIONAL SCRIPTS -->
<!--end::Script-->
<?= $this->endsection() ?>