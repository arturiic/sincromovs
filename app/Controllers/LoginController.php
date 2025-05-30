<?php
namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\UsuariosModel;
use App\Models\BarrasPerfilModel;

class LoginController extends Controller
{
    public function index()
  {
    $usuarios = new UsuariosModel();
    $data['usuarios'] = $usuarios->usuariosActivos();
    return view('login/login', $data);
  }
  public function unauthorized()
  {
    return view('unauthorized.php');
  }
  public function salir()
  {
    $session = session();
    $session->destroy();
    return redirect()->to('/login');
  }
  public function logueoIngreso()
  {    
    $clave = $this->request->getPost('password');
    $usuario = $this->request->getPost('idusuario');

    try {
      $usuarioModel = new UsuariosModel();
      $BarrasperfilModel = new BarrasPerfilModel();
      // Verifica el usuario y la contraseña
      $userData = $usuarioModel->getUser($usuario, $clave); // Implementa este método en tu modelo
      //log_message('error', 'Datos recibidos: ' . json_encode($userData));

      if ($userData) {
        // Si se encontró el usuario, verifica el acceso
        $accessData = $usuarioModel->getAccessData($usuario); // Implementa este método en tu modelo

        $url_x_perfil = $BarrasperfilModel->geturlsxperfil_aside($accessData->perfil);
        //log_message('error', 'Datos recibidos: ' . json_encode($url_x_perfil));

        if ($accessData) {
          // Almacena en sesión los datos necesarios
          session()->set([
            'nombreusuario' => $accessData->nombre,
            'nombreusuariocorto' => $userData['usuario'],
            'usuario' => $userData['idusuarios'],
            'password' => $clave,
            'perfil' => $accessData->perfil,
            'nombre_personal' => $accessData->nombre_personal,
            'correo' => $accessData->correo,
            'urls' => $url_x_perfil,
            'is_logged' => true
          ]);
          //log_message('error', 'VARIABLES: ' . print_r(session()->get(), true));
          return $this->response->setJSON([
            'success' => true
          ]);
        }
      } else {
        return $this->response->setJSON([
          'success' => false,
          'mensaje' => 'Usuario o Clave Incorrecto'
        ]);
      }
    } catch (\Exception $e) {
      return json_encode(['error' => ['text' => $e->getMessage()]]);
    }
  }
}
?>