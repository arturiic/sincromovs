<?php

namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\PersonalModel;

class PersonalController extends Controller
{
    public function nombreFotitoXcod()
    {
        $personalModel = new PersonalModel();
        $cod = $this->request->getGet('cod');
        $data = $personalModel->nombreFotitoXcod($cod);
        return $this->response->setJSON([$data]);
    }
}