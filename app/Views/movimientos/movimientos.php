<?php
$this->extend('dashboard/template.php'); ?>
<?= $this->section('titulo_pagina'); ?>
<h3 class="mb-0">Movimientos</h3>
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
                                <div class="row row-cards">
                                    <div class="col-12">
                                        <div class="form-group mb-3">
                                            <label><i class="fa-solid fa-money-check-dollar"></i>&nbsp; Cuenta</label>
                                            <select class="form-select" id="cmbdetentempresa" name="cmbdetentempresa">
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
                                    <div class="col-12">
                                        <div class="form-group mb-3">
                                            <label><i class="fa-solid fa-users"></i>&nbsp; Destinatario</label>
                                            <input type="hidden" id="txtiddest" name="txtiddest">
                                            <div class="input-group mb-2">
                                                <input type="text" class="form-control" id="txtdestinatario" name="txtdestinatario" placeholder="Destinatario" disabled>
                                                <button class="btn btn-outline-primary" id="btneledestinatario" name="btneledestinatario" onclick="elegirDestinatario()" type="button">
                                                    <i class="fa-solid fa-magnifying-glass"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-cards">
                                    <div class="col-12">
                                        <div class="form-group mb-3">
                                            <label><i class="fa-solid fa-comment"></i>&nbsp; Observacion</label>
                                            <textarea class="form-control" rows="1" id="txtobservacion" name="txtobservacion"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-cards mb-3">
                                    <div class="col-4">
                                        <div class="form-group mb-3">
                                            <label><i class="fa-solid fa-calendar-days"></i>&nbsp; Fecha</label>
                                            <input type="date" class="form-control" id="datefecha" name="datefecha" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>" />
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group mb-3">
                                            <label><i class="fa-solid fa-money-bill-wave"></i>&nbsp; Monto</label>
                                            <input type="text" class="form-control" id="txtmonto" name="txtmonto" placeholder="Monto" maxlength="8" onkeypress="return (event.charCode >= 48 && event.charCode <= 57)">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group mb-3">
                                            <label><i class="fa-solid fa-hashtag"></i>&nbsp; N°operación</label>
                                            <input type="text" class="form-control" id="txtnoperacion" name="txtnoperacion" placeholder="Número de operación" maxlength="20" onkeypress="return (event.charCode >= 48 && event.charCode <= 57)">
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-cards">
                                    <div class="col-12 d-flex justify-content-end">
                                        <button class="btn btn-primary btn-5" onclick="registrarMovEntrada()"><i class="fa-solid fa-floppy-disk"></i>
                                            &nbsp;Registrar
                                        </button>
                                        <button class="btn btn-success ms-auto" id="btnsaldo" name="btnsaldo" onclick="abrirModalSaldo()">
                                            <i class="fa-solid fa-money-bills"></i>
                                            &nbsp; Ingresar Saldo
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!---------------------------------------------- TAB DE SALIDA ------------------------------------------------>
                            <div class="tab-pane fade" id="tabsalida" role="tabpanel" aria-labelledby="custom-tabs-three-profile-tab">
                                <div class="row row-cards">
                                    <div class="col-12">
                                        <div class="form-group mb-3">
                                            <input type="hidden" id="txtidmovsalida" name="txtidmovsalida">
                                            <label><i class="fa-solid fa-money-check-dollar"></i>&nbsp; Cuenta</label>
                                            <select class="form-select" id="cmbdetentempresa2" name="cmbdetentempresa2">
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
                                    <div class="col-12">
                                        <div class="form-group mb-3">
                                            <label><i class="fa-solid fa-users"></i>&nbsp; Destinatario</label>
                                            <input type="hidden" id="txtiddest2" name="txtiddest2">
                                            <div class="input-group mb-2">
                                                <input type="text" class="form-control" id="txtdestinatario2" name="txtdestinatario2" placeholder="Destinatarios" disabled>
                                                <button class="btn btn-outline-primary" id="btneledestinatario" name="btneledestinatario" onclick="elegirDestinatario()" type="button">
                                                    <i class="fa-solid fa-magnifying-glass"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-cards">
                                    <div class="col-12">
                                        <div class="form-group mb-3">
                                            <label><i class="fa-solid fa-comment"></i>&nbsp; Observacion</label>
                                            <textarea class="form-control" rows="1" id="txtobservacion2" name="txtobservacion2"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-cards mb-3">
                                    <div class="col-4">
                                        <div class="form-group mb-3">
                                            <label><i class="fa-solid fa-calendar-days"></i>&nbsp; Fecha</label>
                                            <input type="date" class="form-control" id="datefecha2" name="datefecha2" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>" />
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group mb-3">
                                            <label><i class="fa-solid fa-money-bill-wave"></i>&nbsp; Monto</label>
                                            <input type="text" class="form-control" id="txtmonto2" name="txtmonto2" placeholder="Monto" maxlength="8" onkeypress="return (event.charCode >= 48 && event.charCode <= 57)">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group mb-3">
                                            <label><i class="fa-solid fa-hashtag"></i>&nbsp; N°operación</label>
                                            <input type="text" class="form-control" id="txtnoperacion2" name="txtnoperacion2" placeholder="Número de operación" maxlength="20" onkeypress="return (event.charCode >= 48 && event.charCode <= 57)">
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-cards">
                                    <div class="col-12">
                                        <button class="btn btn-primary btn-5 ms-auto" onclick="registrarMovSalida()" id="btnregistrar2" name="btnregistrar2">
                                            <i class="fa-solid fa-floppy-disk"></i>
                                            &nbsp;Registrar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!------------------------------------------------- FIN DE TABS ----------------------------------------------------->
                </div>
                <!------------------------------------------------ TABLA MOVIMIENTOS ---------------------------------------------------->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblmovimientos" name="tblmovimientos" class="table table-bordered table-hover dataTable dtr-inline">
                            <thead>
                                <tr style="background-color: #000000;">
                                    <th>Destinatario</th>
                                    <th>Observacion</th>
                                    <th>Fecha</th>
                                    <th>Monto</th>
                                    <th>N°Operación</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <button class="btn btn-6 btn-outline-warning d-sm-inline-block" onclick="abrirModalPDF()">
                        <i class="fa-solid fa-file-import"></i>&nbsp;Generar Reporte
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!---------------------------------------------------MODAL DESTINATARIOS------------------------------------------------>
<div class="modal modal-blur fade" tabindex="-1" role="dialog" id="mdleledestinatario" name="mdleledestinatario">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="lbltitulo" name="lbltitulo" class="modal-title">Elegir Destinatario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <!-- Buscador -->
                        <div class="input-group mb-3">
                            <input type="text" id="buscador" class="form-control" placeholder="Escribe al menos 1 letra..." autocomplete="off">
                            <button class="btn btn-outline-primary" type="button" disabled>
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <!-- Resultados -->
                        <ul id="resultados" class="list-group"></ul>
                        <!-- Botón "Cargar más" -->
                        <button id="cargarMas" class="btn btn-secondary btn-sm mt-2 w-100" style="display: none;">
                            <i class="fas fa-sync-alt"></i> Cargar más
                        </button>
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
            <div class="modal-header">
                <h5 id="lbltitulos" name="lbltitulos" class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label">Fecha de inicio:</label>
                        <input type="date" class="form-control" id="dtpfechaini" name="dtpfechaini" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>" />
                    </div>
                    <div class="col-6">
                        <label class="form-label">Fecha de fin:</label>
                        <input type="date" class="form-control" id="dtpfechafin" name="dtpfechafin" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button class="btn btn-outline-danger btn-5 ms-auto" id="btngenerar" name="btngenerar" onclick="reportePDFmovimientos()">
                        <i class="fa-solid fa-file-pdf"></i>&nbsp;Generar PDF
                    </button>
                    <button class="btn btn-outline-success btn-5 ms-auto" id="btnexcel" name="btnexcel" onclick="reporteExcelMovimientos()">
                        <i class="fa-solid fa-file-excel"></i>&nbsp;Generar Excel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!---------------------------------------------------------MODAL SALDO----------------------------------------------------------------->
<div class="modal modal-blur fade" tabindex="-1" role="dialog" id="mdlingsaldo" name="mdlingsaldo">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="lbltitulo3" name="lbltitulo3" class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="col-12">
                        <label class="form-label">Observacion</label>
                        <input type="text" class="form-control" id="txtobservacionS" name="txtobservacionS" placeholder="Descripcion">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label">Saldo</label>
                        <input type="text" class="form-control" id="txtsaldo" name="txtsaldo" placeholder="Saldo" maxlength="8" onkeypress="return (event.charCode >= 48 && event.charCode <= 57)">
                    </div>
                    <div class="col-6">
                        <label class="form-label">N°Operación</label>
                        <input type="text" class="form-control" id="txtnoperacionS" name="txtnoperacionS" placeholder="Número de Operación" maxlength="12" onkeypress="return (event.charCode >= 48 && event.charCode <= 57)">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button class="btn btn-success btn-5 ms-auto" id="btngenerar" name="btngenerar" onclick="registrarMovSaldo()">
                        <i class="fa-solid fa-money-bills"></i>&nbsp; Registrar
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
            <div class="modal-header">
                <h5 id="lbltitulo4" name="lbltitulo4" class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label">N° de Operación:</label>
                        <input type="text" class="form-control" id="txtidoperacion" name="txtidoperacion" disabled>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Monto:</label>
                        <input type="text" class="form-control" id="txtmontomotivo" name="txtmontomotivo" disabled>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="col-12">
                        <label class="form-label">Enviado a:</label>
                        <input type="text" class="form-control" id="txtenviadoa" name="txtenviadoa" disabled>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="col-12">
                        <label class="form-label">Motivo:</label>
                        <input type="text" class="form-control" id="txtmotivo" name="txtmotivo" placeholder="Motivo">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button class="btn btn-primary btn-5 ms-auto" onclick="editar()" id="btneditar" name="btneditar">
                        <i class="fa-solid fa-square-plus"></i>
                        &nbsp;Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endsection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('public/dist/js/paginas/movimientos.js') ?>"></script>
<?= $this->endsection() ?>