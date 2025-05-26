<?php
$this->extend('dashboard/template.php'); ?>
<?= $this->section('titulo_pagina'); ?>
<h3 class="mb-0">Sincronizacion de Movimientos</h3>
<?= $this->endsection() ?>
<?= $this->section('contenido_template'); ?>
<div class="app-content">
    <!--begin::Container-->
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Movimientos</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <div class="card-body">
                <div class="row row-cards">
                    <div class="col-md-4 col-12">
                        <div class="form-group">
                            <label><i class="fa-solid fa-calendar-days"></i>&nbsp; Fecha de Inicio</label>
                            <input type="date" class="form-control" id="datefechaini" value="<?php echo date('Y-m-d'); ?>"
                                max="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                    <div class="col-md-4 col-12">
                        <div class="form-group">
                            <label><i class="fa-solid fa-calendar-days"></i>&nbsp; Fecha de Fin</label>
                            <input type="date" class="form-control" id="datefechafin" value="<?php echo date('Y-m-d'); ?>"
                                max="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                    <div class="col-md-4 col-12 d-flex align-items-end">
                        <button onclick="ver_movimientos_sincro()" class="btn btn-warning w-100">
                            <i class="fa-regular fa-eye"></i>&nbsp;Ver Movimientos</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tbl_sincromovi" name="tbl_sincromovi" class="table table-bordered table-hover dataTable dtr-inline">
                        <thead>
                            <tr>
                                <th>Titulo</th>
                                <th>Destinatario</th>
                                <th>Fecha</th>
                                <th>Monto</th>
                                <th>Moneda</th>
                                <th>N° de operación</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <button onclick="insertarMovimientos()" class="btn btn-primary"><i class="fa-solid fa-plus"></i>
                    &nbsp;Insertar Movimientos</button>
            </div>
        </div>
    </div>
    <!--end::Container-->
</div>
<!--end::App Content-->
<?= $this->endsection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('public/dist/js/paginas/sincromovimi.js') ?>"></script>
<?= $this->endsection() ?>