<?php
$this->extend('dashboard/template.php'); ?>
<?= $this->section('titulo_pestaña'); ?>
<title>Movimientos | Destinatarios</title>
<?= $this->endsection() ?>
<?= $this->section('titulo_pagina'); ?>
<h3 class="mb-0">DESTINATARIOS</h3>
<?= $this->endsection() ?>
<?= $this->section('contenido_template'); ?>
<div class="app-content">
    <!--begin::Container-->
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tbldestinatarios" name="tbldestinatarios" class="table table-bordered table-hover dataTable dtr-inline">
                        <thead>
                            <tr>
                                <th class="bg-dark text-white">Cod</th>
                                <th class="bg-dark text-white">NOMBRE</th>
                                <th class="bg-dark text-white">ESTADO</th>
                                <th class="bg-dark text-white">ACCIÓN</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <button onclick="abrirModal()" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i>
                    &nbsp;AGREGAR NUEVO DESTINATARIO</button>
            </div>
        </div>
    </div>
    <!--end::Container-->
</div>
<!--end::App Content-->
<div class="modal fade" id="mdldestinatario" tabindex="-1" role="dialog" aria-labelledby="lbltitulo" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 id="lbltitulo" name="lbltitulo" class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="txtid" name="txtid">
                <div class="col-lg-9">
                    <div class="form-group mb-3">
                        <label class="form-label">NOMBRE COMPLETO</label>
                        <input type="text" class="form-control form-control-sm" id="txtnombre" name="txtnombre" placeholder="Nombre">
                    </div>
                </div>
                <div class="col-lg-3">
                    <label class="form-label">ESTADO</label>
                    <select class="form-select form-select-sm" id="cmbestado" name="cmbestado">
                        <option value="ACTIVO">ACTIVO</option>
                        <option value="INACTIVO">INACTIVO</option>
                    </select>
                </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i>&nbsp;CERRAR</button>
                <button type="button" class="btn btn-primary btn-sm" id="btnregistrar" name="btnregistrar" onclick="registrar()">
                    <i class="fa-solid fa-plus"></i>&nbsp;REGISTRAR</button>
                <button type="button" class="btn btn-warning btn-sm" id="btneditar" name="btneditar" onclick="editar()">
                    <i class="fas fa-pencil"></i>&nbsp;EDITAR</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endsection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('public/dist/js/paginas/destinatarios.js?v='. getenv('VERSION')) ?>"></script>
<?= $this->endsection() ?>