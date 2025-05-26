<?php
namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\DetEntidadEmpresaModel;

class DetEntidadEmpresaController extends Controller
{
    public function index()
    {
        return view('movimientos/det_entidad_empresa');
    }
    public function traerDetEntidadEmpresa()
    {
        $ent_empresa = new DetEntidadEmpresaModel();
        $data = $ent_empresa->traerDetEntidadEmpresa();
        return $this->response->setJSON(['data' => $data]);
    }
    public function insertar()
    {
        $model = new DetEntidadEmpresaModel();
        $descripcion = $this->request->getPost('descripcion');
        $estado = $this->request->getPost('estado');
        $identidad_bancaria = $this->request->getPost('identidad_bancaria');
        $idempresa = session('codempresa');

        // Validar la descripcion no sea nulo o vacío
        if (empty($descripcion)) {
            return $this->response->setJSON(['error' => 'La descripcion es obligatoria.']);
        }
        // Verificar si la descripción ya existe
        if ($model->exists($descripcion)) {
            return $this->response->setJSON(['error' => 'La entidad empresa ingresada ya existe.']);
        }
        $data = [
            'descripcion' => $descripcion,
            'estado' => $estado,
            'identidad_bancaria' => $identidad_bancaria,
            'idempresa' => $idempresa
        ];
        try {
            // Inserta la entidad empresa
            $model->insert($data);

            return $this->response->setJSON(['success' => true, 'message' => 'Entidad empresa Agregada.']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Ocurrió un error al agregar la entidad empresa: ' . $e->getMessage()]);
        }
    }
    public function detEntidadEmpresaXcod()
    {
        $ent_empresaModel = new DetEntidadEmpresaModel();
        $cod = $this->request->getGet('cod');
        $data = $ent_empresaModel->detEntidadEmpresaXcod($cod);
        return $this->response->setJSON([$data]);
    }
    public function update()
    {
        $model = new DetEntidadEmpresaModel();
        $iddet_entidad_empresa = $this->request->getPost('cod'); // Obtener ID del destinatario a actualizar
        $descripcion = $this->request->getPost('descripcion');
        $estado = $this->request->getPost('estado');
        $identidad_bancaria = $this->request->getPost('identidad_bancaria');

        $data = [
            'descripcion' => $descripcion,
            'estado' => $estado,
            'identidad_bancaria' => $identidad_bancaria
        ];
        // Validar la descripcion no sea nulo o vacío
        if (empty($descripcion)) {
            return $this->response->setJSON(['error' => 'La descripcion es obligatoria.']);
        }

        try {
            // Llama al método de actualización
            if ($model->update($iddet_entidad_empresa, $data)) {
                return $this->response->setJSON(['success' => true, 'message' => 'Entidad empresa actualizada.']);
            } else {
                return $this->response->setJSON(['error' => 'Entidad empresa no encontrada.']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Ocurrió un error al actualizar la entidad empresa: ' . $e->getMessage()]);
        }
    }
}