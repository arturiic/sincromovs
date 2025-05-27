<?php

namespace App\Models;

use CodeIgniter\Model;

class DestinatariosModel extends Model
{
    protected $table      = 'destinatario';
    protected $primaryKey = 'iddestinatario';
    protected $allowedFields = ['iddestinatario', 'nombre', 'estado'];

    public function traerDestinatarios()
    {
        return $this->select('iddestinatario, nombre, estado')
            ->orderBy('nombre', 'DESC')
            ->findAll();
    }
    public function exists($nombre, $id = null)
    {
        $query = $this->where('nombre', $nombre);

        // Si se proporciona un ID, excluimos ese registro
        if ($id !== null) {
            $query->where('iddestinatario !=', $id);
        }

        return $query->first() !== null;
    }
    public function destinatariosXcod($cod)
    {
        return $this->select('nombre, estado')
            ->where('iddestinatario', $cod)
            ->first();
    }
    //FUNCIONES PARA MOVIMIENTOS
    public function buscarDestinatarios($searchTerm, $limite, $offset)
    {
        return $this->select("iddestinatario,nombre")
            ->where('estado', 'ACTIVO')
            ->like('nombre', $searchTerm)
            ->orderBy('nombre', 'ASC')
            ->limit($limite, $offset) // Asegurar que se usa limit y offset correctamente
            ->findAll();
    }
}