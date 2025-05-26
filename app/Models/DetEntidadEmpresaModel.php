<?php

namespace App\Models;

use CodeIgniter\Model;

class DetEntidadEmpresaModel extends Model
{
    protected $table      = 'det_entidad_empresa';
    protected $primaryKey = 'iddet_entidad_empresa';
    protected $allowedFields = ['iddet_entidad_empresa', 'descripcion', 'estado', 'identidad_bancaria', 'idempresa'];

    public function traerDetEntidadEmpresa()
    {
        $session = session(); // Accede a la sesión
        $codempresa = $session->get('codempresa'); // Obtiene el codempresa

        return $this->select('det_entidad_empresa.iddet_entidad_empresa, det_entidad_empresa.descripcion, det_entidad_empresa.estado, entbanc.descripcion as entidad_bancaria, emp.descripcion as empresa')
            ->join('empresa emp', 'det_entidad_empresa.idempresa = emp.idempresa')
            ->join('entidad_bancaria entbanc', 'det_entidad_empresa.identidad_bancaria = entbanc.identidad_bancaria')
            ->where('emp.idempresa', $codempresa)
            ->findAll();
    }
    public function exists($descripcion, $id = null)
    {
        $query = $this->where('descripcion', $descripcion);

        // Si se proporciona un ID, excluimos ese registro
        if ($id !== null) {
            $query->where('iddet_entidad_empresa !=', $id);
        }

        return $query->first() !== null;
    }
    public function detEntidadEmpresaXcod($cod)
    {
        return $this->select("descripcion, estado, identidad_bancaria")
            ->where('iddet_entidad_empresa', $cod)
            ->first();
    }
    //PARA EL FOREACH DE MOVIMIENTOS
    public function selectCuentas()
    {
        $session = session(); // Accede a la sesión
        $codempresa = $session->get('codempresa'); // Obtiene el codempresa

        return $this->select('iddet_entidad_empresa, descripcion')
            ->where('idempresa', $codempresa)
            ->where('estado', 'ACTIVO')
            ->findAll();
    }
}
