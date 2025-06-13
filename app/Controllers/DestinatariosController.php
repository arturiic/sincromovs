<?php

namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\DestinatariosModel;

class DestinatariosController extends Controller
{
    public function index()
    {
        return view('movimientos/destinatarios');
    }
    public function traerDestinatarios()
    {
        $destinatario = new DestinatariosModel();
        $data = $destinatario->traerDestinatarios();
        return $this->response->setJSON(['data' => $data]);
    }
    public function insertar()
    {
        $model = new DestinatariosModel();
        $nombre = $this->request->getPost('nombre');
        $estado = $this->request->getPost('estado');

        // Validar que el nombre no sea nulo o vacío
        if (empty($nombre)) {
            return $this->response->setJSON(['error' => 'Ingresa el nombre del destinatario.']);
        }

        if ($model->exists($nombre)) {
            return $this->response->setJSON(['error' => 'El destinatario ingresado ya existe.']);
        }

        $data = [
            'nombre' => $nombre,
            'estado' => $estado
        ];

        try {
            // Inserta el destinatario
            $model->insert($data);

            return $this->response->setJSON(['success' => true, 'message' => 'Destinatario Agregado.']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Ocurrió un error al agregar el destinatario: ' . $e->getMessage()]);
        }
    }
    public function destinatariosXcod()
    {
        $destinatarioModel = new DestinatariosModel();
        $cod = $this->request->getGet('cod');
        $data = $destinatarioModel->destinatariosXcod($cod);
        return $this->response->setJSON([$data]);
    }
    public function update()
    {
        $model = new DestinatariosModel();
        $iddestinatario = $this->request->getPost('cod'); // Obtener ID del destinatario a actualizar
        $nombre = $this->request->getPost('nombre');
        $estado = $this->request->getPost('estado');

        $data = [
            'nombre' => $nombre,
            'estado' => $estado
        ];
        // Validar que el nombre no sea nulo o vacío
        if (empty($nombre)) {
            return $this->response->setJSON(['error' => 'El nombre del destinatario es obligatorio.']);
        }

        try {
            // Llama al método de actualización
            if ($model->update($iddestinatario, $data)) {
                return $this->response->setJSON(['success' => true, 'message' => 'Destinatario actualizado.']);
            } else {
                return $this->response->setJSON(['error' => 'Destinatario no encontrado.']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Ocurrió un error al actualizar el destinatario: ' . $e->getMessage()]);
        }
    }
    public function buscarDestinatarios()
    {
        $termino = $this->request->getGet('q');
        $pagina = $this->request->getGet('page') ?? 1; // Página actual
        $limite = 5; // Resultados por página
        $offset = ($pagina - 1) * $limite; // Calcular desplazamiento

        if (strlen($termino) >= 1) {
            $model = new DestinatariosModel();
            $destinatarios = $model->buscarDestinatarios($termino, $limite, $offset);

            // Contar total de coincidencias con el nombre correcto de la columna
            $total = $model->where('estado', 'ACTIVO')
                ->like('nombre', $termino)
                ->countAllResults();

            return $this->response->setJSON([
                'destinatarios' => $destinatarios,
                'total' => $total,
                'pagina' => $pagina,
                'limite' => $limite
            ]);
        }

        return $this->response->setJSON(['destinatarios' => [], 'total' => 0]);
    }
}