<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\AccesoModel;

class AccesoController extends BaseController
{
    public function get_empresas()
    {
        $codigousuario = session()->get('usuario');
        $accesoModel = new AccesoModel();
        $data = $accesoModel->traer_empresas($codigousuario);
        //log_message('error', 'Usuario desde sesión: ' . print_r($codigousuario, true));
        return $this->response->setJSON(['success' => true, 'empresas' => $data]);
    }

    public function get_sucursales()
    {
        $codigoempresa = $this->request->getPost('empresa');
        $accesoModel = new AccesoModel();
        $data = $accesoModel->traer_sucursales($codigoempresa);
        return $this->response->setJSON(['success' => true, 'sucursales' => $data]);
    }

    public function get_almacenes()
    {
        $codigosucursal = $this->request->getPost('sucursal');
        $accesoModel = new AccesoModel();
        $data = $accesoModel->traer_almacenes($codigosucursal);
        return $this->response->setJSON(['success' => true, 'almacenes' => $data]);
    }
    public function accesoalmacen()
    {
        $usuario = session()->get('usuario');

        $empresa = $this->request->getPost('idempresa');
        $sucursal = $this->request->getPost('idsucursal');
        $almacen = $this->request->getPost('idalmacen');

        $usuarioModel = new AccesoModel();
        $accessData = $usuarioModel->getAccessData($usuario, $sucursal,$almacen);

        if ($accessData) {
            session()->set([
                'codempresa' => $empresa,
                'codsucursal' => $sucursal,
                'codigoalmacen' => $almacen,
                'nempresa' => $accessData->nempresa,
                'nombrempresa' => $accessData->datempresa,
                'sucursal' => $accessData->descripcion,
                'n_empresa' => $accessData->nempresa,
                'almacen'  => $accessData->descripcion_alm
            ]);
            //log_message('error', 'Sesión después de accesoalmacen: ' . print_r(session()->get(), true));
            return redirect()->back(); // Redirige a la página anterior
        } else {
            return redirect()->to('login');
        }
    }
}
