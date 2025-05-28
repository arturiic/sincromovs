<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\DetEntidadEmpresaModel;
use App\Models\MovimientosModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use XMLWriter;

class MovimientosController extends Controller
{
    public function index()
    {
        $DetentempModel = new DetEntidadEmpresaModel();
        $data['cuentas'] = $DetentempModel->selectCuentas();
        return view('movimientos/movimientos', $data);
    }

    public function guardarMovimientos()
    {
        $data = $this->request->getJSON(true);
        $movimientos = $data['movimientos'] ?? null;
        $session = session();
        $idempresa = $session->get('codempresa');

        $model = new MovimientosModel();

        $resultados = [
            'registrados' => [],
            'omitidos' => [],
            'errores' => []
        ];

        $todosExitosos = true;
        $algunExitoso = false;

        foreach ($movimientos as $mov) {
            try {
                $resultado = $model->registrarSincroMov(
                    $idempresa,
                    $mov['nombre_depositante'],
                    $mov['observacion'],
                    $mov['fecha_hora'],
                    $mov['monto'],
                    $mov['moneda'],
                    $mov['noperacion']
                );

                if ($resultado['resultado'] === 'OK') {
                    $resultados['registrados'][] = [
                        'noperacion' => $mov['noperacion'],
                        'mensaje' => $resultado['mensaje']
                    ];
                    $algunExitoso = true;
                } elseif ($resultado['resultado'] === 'SKIP') {
                    $resultados['omitidos'][] = [
                        'noperacion' => $mov['noperacion'],
                        'mensaje' => $resultado['mensaje']
                    ];
                    $todosExitosos = false;
                } else {
                    $resultados['errores'][] = [
                        'noperacion' => $mov['noperacion'],
                        'mensaje' => $resultado['mensaje']
                    ];
                    $todosExitosos = false;
                }
            } catch (\Exception $e) {
                $resultados['errores'][] = [
                    'noperacion' => $mov['noperacion'],
                    'mensaje' => $e->getMessage()
                ];
                $todosExitosos = false;
            }
        }

        if ($todosExitosos && count($resultados['registrados']) === count($movimientos)) {
            $status = 'OK';
            $mensajeGeneral = 'Todos los movimientos fueron registrados correctamente';
        } elseif ($algunExitoso) {
            $status = 'PARTIAL';
            $mensajeGeneral = 'Algunos movimientos fueron registrados, otros fueron omitidos o fallaron';
        } else {
            $status = 'ERROR';
            $mensajeGeneral = 'Ningún movimiento fue registrado (todos omitidos o fallaron)';
        }

        return $this->response->setJSON([
            'status' => $status,
            'message' => $mensajeGeneral,
            'details' => $resultados,
            'logs' => [
                'registrados_count' => count($resultados['registrados']),
                'omitidos_count' => count($resultados['omitidos']),
                'errores_count' => count($resultados['errores']),
                'summary' => "Registrados: " . count($resultados['registrados']) . ", Omitidos: " . count($resultados['omitidos']) . ", Errores: " . count($resultados['errores'])
            ]
        ]);
    }

    public function registrarMovimientos()
    {
        $data = [
            'Destinatario' => $this->request->getPost('Destinatario'),
            'Cuenta' => $this->request->getPost('Cuenta'),
            'Observacion' => $this->request->getPost('Observacion'),
            'Fecha' => $this->request->getPost('Fecha'),
            'Monto' => $this->request->getPost('Monto'),
            'Tipo' => $this->request->getPost('Tipo'),
            'Noperacion' => $this->request->getPost('Noperacion')
        ];
        try {
            $MovimientosModel = new MovimientosModel();
            //Generar XML
            $xml = new XMLWriter();
            $xml->openMemory();
            $xml->setIndent(true);
            $xml->startDocument('1.0', 'UTF-8');
            $xml->startElement('Movimiento');
            $xml->startElement('Cabecera');
            foreach ($data as $key => $value) {
                $xml->writeElement($key, $value);
            }
            $xml->endElement();
            $xml->endElement();
            $xml->endDocument();


            // **Llamar al procedimiento almacenado**
            $resultado = $MovimientosModel->registrarMovimientos($xml->outputMemory());

            echo $resultado;
        } catch (\Exception $e) {
            echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function movEntradaXfecha()
    {
        $fechaModel = new MovimientosModel();
        $fecha = $this->request->getGet('fecha');
        $entidademp = $this->request->getGet('entidad');
        $data =  $fechaModel->movEntradaXfecha($fecha, $entidademp);
        return $this->response->setJSON(['data' => $data]);
    }
    public function movSalidaXfecha()
    {
        $fechaModel = new MovimientosModel();
        $fecha = $this->request->getGet('fecha');
        $entidademp = $this->request->getGet('entidad');
        $data =  $fechaModel->movSalidaXfecha($fecha, $entidademp);
        return $this->response->setJSON(['data' => $data]);
    }

    //PARA ELIMINAR REGISTROS
    public function eliminar()
    {
        $cod = $this->request->getPost('idmovfinanzas');
        $modelo = new MovimientosModel();
        $resultado = $modelo->eliminar($cod);

        if ($resultado) {
            return $this->response->setJSON(['success' => true, 'message' => 'Movimiento eliminado correctamente.']);
        } else {
            return $this->response->setJSON(['error' => 'No se encontró el movimiento o ya fue eliminado.']);
        }
    }
    public function reportePDFmovimientos()
    {
        require_once(APPPATH . 'Libraries/fpdf/fpdf.php');

        // Obtener los parámetros
        $inicio = $this->request->getPost('i');
        $fin = $this->request->getPost('f');
        $nombreusuario = session()->get('nombreusuariocorto');
        $nombrempresa = session()->get('nempresa');
        $codempresa = session()->get('codempresa');
        //log_message('error', 'Nombre empresa que se envía al PDF: ' . $nombrempresa);

        $MovimientosModel = new MovimientosModel();
        $movimientos = $MovimientosModel->movimientosXfecha($inicio, $fin, $codempresa);
        $pdf = new class($inicio, $fin, $nombreusuario, $nombrempresa) extends \FPDF {
            private $inicio;
            private $fin;
            private $nombreusuario;
            private $nombrempresa;

            public function __construct($inicio, $fin, $nombreusuario, $nombrempresa)
            {
                parent::__construct();
                $this->inicio = (new \DateTime($inicio))->format('d/m/Y');
                $this->fin = (new \DateTime($fin))->format('d/m/Y');
                $this->nombreusuario = $nombreusuario;
                $this->nombrempresa = $nombrempresa;
            }
            // Encabezado
            function Header()
            {
                $this->SetFont('Arial', '', 8);
                $this->Cell(0, 4, 'Fecha: ' . date("d/m/Y"), 0, 1, 'R');
                $this->Cell(0, 4, 'Usuario: ' . $this->nombreusuario, 0, 1, 'R');

                $this->SetFont('Arial', 'B', 9);
                $this->Cell(0, 4, 'REPORTE DE MOVIMIENTOS ' . ' DEL: ' . $this->inicio . ' AL: ' . $this->fin, 0, 1, 'C');
                $this->Cell(0, 4, 'EMPRESA:' . $this->nombrempresa, 0, 1, 'L', 0, '', 0);
                $this->SetFont('Arial', '', 7);
                $this->Ln(2); // Salto de línea
                $this->Line(10, $this->GetY(), 285, $this->GetY()); // Ajusta las coordenadas según sea necesario

            }
            // Pie de página
            function Footer()
            {
                $this->SetY(-15); // Posición a 1.5 cm del final
                $this->SetFont('Arial', 'B', 8);
                $this->Cell(0, 8, $this->PageNo() . '/{nb}', 0, 0, 'R'); // Número de página
            }
            var $widths;
            var $aligns;
            function SetWidths($w)
            {
                //Set the array of column widths
                $this->widths = $w;
            }
            function SetAligns($a)
            {
                //Set the array of column alignments
                $this->aligns = $a;
            }
            function Row($data)
            {
                //Calculate the height of the row
                $nb = 0;
                for ($i = 0; $i < count($data); $i++)
                    $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
                $h = 3 * $nb;
                //Issue a page break first if needed
                $this->CheckPageBreak($h);
                //Draw the cells of the row
                for ($i = 0; $i < count($data); $i++) {
                    $w = $this->widths[$i];
                    $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
                    //Save the current position
                    $x = $this->GetX();
                    $y = $this->GetY();
                    //Draw the border
                    $this->Rect($x, $y, $w, $h);
                    //Print the text
                    $this->MultiCell($w, 3, $data[$i], 0, $a);
                    //Put the position to the right of the cell
                    $this->SetXY($x + $w, $y);
                }
                //Go to the next line
                $this->Ln($h);
            }

            function CheckPageBreak($h)
            {
                //If the height h would cause an overflow, add a new page immediately
                if ($this->GetY() + $h > $this->PageBreakTrigger)
                    $this->AddPage($this->CurOrientation);
            }

            function NbLines($w, $txt)
            {
                //Computes the number of lines a MultiCell of width w will take
                $cw = &$this->CurrentFont['cw'];
                if ($w == 0)
                    $w = $this->w - $this->rMargin - $this->x;
                $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
                $s = str_replace("\r", '', $txt);
                $nb = strlen($s);
                if ($nb > 0 and $s[$nb - 1] == "\n")
                    $nb--;
                $sep = -1;
                $i = 0;
                $j = 0;
                $l = 0;
                $nl = 1;
                while ($i < $nb) {
                    $c = $s[$i];
                    if ($c == "\n") {
                        $i++;
                        $sep = -1;
                        $j = $i;
                        $l = 0;
                        $nl++;
                        continue;
                    }
                    if ($c == ' ')
                        $sep = $i;
                    $l += $cw[$c];
                    if ($l > $wmax) {
                        if ($sep == -1) {
                            if ($i == $j)
                                $i++;
                        } else
                            $i = $sep + 1;
                        $sep = -1;
                        $j = $i;
                        $l = 0;
                        $nl++;
                    } else
                        $i++;
                }
                return $nl;
            }
        };
        // Generar el contenido del PDF
        $pdf->AddPage('L');
        $pdf->AliasNbPages();
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->Ln();

        // COLORES
        $pdf->SetFillColor(200, 220, 255); // color de fondo celeste claro
        $pdf->SetTextColor(0, 0, 0); // texto negro
        $pdf->SetFont('Helvetica', 'B', 7);

        //ENCABEZADOS (agregamos la columna SALDO)
        $pdf->Cell(15, 6, 'FECHA', 1, 0, 'C', true);
        $pdf->Cell(60, 6, 'DESTINATARIO', 1, 0, 'C', true);
        $pdf->Cell(65, 6, 'CUENTA', 1, 0, 'C', true);
        $pdf->Cell(55, 6, 'OBSERVACION', 1, 0, 'C', true);
        $pdf->Cell(23, 6, 'NRO. OPERACION', 1, 0, 'C', true);
        $pdf->Cell(19, 6, 'ENTRADA', 1, 0, 'C', true);
        $pdf->Cell(19, 6, 'SALIDA', 1, 0, 'C', true);
        $pdf->Cell(19, 6, 'SALDO', 1, 1, 'C', true);

        // Inicializar acumuladores
        $entra = 0;
        $salid = 0;
        $saldo_acumulado = 0; // Nuevo acumulador para el saldo

        foreach ($movimientos as $movimiento) {
            $pdf->SetFont('Helvetica', '', 7);

            // Comprobar si es tipo 'SALDO' para poner fondo amarillo
            if ($movimiento['tipo'] == 'SALDO') {
                $pdf->SetFillColor(255, 249, 196); // color de fondo amarillo
            } else {
                $pdf->SetFillColor(255, 255, 255); // fondo blanco para otros tipos
            }

            // Actualizar saldo acumulado (entradas suman, salidas restan)
            $saldo_acumulado += $movimiento['entrada'] - $movimiento['salida'];

            $fecha_formateada = date('d-m-Y', strtotime($movimiento['fecha']));

            $pdf->Cell(15, 4, $fecha_formateada, 1, 0, 'C', true);
            $pdf->Cell(60, 4, $movimiento['destinatario'], 1, 0, 'C', true);
            $pdf->Cell(65, 4, $movimiento['cuenta'], 1, 0, 'C', true);
            $pdf->Cell(55, 4, $movimiento['observacion'], 1, 0, 'C', true);
            $pdf->Cell(23, 4, $movimiento['noperacion'], 1, 0, 'C', true);
            $pdf->Cell(19, 4, $movimiento['entrada'], 1, 0, 'R', true);
            $pdf->Cell(19, 4, $movimiento['salida'], 1, 0, 'R', true);
            $pdf->Cell(19, 4, number_format($saldo_acumulado, 2), 1, 1, 'R', true);

            // Acumular totales
            $entra += $movimiento['entrada'];
            $salid += $movimiento['salida'];
        }

        $total = $entra - $salid;
        $pdf->SetFillColor(200, 220, 255);
        $pdf->SetFont('Helvetica', 'B', 7);
        $pdf->Cell(218, 4, '', 0, 0, 'R'); 
        $pdf->Cell(19, 4, number_format($entra, 2), 1, 0, 'R');
        $pdf->Cell(19, 4, number_format($salid, 2), 1, 0, 'R');
        $pdf->Cell(19, 4, number_format($total, 2), 1, 1, 'R'); // Mostrar el total en la columna SALDO

        $pdf->Cell(218, 4, 'TOTAL', 0, 0, 'R');
        $pdf->Cell(57, 4, number_format($total, 2), 1, 0, 'C', true); // Ajustado para 3 celdas

        // Establecer los encabezados para la respuesta PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="reporte.pdf"');
        // Generar el PDF y enviarlo al navegador
        $pdf->Output('I', 'reporte.pdf');
        exit; // Termina el script para evitar contenido adicional en la respuesta
    }

    public function registrarSaldo()
    {
        $data = [
            'Observacion' => $this->request->getPost('Observacion'),
            'Fecha' => $this->request->getPost('Fecha'),
            'Saldo' => $this->request->getPost('Saldo'),
            'Cuenta' => $this->request->getPost('Cuenta'),
            'Tipo' => $this->request->getPost('Tipo'),
            'Noperacion' => $this->request->getPost('Noperacion')
        ];
        try {
            $MovimientosModel = new MovimientosModel();
            //Generar XML
            $xml = new XMLWriter();
            $xml->openMemory();
            $xml->setIndent(true);
            $xml->startDocument('1.0', 'UTF-8');
            $xml->startElement('Movimiento');
            $xml->startElement('Cabecera');
            foreach ($data as $key => $value) {
                $xml->writeElement($key, $value);
            }
            $xml->endElement();
            $xml->endElement();
            $xml->endDocument();

            // **Llamar al procedimiento almacenado**
            $resultado = $MovimientosModel->registrarSaldo($xml->outputMemory());

            echo $resultado;
        } catch (\Exception $e) {
            echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
        }
    }
    public function ver_movimientos_sincro()
    {
        // Obtener fechas por POST y empresa de la sesión
        $fini = $this->request->getPost('fini');
        $ffin = $this->request->getPost('ffin');
        $codempresa = session('codempresa'); // Obtener directamente de la sesión

        try {
            $model = new MovimientosModel();
            $movimientos = $model->ver_movimientos_sincro($fini, $ffin, $codempresa);

            return $this->response->setJSON([$movimientos]);
        } catch (\Exception $e) {
            //log_message('error', 'Error en ver_movimientos_sincro: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => true,
                'message' => 'Error al generar el reporte'
            ]);
        }
    }

    public function reporteExcelMovimientos()
    {
        $inicio = $this->request->getPost('i');
        $fin = $this->request->getPost('f');
        $codempresa = session()->get('codempresa');
        $nombreusuario = session()->get('nombreusuariocorto');
        $nombrempresa = session()->get('nempresa');

        $MovimientosModel = new MovimientosModel();
        $movimientos = $MovimientosModel->movimientosXfecha($inicio, $fin, $codempresa);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Estilos
        $styleTitle = [
            'font' => ['bold' => true, 'size' => 13],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];

        $styleHeader = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'BDD7EE'] // Azul claro
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ];

        $styleTotal = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9E1F2']
            ]
        ];
        $styleSaldo = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFACD'], // amarillo
            ]
        ];

        // Título principal centrado
        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'REPORTE DE MOVIMIENTOS DEL: ' . date('d/m/Y', strtotime($inicio)) . ' AL: ' . date('d/m/Y', strtotime($fin)));
        $sheet->getStyle('A1')->applyFromArray($styleTitle);

        // Empresa y usuario
        $sheet->mergeCells('A2:C2');
        $sheet->setCellValue('A2', 'EMPRESA: ' . $nombrempresa);
        $sheet->setCellValue('H2', 'Fecha: ' . date('d/m/Y'));
        $sheet->setCellValue('H3', 'Usuario: ' . $nombreusuario);

        // Encabezados
        $sheet->setCellValue('A5', 'FECHA');
        $sheet->setCellValue('B5', 'DESTINATARIO');
        $sheet->setCellValue('C5', 'CUENTA');
        $sheet->setCellValue('D5', 'OBSERVACION');
        $sheet->setCellValue('E5', 'NRO. OPERACION');
        $sheet->setCellValue('F5', 'ENTRADA');
        $sheet->setCellValue('G5', 'SALIDA');
        $sheet->setCellValue('H5', 'SALDO');
        $sheet->getStyle('A5:H5')->applyFromArray($styleHeader);

        // Datos
        $row = 6;
        $totalEntrada = 0;
        $totalSalida = 0;
        $saldoAcumulado = 0;

        foreach ($movimientos as $mov) {
            $saldoAcumulado += $mov['entrada'] - $mov['salida'];

            $sheet->setCellValue('A' . $row, $mov['fecha']);
            $sheet->setCellValue('B' . $row, $mov['destinatario']);
            $sheet->setCellValue('C' . $row, $mov['cuenta']);
            $sheet->setCellValue('D' . $row, $mov['observacion']);
            $sheet->setCellValue('E' . $row, $mov['noperacion']);
            $sheet->setCellValue('F' . $row, $mov['entrada']);
            $sheet->setCellValue('G' . $row, $mov['salida']);
            $sheet->setCellValue('H' . $row, $saldoAcumulado);

            // Aplicar color de fondo amarillo si el tipo es SALDO
            if ($mov['tipo'] === 'SALDO') {
                $sheet->getStyle("A$row:H$row")->applyFromArray($styleSaldo);
            }

            // Bordes para cada fila
            $sheet->getStyle("A{$row}:H{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            $totalEntrada += $mov['entrada'];
            $totalSalida += $mov['salida'];
            $row++;
        }

        // Totales
        $totalGeneral = $totalEntrada - $totalSalida;
        $sheet->setCellValue('E' . $row, 'TOTAL');
        $sheet->setCellValue('F' . $row, $totalEntrada);
        $sheet->setCellValue('G' . $row, $totalSalida);
        $sheet->setCellValue('H' . $row, $totalGeneral);
        $sheet->getStyle("E{$row}:H{$row}")->applyFromArray($styleTotal);

        // Formato numérico
        $sheet->getStyle("F6:F{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("G6:G{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("H6:H{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

        // Ajustar ancho
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Salida
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte_movimientos.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function movimientosXcod()
    {
        $movModel = new MovimientosModel();
        $cod = $this->request->getGet('cod');
        $data = $movModel->movimientosXcod($cod);
        return $this->response->setJSON([$data]);
    }

    public function update()
    {
        $model = new MovimientosModel();
        $idmov_finanzas = $this->request->getPost('cod'); // Obtener ID del movimiento a actualizar
        $observacion = $this->request->getPost('observacion');

        $data = [
            'observacion' => $observacion
        ];

        try {
            // Llama al método de actualización
            if ($model->update($idmov_finanzas, $data)) {
                return $this->response->setJSON(['success' => true, 'message' => 'Movimiento actualizado.']);
            } else {
                return $this->response->setJSON(['error' => 'Movimientos no encontrado.']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Ocurrió un error al actualizar el movimiento: ' . $e->getMessage()]);
        }
    }
}
