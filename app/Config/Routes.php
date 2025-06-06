<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group('login', function ($routes) {
    $routes->get('/', 'LoginController::index');
    $routes->post('ingresar', 'LoginController::logueoIngreso');
    $routes->get('salir', 'LoginController::salir');
});
$routes->group('', ['filter' => 'AuthFilter'], function ($routes) {
    $routes->get('/', 'Home::index');
    $routes->get('dashboard', 'Home::index');
});
$routes->group('config', ['filter' => 'AuthFilter'], function ($routes) {
    $routes->get('sincromovimi', 'SincroMovimientosController::index');
});
$routes->group('cambio', ['filter' => 'CambioFilter'], function ($routes) {
    $routes->post('empresa', 'AccesoController::getEmpresas');
    $routes->post('sucursal', 'AccesoController::getSucursales');
    $routes->post('almacen', 'AccesoController::getAlmacenes');
    $routes->post('ingresar', 'AccesoController::accesoAlmacen');
});
//RUTAS PARA EL PADRE MOVIMIENTOS
$routes->group('movimientos',['filter' => 'AuthFilter'], function ($routes) {
    $routes->get('sincro', 'SincroMovimientosController::sincronizacionMovimientos');
    $routes->post('insertar_sincromov', 'MovimientosController::guardarMovimientos');
    $routes->post('registrar_xml', 'MovimientosController::registrarMovimientos');
    $routes->get('movEntradaXfecha', 'MovimientosController::movEntradaXfecha');
    $routes->get('movSalidaXfecha', 'MovimientosController::movSalidaXfecha');
    $routes->post('eliminar', 'MovimientosController::eliminar');
    $routes->get('reporte_movimientos.pdf', 'MovimientosController::reportePDFmovimientos');
    $routes->post('registrar_saldo', 'MovimientosController::registrarSaldo');
    $routes->get('reporte_excel_movimientos', 'MovimientosController::reporteExcelMovimientos');
    $routes->get('editar_salida', 'MovimientosController::movimientosXcod');
    $routes->post('actualizar', 'MovimientosController::update');
});
$routes->group('movimientos', ['filter' => 'AuthFilter'], function ($routes) {
    $routes->get('destinatarios', 'DestinatariosController::index');
    $routes->get('det_entidad_empresa', 'DetEntidadEmpresaController::index');
    $routes->get('movimientos', 'MovimientosController::index');
});
$routes->group('destinatarios', ['filter' => 'CambioFilter'], function ($routes) {
    $routes->post('registrar', 'DestinatariosController::insertar');
    $routes->post('editar', 'DestinatariosController::update');
    $routes->get('destinatarioxcod', 'DestinatariosController::destinatariosXcod');
    $routes->get('dtdestinatarios', 'DestinatariosController::traerDestinatarios');
    $routes->get('busc_destinatarios', 'DestinatariosController::buscarDestinatarios');
});
$routes->group('detEntidadEmpresas', ['filter' => 'CambioFilter'], function ($routes) {
    $routes->post('registrar', 'DetEntidadEmpresaController::insertar');
    $routes->post('editar', 'DetEntidadEmpresaController::update');
    $routes->get('dtDetEntidadEmpresas', 'DetEntidadEmpresaController::traerDetEntidadEmpresa');
    $routes->get('det_entidad_empresasxcod', 'DetEntidadEmpresaController::detEntidadEmpresaXcod');
});

