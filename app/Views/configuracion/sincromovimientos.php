<?php
$this->extend('dashboard/template.php'); ?>
<?= $this->section('titulo_pestaña'); ?>
<title>Movimientos | Sincronización</title>
<?= $this->endsection() ?>
<?= $this->section('titulo_pagina'); ?>
<h3 class="mb-0">SINCRONIZACIÓN DE MOVIMIENTOS</h3>
<?= $this->endsection() ?>
<?= $this->section('contenido_template'); ?>
<div class="app-content">
    <!--begin::Container-->
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">MOVIMIENTOS</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <div class="card-body">
                <div class="row row-cards">
                    <div class="col-md-4 col-12 mb-3 mb-md-0">
                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-calendar-days"></i>&nbsp; FECHA DE INICIO</label>
                            <input type="date" class="form-control form-control-sm" id="datefechaini" value="<?php echo date('Y-m-d'); ?>"
                                max="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                    <div class="col-md-4 col-12 mb-3 mb-md-0">
                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-calendar-days"></i>&nbsp; FECHA DE FIN</label>
                            <input type="date" class="form-control form-control-sm" id="datefechafin" value="<?php echo date('Y-m-d'); ?>"
                                max="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                    <div class="col-md-4 col-12 d-flex align-items-end mb-3 mb-md-0">
                        <button onclick="verSincronizacionMovimientos()" class="btn btn-warning btn-sm w-100">
                            <i class="fa-regular fa-eye"></i>&nbsp;VER MOVIMIENTOS
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tbl_sincromovi" name="tbl_sincromovi" class="table table-bordered table-hover dataTable dtr-inline"> 
                        <thead>
                            <tr>
                                <th class="bg-dark text-white">TITULO</th>
                                <th class="bg-dark text-white">DESTINATARIO</th>
                                <th class="bg-dark text-white">FECHA</th>
                                <th class="bg-dark text-white">MONTO</th>
                                <th class="bg-dark text-white">MONEDA</th>
                                <th class="bg-dark text-white">N°OPERACION</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <button onclick="insertarMovimientos()" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i>
                    &nbsp;REGISTRAR MOVIMIENTOS</button>
            </div>
        </div>
    </div>
    <!--end::Container-->
</div>
<!--end::App Content-->
<?= $this->endsection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('public/dist/js/paginas/sincromovimi.js?v='. getenv('VERSION')) ?>"></script>
<?= $this->endsection() ?>