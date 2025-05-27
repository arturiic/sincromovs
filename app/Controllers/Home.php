<?php

namespace App\Controllers;

use App\Models\MovimientosModel;

class Home extends BaseController
{
    public function index()
    {
        $idEmpresa = session()->get('codempresa');

        $movModel = new MovimientosModel();
        $datosSalidas = $movModel->obtenerTotalesPorMes($idEmpresa);
        $datosEntradas = $movModel->obtenerTotalesEntradasPorMes($idEmpresa);

        $nombresMeses = [
            1 => 'Ene',2 => 'Feb',3 => 'Mar',4 => 'Abr',5 => 'May',6 => 'Jun',
            7 => 'Jul',8 => 'Ago',9 => 'Sep',10 => 'Oct',11 => 'Nov',12 => 'Dic'
        ];

        //SALIDAS
        $totalesSalidas = array_fill(0, 12, 0);
        $coloresSalidas = array_fill(0, 12, 'rgba(220, 53, 69, 0.6)');

        //ENTRADAS
        $totalesEntradas = array_fill(0, 12, 0);
        $coloresEntradas = array_fill(0, 12, 'rgba(0, 123, 255, 0.6)');

        // Procesar datos de SALIDAS
        foreach ($datosSalidas as $row) {
            $mesNumero = (int)$row['mes'];
            if ($mesNumero >= 1 && $mesNumero <= 12) {
                $totalesSalidas[$mesNumero - 1] = (float)$row['total'];
            }
        }

        // Procesar datos de ENTRADAS
        foreach ($datosEntradas as $row) {
            $mesNumero = (int)$row['mes'];
            if ($mesNumero >= 1 && $mesNumero <= 12) {
                $totalesEntradas[$mesNumero - 1] = (float)$row['total'];
            }
        }

        //TOTALES
        $totalSalidas = array_sum($totalesSalidas);
        $totalEntradas = array_sum($totalesEntradas);

        return view('dashboard/index', [
            'meses' => $nombresMeses,
            'totalesSalidas' => $totalesSalidas,
            'totalesEntradas' => $totalesEntradas,
            'totalSalidas' => $totalSalidas,
            'totalEntradas' => $totalEntradas,
            'coloresSalidas' => $coloresSalidas,
            'coloresEntradas' => $coloresEntradas
        ]);
    }
}
