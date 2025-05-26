<?php
$this->extend('dashboard/template.php'); ?>
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
                            <tr style="background-color: #000000;">
                                <th>Cod</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Entidad Bancaria</th>
                                <th>Empresa</th>
                                <th>Acción</th>
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
            <div class="modal-header">
                <h5 id="lbltitulo" name="lbltitulo" class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txtid" name="txtid">
                <div class="col-lg-12">
                    <div class="form-group mb-3">
                        <label class="form-label">Descripción</label>
                        <input type="text" class="form-control" id="txtdescripcion" name="txtdescripcion" placeholder="Descripción">
                    </div>
                </div>
                <div class="form-group row mb-3">
                <div class="col-lg-6">
                    <label class="form-label">Estado</label>
                    <select class="form-select" id="cmbestado" name="cmbestado">
                        <option value="ACTIVO">ACTIVO</option>
                        <option value="INACTIVO">INACTIVO</option>
                    </select>
                </div>
                <div class="col-lg-6">
                    <label class="form-label">Entidad Bancaria</label>
                    <select class="form-select" id="cmbent_bancaria" name="cmbent_bancaria">
                        <option value="1">BCP</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnregistrar" name="btnregistrar" onclick="registrar()">
                    <i class="fas fa-exchange-alt"></i>&nbsp; Registrar</button>
                <button type="button" class="btn btn-warning" id="btneditar" name="btneditar" onclick="editar()">
                    <i class="fas fa-exchange-alt"></i>&nbsp; Editar</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endsection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('public/dist/js/paginas/det_entidad_empresa.js') ?>"></script>
<?= $this->endsection() ?>