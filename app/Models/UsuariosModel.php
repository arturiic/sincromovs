<?php

namespace App\Models;
use CodeIgniter\Model;
class UsuariosModel extends Model
{
    protected $table      = 'usuarios';
    protected $primaryKey = 'idusuarios';
    protected $allowedFields = ['usuario', 'clave'];

    public function usuarios_activos()
    {
    return $this->select('idusuarios, usuario')
                ->where('estado', 'ACTIVO')
                ->orderBy("(CASE WHEN idusuarios = 32 THEN 0 ELSE 1 END)", 'ASC') // Para que mi usuario aparezca primero
                ->orderBy('idusuarios','ASC') 
                ->findAll();
    }
    public function getAccessData($codusuario)
    {
        return $this->db->table('acceso ace')
            ->select('ace.idusuarios codusu,usu.usuario AS nombre,usu.perfil AS perfil,usu.idusuarios,p.nombre AS nombre_personal,p.email AS correo, p.fotito as fotito')
            ->join('usuarios usu', 'ace.idusuarios = usu.idusuarios')
            ->join('personal p', 'usu.idpersonal = p.idpersonal')
            ->where('acceso', 'SI')
            ->where('ace.idusuarios', $codusuario)
            ->get()
            ->getRow();
    }

    public function getUser($usuario, $clave)
    {
        // Obtener el usuario desde la base de datos
        $user = $this->where('idusuarios', $usuario)
                     ->where('estado', 'ACTIVO')
                     ->first();        
    
        // Verificar si el usuario fue encontrado
        if ($user) {
           
            $passwordCheck = password_verify($clave, $user['password_usu']);            
                
            if ($passwordCheck) {
                return $user; // La contraseña es correcta
            }
        }        
        
        return null; // Usuario desactivado o contraseña incorrecta
    }
}