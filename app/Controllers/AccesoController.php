<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\AccesoModel;

class AccesoController extends BaseController
{
    public function getEmpresas()
    {
        $codigousuario = session()->get('usuario');
        $accesoModel = new AccesoModel();
        $data = $accesoModel->getEmpresas($codigousuario);
        //log_message('error', 'Usuario desde sesión: ' . print_r($codigousuario, true));
        return $this->response->setJSON(['success' => true, 'empresas' => $data]);
    }

    public function getSucursales()
    {
        $codigoempresa = $this->request->getPost('empresa');
        $accesoModel = new AccesoModel();
        $data = $accesoModel->getSucursales($codigoempresa);
        return $this->response->setJSON(['success' => true, 'sucursales' => $data]);
    }

    public function getAlmacenes()
    {
        $codigosucursal = $this->request->getPost('sucursal');
        $accesoModel = new AccesoModel();
        $data = $accesoModel->getAlmacenes($codigosucursal);
        return $this->response->setJSON(['success' => true, 'almacenes' => $data]);
    }
    public function accesoAlmacen()
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
