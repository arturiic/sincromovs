<?php

namespace App\Models;

use CodeIgniter\Model;

class MovimientosModel extends Model
{
    protected $table      = 'mov_finanzas';
    protected $primaryKey = 'idmov_finanzas';
    protected $allowedFields = [
        'idmov_finanzas',
        'iddestinatario',
        'iddet_entidad_empresa',
        'nombre_depositante',
        'observacion',
        'fecha',
        'monto',
        'tipo',
        'noperacion',
        'enviado_a'
    ];

    public function registrarSincroMov($idempresa, $titulo, $enviado_a, $fecha_hora, $monto, $moneda, $noperacion)
{
    $sql = "CALL REGISTRAR_SINCROMOV(?, ?, ?, ?, ?, ?, ?)";
    $query = $this->db->query($sql, [
        $idempresa,
        $titulo,
        $enviado_a,
        $fecha_hora,
        $monto,
        $moneda,
        $noperacion
    ]);
    
    // Obtener el resultado completo (resultado y mensaje)
    $result = $query->getRow();
    
    // Liberar el resultset
    $query->freeResult();
    
    if ($result) {
        return [
            'resultado' => $result->resultado ?? 'ERROR',
            'mensaje' => $result->mensaje ?? 'Error desconocido',
            'noperacion' => $noperacion
        ];
    }
    
    return [
        'resultado' => 'ERROR',
        'mensaje' => 'No se recibió respuesta del procedimiento almacenado',
        'noperacion' => $noperacion
    ];
}
    //PARA REGISTRAR MOVS CON SP
    public function registrarMovimientos($xmlContent)
    {
        //log_message('error', 'XML DE MOVIMIENTOS:' . $xmlContent);
        try {

            $sql = 'CALL REG_MOV_FINANZAS(?,@mensaje)';
            $this->db->query($sql, [$xmlContent]);

            $result = $this->db->query("SELECT @mensaje as mensaje");
            $mensaje = $result->getRow()->mensaje;

            if (strpos($mensaje, 'ERROR:') !== false) {
                return $mensaje;
            }
            return $mensaje;
        } catch (\mysqli_sql_exception $e) {
            //log_message('error', 'Error al registrar al movimiento:' . $e->getMessage());
            return 'Error:' . $e->getMessage();
        } catch (\Exception $e) {
            //log_message('error', 'Error generico:' . $e->getMessage());
            return 'Error:' . $e->getMessage();
        }
    }

    //PARA LA TABLA MOVIMIENTOS POR FECHA Y CUENTA
    public function movEntradaXfecha($fecha, $entidademp)
    {
        return $this->select('idmov_finanzas,
                            IFNULL(NULLIF(dest.nombre, ""), mov_finanzas.enviado_a) as destinatario,
                            IFNULL(observacion, "") as observacion,
                            fecha,
                            monto,
                            tipo,
                            IFNULL(NULLIF(noperacion, ""), "") as noperacion')
            ->join('destinatario dest', 'mov_finanzas.iddestinatario = dest.iddestinatario', 'left')
            ->whereIn('tipo', ['ENTRADA', 'SALDO'])
            ->where('fecha', $fecha)
            ->where('iddet_entidad_empresa', $entidademp)
            ->findAll();
    }

    public function movSalidaXfecha($fecha, $entidademp)
    {
        return $this->select('idmov_finanzas,
                            IFNULL(NULLIF(dest.nombre, ""), mov_finanzas.enviado_a) as destinatario,
                            IFNULL(observacion, "") as observacion,
                            fecha,
                            monto,
                            tipo,
                            IFNULL(NULLIF(noperacion, ""), "") as noperacion')
            ->join('destinatario dest', 'mov_finanzas.iddestinatario = dest.iddestinatario', 'left')
            ->where('tipo', 'SALIDA')
            ->where('fecha', $fecha)
            ->where('iddet_entidad_empresa', $entidademp)
            ->findAll();
    }
    public function eliminar($cod)
    {
        $movimientos = $this->find($cod);
        if (!$movimientos) {
            return false; // No existe el movimientos
        }

        return $this->where('idmov_finanzas', $cod)->delete();
    }
    //PARA VER MOVIMIENTOS POR PDF
    public function movimientosXfecha($fini, $ffin, $codempresa)
    {
        $sql = 'CALL VER_MOVIMIENTOS_PDF(?, ?, ?)';
        $query = $this->db->query($sql, [

            $fini,
            $ffin,
            $codempresa,
        ]);
        return $query->getResultArray(); // Retorna los resultados como un array
    }
    public function registrarSaldo($xmlContent)
    {
        //log_message('error', 'XML DE MOVIMIENTOS:' . $xmlContent);
        try {
            $sql = 'CALL REG_SALDO(?,@mensaje)';
            $this->db->query($sql, [$xmlContent]);

            $result = $this->db->query("SELECT @mensaje as mensaje");
            $mensaje = $result->getRow()->mensaje;

            if (strpos($mensaje, 'ERROR:') !== false) {
                return $mensaje;
            }
            return $mensaje;
        } catch (\mysqli_sql_exception $e) {
            //log_message('error', 'Error al registrar al movimiento:' . $e->getMessage());
            return 'Error:' . $e->getMessage();
        } catch (\Exception $e) {
            return 'Error:' . $e->getMessage();
        }
    }
    public function movimientosXcod($cod)
{
    return $this->select('mov_finanzas.idmov_finanzas,
                        mov_finanzas.iddestinatario,
                        mov_finanzas.iddet_entidad_empresa,
                        mov_finanzas.nombre_depositante,
                        mov_finanzas.observacion,
                        mov_finanzas.fecha,
                        mov_finanzas.monto,
                        mov_finanzas.tipo,
                        mov_finanzas.noperacion,
                        IFNULL(NULLIF(dest.nombre, ""), mov_finanzas.enviado_a) as enviado_a')
        ->join('destinatario dest', 'mov_finanzas.iddestinatario = dest.iddestinatario', 'left')
        ->where('mov_finanzas.idmov_finanzas', $cod)
        ->first();
}
}
