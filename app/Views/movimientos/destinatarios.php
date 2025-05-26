<?php
$this->extend('dashboard/template.php'); ?>
<?= $this->section('titulo_pagina'); ?>
<h3 class="mb-0">Destinatarios</h3>
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
                                <th>Cod</th>
                                <th>Nombre</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <button onclick="abrirModal()" class="btn btn-primary"><i class="fa-solid fa-plus"></i>
                    &nbsp;Agregar Nuevo Destinatario</button>
            </div>
        </div>
    </div>
    <!--end::Container-->
</div>
<!--end::App Content-->
<div class="modal fade" id="mdldestinatario" tabindex="-1" role="dialog" aria-labelledby="lbltitulo" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="lbltitulo" name="lbltitulo" class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="txtid" name="txtid">
                <div class="col-lg-9">
                    <div class="form-group mb-3">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" id="txtnombre" name="txtnombre" placeholder="Nombre">
                    </div>
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Estado</label>
                    <select class="form-select" id="cmbestado" name="cmbestado">
                        <option value="ACTIVO">ACTIVO</option>
                        <option value="INACTIVO">INACTIVO</option>
                    </select>
                </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnregistrar" name="btnregistrar" onclick="registrar()">
                    <i class="fas fa-exchange-alt"></i>&nbsp;Registrar</button>
                <button type="button" class="btn btn-warning" id="btneditar" name="btneditar" onclick="editar()">
                    <i class="fas fa-exchange-alt"></i>&nbsp;Editar</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endsection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('public/dist/js/paginas/destinatarios.js') ?>"></script>
<?= $this->endsection() ?>