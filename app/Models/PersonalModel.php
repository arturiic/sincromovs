<?php

namespace App\Models;
use CodeIgniter\Model;
class PersonalModel extends Model
{
    protected $table      = 'personal';
    protected $primaryKey = 'idpersonal';
    protected $allowedFields = ['nombre', 'fotito','email'];

    public function nombreFotitoXcod($cod)
    {
        return $this->select('nombre, fotito, email')
            ->where('idpersonal', $cod)
            ->first();
    }
}