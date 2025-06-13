<?php
$this->extend('dashboard/template.php'); ?>
<?= $this->section('titulo_pestaña'); ?>
<title>Movimientos | Registrar</title>
<?= $this->endsection() ?>
<?= $this->section('titulo_pagina'); ?>
<h3 class="mb-0">MOVIMIENTOS</h3>
<?= $this->endsection() ?>
<?= $this->section('contenido_template'); ?>
<div class="app-content">
    <!--begin::Container-->
    <div class="container-fluid">
        <div class="card">
            <div class="col-12 col-lg">
                <div class="card card-primary card-outline card-tabs">
                    <div class="card-header p-0 pt-1 border-bottom-0">
                        <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="custom-tabs-three-home-tab" data-bs-toggle="tab" href="#tabentrada" role="tab" aria-controls="custom-tabs-three-home" aria-selected="true"><i class="fa-solid fa-door-closed"></i>&nbsp; Entrada</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-three-profile-tab" data-bs-toggle="tab" href="#tabsalida" role="tab" aria-controls="custom-tabs-three-profile" aria-selected="false"><i class="fa-solid fa-door-open"></i>&nbsp; Salida</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="custom-tabs-three-tabContent">
                            <!---------------------------------------------- TAB DE ENTRADA ------------------------------------------------>
                            <div class="tab-pane fade show active" id="tabentrada" role="tabpanel" aria-labelledby="custom-tabs-three-home-tab">
                                <div class="row row-cards mb-md-3">
                                    <div class="col-md-10 col-12 mb-3 mb-md-0 order-md-1 order-2">
                                        <div class="form-group">
                                            <label class="form-label"><i class="fa-solid fa-money-check-dollar"></i>&nbsp; CUENTA</label>
                                            <select class="form-select form-select-sm" id="cmbdetentempresa" name="cmbdetentempresa">
                                                <?php foreach ($cuentas as $cuentasreg): ?>
                                                    <option value="<?= esc($cuentasreg['iddet_entidad_empresa']); ?>">
                                                        <?= esc($cuentasreg['descripcion']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-12 d-flex align-items-end mb-3 mb-md-0 order-md-2 order-1">
                                        <button class="btn btn-success btn-sm w-100" id="btnsaldo" name="btnsaldo" onclick="abrirModalSaldo()">
                                            <i class="fa-solid fa-money-bills"></i>&nbsp; INGRESAR SALDO
                                        </button>
                                    </div>
                                </div>
                                <div class="row row-cards">
                                    <div class="col-12 mb-3">
                                        <div class="form-group">
                                            <label class="form-label"><i class="fa-solid fa-users"></i>&nbsp; DESTINATARIO</label>
                                            <input type="hidden" id="txtiddest" name="txtiddest">
                                            <div class="input-group input-group-sm">
                                                <input type="text" class="form-control" id="txtdestinatario" name="txtdestinatario" placeholder="Escribe al menos 3 digitos...">
                                                <button class="btn btn-outline-primary btn-sm" id="btnregistrardesti" name="btnregistrardesti" onclick="registrarDestinatario()" type="button">
                                                    <i class="fa-solid fa-plus"></i>&nbsp;REGISTRAR
                                                </button>
                                            </div>
                                            <ul id="resultados_destinatario" class="list-group"></ul>
                                            <button id="cargarMas_destinatario" class="btn btn-secondary btn-sm mt-2 w-100" style="display: none;">
                                                <i class="fas fa-sync-alt"></i> CARGAR MÁS REGISTROS
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-cards">
                                    <div class="col-lg-8 col-12 mb-3">
                                        <div class="form-group">
                                            <label class="form-label"><i class="fa-solid fa-comment"></i>&nbsp; OBSERVACIÓN</label>
                                            <textarea class="form-control form-control-sm" rows="1" id="txtobservacion" name="txtobservacion"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-12 mb-3">
                                        <div class="form-group">
                                            <label class="form-label"><i class="fa-solid fa-calendar-days"></i>&nbsp; FECHA</label>
                                            <input type="date" class="form-control form-control-sm" id="datefecha" name="datefecha" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-cards">
                                    <div class="col-md-5 col-12 mb-3 mb-md-0">
                                        <div class="form-group">
                                            <label class="form-label"><i class="fa-solid fa-money-bill-wave"></i>&nbsp; MONTO</label>
                                            <input type="text" class="form-control form-control-sm" id="txtmonto" name="txtmonto" placeholder="Monto" maxlength="8">
                                        </div>
                                    </div>
                                    <div class="col-md-5 col-12 mb-3 mb-md-0">
                                        <div class="form-group">
                                            <label class="form-label"><i class="fa-solid fa-hashtag"></i>&nbsp; N°OPERACIÓN</label>
                                            <input type="text" class="form-control form-control-sm" id="txtnoperacion" name="txtnoperacion" placeholder="Número de operación" maxlength="20">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-12 d-flex align-items-end mb-3 mb-md-0">
                                        <button class="btn btn-primary btn-sm w-100" onclick="registrarMovEntrada()">
                                            <i class="fa-solid fa-floppy-disk"></i>&nbsp; REGISTRAR
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!---------------------------------------------- TAB DE SALIDA ------------------------------------------------>
                            <div class="tab-pane fade" id="tabsalida" role="tabpanel" aria-labelledby="custom-tabs-three-profile-tab">
                                <div class="row row-cards">
                                    <div class="col-12 mb-3">
                                        <div class="form-group">
                                            <input type="hidden" id="txtidmovsalida" name="txtidmovsalida">
                                            <label class="form-label"><i class="fa-solid fa-money-check-dollar"></i>&nbsp; CUENTA</label>
                                            <select class="form-select form-select-sm" id="cmbdetentempresa2" name="cmbdetentempresa2">
                                                <?php foreach ($cuentas as $cuentasreg): ?>
                                                    <option value="<?= esc($cuentasreg['iddet_entidad_empresa']); ?>">
                                                        <?= esc($cuentasreg['descripcion']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-cards">
                                    <div class="col-12 mb-3">
                                        <div class="form-group">
                                            <label class="form-label"><i class="fa-solid fa-users"></i>&nbsp; DESTINATARIO</label>
                                            <input type="hidden" id="txtiddest2" name="txtiddest2">
                                            <div class="input-group input-group-sm">
                                                <input type="text" class="form-control" id="txtdestinatario2" name="txtdestinatario2" placeholder="Escribe al menos 3 digitos...">
                                                <button class="btn btn-outline-primary btn-sm" id="btnregistrardesti2" name="btnregistrardesti2" onclick="registrarDestinatario()" type="button">
                                                    <i class="fa-solid fa-plus"></i>&nbsp;REGISTRAR
                                                </button>
                                            </div>
                                            <ul id="resultados_destinatario2" class="list-group"></ul>
                                            <button id="cargarMas_destinatario2" class="btn btn-secondary btn-sm mt-2 w-100" style="display: none;">
                                                <i class="fas fa-sync-alt"></i> CARGAR MÁS REGISTROS
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-cards">
                                    <div class="col-lg-8 col-12 mb-3">
                                        <div class="form-group">
                                            <label class="form-label"><i class="fa-solid fa-comment"></i>&nbsp; OBSERVACIÓN</label>
                                            <textarea class="form-control form-control-sm" rows="1" id="txtobservacion2" name="txtobservacion2"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-12 mb-3">
                                        <div class="form-group">
                                            <label class="form-label"><i class="fa-solid fa-calendar-days"></i>&nbsp; FECHA</label>
                                            <input type="date" class="form-control form-control-sm" id="datefecha2" name="datefecha2" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-cards">
                                    <div class="col-md-5 col-12 mb-3 mb-md-0">
                                        <div class=" form-group">
                                            <label class="form-label"><i class="fa-solid fa-money-bill-wave"></i>&nbsp; MONTO</label>
                                            <input type="text" class="form-control form-control-sm" id="txtmonto2" name="txtmonto2" placeholder="Monto" maxlength="8">
                                        </div>
                                    </div>
                                    <div class="col-md-5 col-12 mb-3 mb-md-0">
                                        <div class="form-group">
                                            <label class="form-label"><i class="fa-solid fa-hashtag"></i>&nbsp; N°OPERACIÓN</label>
                                            <input type="text" class="form-control form-control-sm" id="txtnoperacion2" name="txtnoperacion2" placeholder="Número de operación" maxlength="20">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-12 d-flex align-items-end mb-3 mb-md-0">
                                        <button class="btn btn-primary btn-sm w-100" onclick="registrarMovSalida()" id="btnregistrar2" name="btnregistrar2">
                                            <i class="fa-solid fa-floppy-disk"></i>
                                            &nbsp;REGISTRAR
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!------------------------------------------------- FIN DE TABS ----------------------------------------------------->
                        <!------------------------------------------------ TABLA MOVIMIENTOS ---------------------------------------------------->
                        <div class="col-sm-12 mt-md-4">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3 class="card-title mb-0"><i class="fas fa-list"></i> &nbsp;REGISTRO DE MOVIMIENTOS</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="tblmovimientos" name="tblmovimientos" class="table table-bordered table-hover dataTable dtr-inline">
                                            <thead>
                                                <tr>
                                                    <th class="bg-dark text-white">DESTINATARIO</th>
                                                    <th class="bg-dark text-white">OBSERVACIÓN</th>
                                                    <th class="bg-dark text-white">FECHA</th>
                                                    <th class="bg-dark text-white">MONTO</th>
                                                    <th class="bg-dark text-white">N°OPERACIÓN</th>
                                                    <th class="bg-dark text-white">ACCIÓN</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="col-md-2 col-12 d-flex align-items-end mb-3 mb-md-0">
                                        <button class="btn btn-warning btn-sm w-100" onclick="abrirModalPDF()">
                                            <i class="fa-solid fa-file-import"></i>&nbsp;GENERAR REPORTE
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-------------------------------------------------------MODAL PDF---------------------------------------------------------->
    <div class="modal modal-blur fade" tabindex="-1" role="dialog" id="mdlpdf" name="mdlpdf">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 id="lbltitulos" name="lbltitulos" class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">FECHA DE INICIO:</label>
                            <input type="date" class="form-control form-control-sm" id="dtpfechaini" name="dtpfechaini" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>" />
                        </div>
                        <div class="col-6">
                            <label class="form-label">FECHA DE FIN:</label>
                            <input type="date" class="form-control form-control-sm" id="dtpfechafin" name="dtpfechafin" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg"></i>&nbsp;CERRAR
                        </button>
                        <div class="d-flex ms-auto">
                            <button class="btn btn-outline-danger btn-5" id="btngenerar" name="btngenerar" onclick="reportePDFmovimientos()">
                                <i class="fa-solid fa-file-pdf"></i>&nbsp;PDF
                            </button>
                            <button class="btn btn-outline-success btn-5 ms-2" id="btnexcel" name="btnexcel" onclick="reporteExcelMovimientos()">
                                <i class="fa-solid fa-file-excel"></i>&nbsp;EXCEL
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!---------------------------------------------------------MODAL SALDO----------------------------------------------------------------->
    <div class="modal modal-blur fade" tabindex="-1" role="dialog" id="mdlingsaldo" name="mdlingsaldo">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 id="lbltitulo3" name="lbltitulo3" class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group">
                                <label class="form-label">OBSERVACIÓN</label>
                                <input type="text" class="form-control form-control-sm" id="txtobservacionS" name="txtobservacionS" placeholder="Descripcion">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-12 mb-3">
                            <div class="form-group">
                                <label class="form-label">SALDO</label>
                                <input type="text" class="form-control form-control-sm" id="txtsaldo" name="txtsaldo" placeholder="Saldo" maxlength="8">
                            </div>
                        </div>
                        <div class="col-lg-6 col-12 mb-3">
                            <div class="form-group">
                                <label class="form-label">N°OPERACIÓN</label>
                                <input type="text" class="form-control form-control-sm" id="txtnoperacionS" name="txtnoperacionS" placeholder="Número de Operación" maxlength="12">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i>&nbsp;CERRAR</button>
                        <button class="btn btn-success btn-5 ms-auto" id="btngenerar" name="btngenerar" onclick="registrarMovSaldo()">
                            <i class="fa-solid fa-money-bills"></i>&nbsp; REGISTRAR
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!---------------------------------------------------------MODAL MOTIVO----------------------------------------------------------------->
    <div class="modal modal-blur fade" tabindex="-1" role="dialog" id="mdlmotivo" name="mdlmotivo">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 id="lbltitulo4" name="lbltitulo4" class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">N°OPERACIÓN</label>
                            <input type="text" class="form-control form-control-sm" id="txtidoperacion" name="txtidoperacion" disabled>
                        </div>
                        <div class="col-6">
                            <label class="form-label">MONTO:</label>
                            <input type="text" class="form-control form-control-sm" id="txtmontomotivo" name="txtmontomotivo" disabled>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="col-12">
                            <label class="form-label">ENVIADO A:</label>
                            <input type="text" class="form-control form-control-sm" id="txtenviadoa" name="txtenviadoa" disabled>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="col-12">
                            <label class="form-label">MOTIVO:</label>
                            <input type="text" class="form-control form-control-sm" id="txtmotivo" name="txtmotivo" placeholder="Motivo">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i>&nbsp;CERRAR</button>
                        <button class="btn btn-primary btn-5 ms-auto" onclick="editar()" id="btneditar" name="btneditar">
                            <i class="fa-solid fa-square-plus"></i>
                            &nbsp;GUARDAR
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?= $this->endsection() ?>

    <?= $this->section('scripts') ?>
    <script src="<?= base_url('public/dist/js/paginas/movimientos.js?v=' . getenv('VERSION')) ?>"></script>
    <?= $this->endsection() ?>