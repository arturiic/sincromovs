<?php
$this->extend('dashboard/template.php'); ?>
<?= $this->section('titulo_pestaña'); ?>
<title>Movimientos | Cuentas</title>
<?= $this->endsection() ?>
<?= $this->section('titulo_pagina'); ?>
<h3 class="mb-0">Cuentas</h3>
<?= $this->endsection() ?>
<?= $this->section('contenido_template'); ?>
<div class="app-content">
    <!--begin::Container-->
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tbldet_entidad_empresa" name="tbldet_entidad_empresa" class="table table-bordered table-hover dataTable dtr-inline">
                        <thead>
                            <tr>
                                <th class="bg-dark text-white">Cod</th>
                                <th class="bg-dark text-white">Descripción</th>
                                <th class="bg-dark text-white">Estado</th>
                                <th class="bg-dark text-white">Entidad Bancaria</th>
                                <th class="bg-dark text-white">Empresa</th>
                                <th class="bg-dark text-white">Acción</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <button onclick="abrirModal()" class="btn btn-primary"><i class="fa-solid fa-plus"></i>
                    &nbsp;Agregar Nueva Cuenta</button>
            </div>
        </div>
    </div>
    <!--end::Container-->
</div>
<!--end::App Content-->
<div class="modal fade" id="mdldet_entidad_empresa" tabindex="-1" role="dialog" aria-labelledby="lbltitulo" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 id="lbltitulo" name="lbltitulo" class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txtid" name="txtid">
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="form-group">
                            <label class="form-label">Descripción</label>
                            <input type="text" class="form-control form-control-sm" id="txtdescripcion" name="txtdescripcion" placeholder="Descripción">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <div class="form-group">
                            <label class="form-label">Estado</label>
                            <select class="form-select form-select-sm" id="cmbestado" name="cmbestado">
                                <option value="ACTIVO">ACTIVO</option>
                                <option value="INACTIVO">INACTIVO</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6 col-12 mb-3">
                        <div class="form-group">
                            <label class="form-label">Entidad Bancaria</label>
                            <select class="form-select form-select-sm" id="cmbent_bancaria" name="cmbent_bancaria">
                                <option value="1">BCP</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i>&nbsp;Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnregistrar" name="btnregistrar" onclick="registrar()">
                    <i class="fas fa-plus"></i>&nbsp; Registrar</button>
                <button type="button" class="btn btn-warning" id="btneditar" name="btneditar" onclick="editar()">
                    <i class="fas fa-pencil"></i>&nbsp; Editar</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endsection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('public/dist/js/paginas/det_entidad_empresa.js') ?>"></script>
<?= $this->endsection() ?>