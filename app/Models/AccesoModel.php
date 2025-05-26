<?php
namespace App\Models;
use CodeIgniter\Model;
class AccesoModel extends Model
{
    protected $table            = 'acceso ace';
    protected $primaryKey       = 'idacceso';
    protected $allowedFields    = ['idusuarios','idsucursal','acceso'];

    public function traer_empresas($codigousuario)  {
        return $this->distinct()
        ->select ('emp.idempresa, emp.descripcion')
        ->join('sucursal suc','ace.idsucursal = suc.idsucursal')
        ->join('empresa emp','suc.idempresa = emp.idempresa')
        ->where('emp.estado','ACTIVO')
        ->where('ace.acceso','SI')
        ->where('ace.idusuarios', $codigousuario)
        ->findAll();
    }

    // TRAER SUCURSAL
    public function traer_sucursales($codigoempresa) {
        return $this->distinct()
            ->select('suc.idsucursal, suc.descripcion')
            ->join('sucursal suc', 'ace.idsucursal = suc.idsucursal')
            ->join('empresa emp', 'suc.idempresa = emp.idempresa')
            ->where('suc.estado', 'ACTIVO')  
            ->where('emp.idempresa', $codigoempresa)
            ->findAll();
    }
    // TRAER ALMACENES
    public function traer_almacenes($codigosucursal) {
        return $this->distinct()
            ->select('alm.idalmacen, alm.descripcion')
            ->join('sucursal suc', 'ace.idsucursal = suc.idsucursal')
            ->join('almacen alm', 'suc.idsucursal = alm.idsucursal')
            ->where('alm.estado', 'ACTIVO')
            ->where('suc.idsucursal', $codigosucursal)
            ->findAll();
    }  

    public function getAccessData($codusuario,$sucursal,$almacen)
    {
        return $this->select('ace.idusuarios codusu,usu.usuario AS nombre,sucu.descripcion,alm.descripcion descripcion_alm,
                emp.descripcion nempresa,emp.venc_crt,CONCAT(emp.descripcion,"-",emp.ruc)datempresa,emp.ruc,emp.direccion dir_empresa,
                emp.usuario_sol,emp.clavesol,usu.perfil AS perfil,emp.ubigeo,emp.modo_ft_notas,emp.modo_guias,emp.clientid,emp.passid')
            ->join('usuarios usu', 'ace.idusuarios = usu.idusuarios')
            ->join('sucursal sucu', 'ace.idsucursal = sucu.idsucursal')
            ->join('empresa emp', 'emp.idempresa = sucu.idempresa')
            ->join('almacen alm', 'sucu.idsucursal = alm.idsucursal')
            ->where('acceso', 'SI')
            ->where('ace.idusuarios', $codusuario)
            ->where('ace.idsucursal', $sucursal)
            ->where('alm.idalmacen',$almacen)
            ->get()
            ->getRow();
    }  
}
