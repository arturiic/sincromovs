<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class SincroMovimientosController extends Controller
{
    public function index()
    {
        return view('configuracion/sincromovimientos');
    }
    public function sincronizacionMovimientos()
    {
        // Aumentar el tiempo de ejecución para operaciones IMAP
        set_time_limit(300); // 5 minutos

        // VARIABLE NOMBRE DE LA EMPRESA
        $nempresa = session('nempresa');

        // Configuración del servidor IMAP
        $hostname = '{mail.grupoasiu.com:993/imap/ssl}INBOX';
        $username = 'yulyasiu@grupoasiu.com';
        $password = 'margot2405';
        $remitente_buscado = 'notificaciones@notificacionesbcp.com.pe';

        // Obtener fechas desde la solicitud
        $fecha_inicio = $this->request->getGet('desde');
        $fecha_fin = $this->request->getGet('hasta') ?: $fecha_inicio;

        // Conectar al servidor IMAP con manejo de errores
        try {
            $inbox = imap_open($hostname, $username, $password);
            if (!$inbox) {
                throw new \Exception('No se pudo conectar: ' . imap_last_error());
            }
        } catch (\Exception $e) {
            die(json_encode(['error' => $e->getMessage()]));
        }

        // Procesamiento principal
        try {
            $data = [];
            // Criterio de búsqueda para un rango de fechas
            $criterio = 'FROM "' . $remitente_buscado . '" SINCE "' . $fecha_inicio . '" BEFORE "' . date('Y-m-d', strtotime($fecha_fin . ' +1 day')) . '"';

            // Buscar en bandeja de entrada
            $entrada = buscarMensajes($inbox, $criterio);
            foreach ($entrada as $email_number) {
                $mensaje = obtenerDatosExtraidos($inbox, $email_number, 'Bandeja de entrada');
                // Filtrar por nombre base de empresa si el mensaje es válido
                if ($mensaje && $this->validarEmpresaPorTitulo($mensaje['titulo'], $nempresa)) {
                    $data[] = $mensaje;
                }
            }

            // Buscar en posibles carpetas de spam
            $spam_folders = ['spam'];
            foreach ($spam_folders as $folder) {
                $spam_folder = str_replace('INBOX', $folder, $hostname);
                if (@imap_reopen($inbox, $spam_folder)) {
                    $spam = buscarMensajes($inbox, $criterio);
                    foreach ($spam as $email_number) {
                        $mensaje = obtenerDatosExtraidos($inbox, $email_number, $folder);
                        // Filtrar por nombre base de empresa si el mensaje es válido
                        if ($mensaje && stripos($mensaje['titulo'], $nempresa) !== false) {
                            $data[] = $mensaje;
                        }
                    }
                    imap_reopen($inbox, $hostname);
                }
            }

            // Cerrar conexión
            imap_close($inbox);

            array_walk($data, function (&$item) {
                foreach ($item as $key => $value) {
                    if (is_string($value)) {
                        // Eliminar caracteres no UTF-8
                        $item[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                        // Opcional: eliminar caracteres especiales problemáticos
                        $item[$key] = preg_replace('/[^\x20-\x7E]/u', '', $item[$key]);
                    }
                }
            });

            // Forzar la codificación UTF-8 en la respuesta
            return $this->response
                ->setContentType('application/json; charset=UTF-8')
                ->setJSON([
                    'data' => $data,
                    //'fecha_inicio' => $fecha_inicio,
                    //'fecha_fin' => $fecha_fin,
                    //'cantidad' => count($data)
                ]);
        } catch (\Exception $e) {
            return $this->response
                ->setContentType('application/json; charset=UTF-8')
                ->setJSON([
                    'error' => $e->getMessage()
                ]);
        }
    }

    public function validarEmpresaPorTitulo($titulo, $nombreEmpresa)
    {
        // Normalizar strings: eliminar caracteres especiales, espacios extras y convertir a minúsculas
        $tituloLimpio = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower(trim($titulo)));
        $nombreLimpio = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower(trim($nombreEmpresa)));
        // Dividir el nombre de la empresa en partes
        $partesNombre = preg_split('/\s+/', $nombreLimpio);
        // Mapeo de variantes comunes de nombres
        $variantesNombres = [
            'magaly' => ['magali', 'magaly', 'magalí'],
            'zoila' => ['zoila', 'zoyla'],
            'fox' => ['fox', 'foox', 'foks'],
            // Puedes agregar más variantes según necesites
        ];
        //Coincidencia exacta del nombre completo
        if (strpos($tituloLimpio, $nombreLimpio) !== false) {
            return true;
        }
        //Coincidencia con variantes del nombre completo
        foreach ($variantesNombres as $nombreBase => $variantes) {
            foreach ($variantes as $variante) {
                $nombreVariante = str_replace($nombreBase, $variante, $nombreLimpio);
                if (strpos($tituloLimpio, $nombreVariante) !== false) {
                    return true;
                }
            }
        }
        //Coincidencia con todas las palabras importantes (ignorando "SAC", "S.A.C", etc.)
        $palabrasImportantes = array_filter($partesNombre, function ($palabra) {
            return !in_array($palabra, ['sac', 's.a.c', 's.a', 'e.i.r.l', 'ltda', 'srl']);
        });
        // Verificar cada palabra importante y sus posibles variantes
        $todasCoinciden = true;
        foreach ($palabrasImportantes as $palabra) {
            $coincide = false;
            // Verificar la palabra original
            if (strpos($tituloLimpio, $palabra) !== false) {
                $coincide = true;
            }
            // Verificar variantes si no coincide la original
            if (!$coincide && isset($variantesNombres[$palabra])) {
                foreach ($variantesNombres[$palabra] as $variante) {
                    if (strpos($tituloLimpio, $variante) !== false) {
                        $coincide = true;
                        break;
                    }
                }
            }
            if (!$coincide) {
                $todasCoinciden = false;
                break;
            }
        }
        if ($todasCoinciden && !empty($palabrasImportantes)) {
            return true;
        }
        //Coincidencia con la primera palabra (y sus variantes)
        if (!empty($partesNombre[0])) {
            $primerPalabra = $partesNombre[0];

            // Verificar palabra original
            if (strpos($tituloLimpio, $primerPalabra) !== false) {
                return true;
            }
            // Verificar variantes
            if (isset($variantesNombres[$primerPalabra])) {
                foreach ($variantesNombres[$primerPalabra] as $variante) {
                    if (strpos($tituloLimpio, $variante) !== false) {
                        return true;
                    }
                }
            }
        }
        // Si no pasa ninguna validación
        return false;
    }
}
//FUNCION SOLO PARA MOSTRAR EXTRAIDOS
function obtenerDatosExtraidos($inbox, $email_number)
{
    $estructura = @imap_fetchstructure($inbox, $email_number);
    $cuerpo = '';

    if ($estructura) {
        $cuerpo = obtenerCuerpoMensaje($inbox, $email_number, $estructura);
    }

    return extraerDatosHtml($cuerpo);
}

//FUNCIONES FUERA
//FUNCION 1 Obtiene todos los datos de un mensaje de forma segura
function obtenerDatosMensaje($inbox, $email_number, $carpeta)
{
    $header = @imap_headerinfo($inbox, $email_number);
    if (!$header) return null;

    $datos = [
        //'id' => $email_number,
        //'carpeta' => $carpeta,
        'asunto' => isset($header->subject) ? imap_utf8($header->subject) : 'Sin asunto',
        'de' => isset($header->from[0]->mailbox) ?
            imap_utf8($header->from[0]->mailbox . '@' . $header->from[0]->host) : 'Desconocido',
        //'nombre_remitente' => isset($header->from[0]->personal) ?
        //imap_utf8($header->from[0]->personal) : '',
        //'para' => isset($header->toaddress) ? imap_utf8($header->toaddress) : '',
        'fecha' => isset($header->date) ? $header->date : '',
        //'fecha_utc' => isset($header->udate) ? date('Y-m-d H:i:s', $header->udate) : '',
    ];

    $estructura = @imap_fetchstructure($inbox, $email_number);
    if ($estructura) {
        $datos['cuerpo'] = obtenerCuerpoMensaje($inbox, $email_number, $estructura);
    } else {
        $datos['cuerpo'] = '';
    }
    $datos['extraidos'] = extraerDatosHtml($datos['cuerpo']);

    return $datos;
}

//FUNCION 2 Obtiene el cuerpo del mensaje con manejo seguro
function obtenerCuerpoMensaje($inbox, $email_number, $estructura)
{
    $cuerpo = '';

    try {
        if ($estructura->type == 0) {
            $cuerpo = imap_fetchbody($inbox, $email_number, 1);

            if ($estructura->encoding == 3) {
                $cuerpo = base64_decode($cuerpo);
            } elseif ($estructura->encoding == 4) {
                $cuerpo = quoted_printable_decode($cuerpo);
            }
        } elseif ($estructura->type == 1 && isset($estructura->parts)) {
            foreach ($estructura->parts as $part_number => $part) {
                if ($part->type == 0 && strtolower($part->subtype) == 'plain') {
                    $part_id = $part_number + 1;
                    $cuerpo = imap_fetchbody($inbox, $email_number, $part_id);

                    if ($part->encoding == 3) {
                        $cuerpo = base64_decode($cuerpo);
                    } elseif ($part->encoding == 4) {
                        $cuerpo = quoted_printable_decode($cuerpo);
                    }
                    break;
                }
            }
        }
    } catch (\Exception $e) {
        error_log("Error obteniendo cuerpo del mensaje: " . $e->getMessage());
    }

    return trim($cuerpo);
}

//FUNCION 3 Busca mensajes con manejo de errores
function buscarMensajes($inbox, $criterio)
{
    $resultados = @imap_search($inbox, $criterio);
    return is_array($resultados) ? $resultados : [];
}

function extraerDatosHtml($cuerpo, $asunto = null)
{
    // --- Validación 1: Cuerpo vacío ---
    if (empty(trim($cuerpo))) {
        return null;
    }
    // --- Validación 2: Movimientos no relevantes (ej: códigos de verificación) ---
    if ($asunto && preg_match('/WebMiNegocioBCP\s*-\s*Código de verificación/i', $asunto)) {
        return null;
    }

    $datos = [
        'titulo' => null,
        'monto' => null,
        'enviado_a' => null,
        'fecha_y_hora' => null,
        'moneda' => null,
        'noperacion' => '-',
    ];
    // === TÍTULO ===
    if (preg_match('/Hola\s*<b>([^<]+),<\/b>/i', $cuerpo, $m) && isset($m[1])) {
        $titulo = trim($m[1]);
        if (preg_match('/Operaci[^\w]*n realizada<\/td>\s*<td[^>]*>\s*<b>([^<]+)<\/b>/i', $cuerpo, $op) && isset($op[1])) {
            $operacion = trim($op[1]);
        } else {
            $operacion = 'Devolución';
        }
        $datos['titulo'] = "$titulo - $operacion";
    } elseif (preg_match('/^\*Hola\s([^\*,]+),/i', $cuerpo, $m) && isset($m[1])) {
        $titulo = trim($m[1]);
        $operacion = (preg_match('/Operaci[^\w]*n realizada:\s*\*([^\*]+)\*/i', $cuerpo, $op) && isset($op[1])) ? trim($op[1]) : '';
        $datos['titulo'] = $titulo . ($operacion ? " - $operacion" : '');
    } elseif (preg_match('/Hola\s+\*([^\*]+),\*/', $cuerpo, $m) && isset($m[1])) {
        $titulo = trim($m[1]);
        $operacion = (preg_match('/Operaci[^\w]*n realizada \*([^\*]+)\*/i', $cuerpo, $op) && isset($op[1])) ? trim($op[1]) : '';
        $datos['titulo'] = $titulo . ($operacion ? " - $operacion" : '');
    } elseif (preg_match('/Estimado\s*<b>([^<]+),<\/b>/i', $cuerpo, $m)) {
        $titulo = trim($m[1]);
        $operacion = '';
        // Intentar patrón 1: "Operación realizada"
        if (preg_match('/Operaci[^\w]*n realizada<\/td>\s*<td[^>]*>\s*<b>([^<]+)<\/b>/i', $cuerpo, $op) && isset($op[1])) {
            $operacion = trim($op[1]);
        } elseif (preg_match('/Tipo de operaci(?:ó|o)n.*?<\/td>\s*<td[^>]*>\s*(.*?)\s*<\/td>/is', $cuerpo, $match)) {
            $html = $match[1];
            $operacion = trim(strip_tags(str_replace('<br />', ' ', $html)));
        } else {
            $operacion = '';
        }
        $datos['titulo'] = $titulo . ($operacion ? " - $operacion" : '');
    } elseif (preg_match('/preview:\s*Hola\s+([^,]+),/i', $cuerpo, $m)) {
        // Nueva condición para el formato "preview: Hola Nombre,"
        $titulo = trim($m[1]);
        $operacion = 'Realizaste un Consumo'; // Puedes ajustar esto según necesites
        $datos['titulo'] = "$titulo - $operacion";
    } elseif (preg_match('/Empresa ordenante \*([^*]+)\*/i', $cuerpo, $m)) {
        $titulo = trim($m[1]);
        $operacion = 'Transferencia Telecrédito'; // Puedes ajustar esto según necesites
        $datos['titulo'] = "$titulo - $operacion";
    } elseif (preg_match('/Estimados\s*<b>([^<]+),<\/b>/i', $cuerpo, $m) && isset($m[1])) {
        $titulo = trim($m[1]);
        if (preg_match('/Operaci[^\w]*n realizada\s*<\/td>\s*<td[^>]*>\s*<b>([^<]+)<\/b>/i', $cuerpo, $op) && isset($op[1])) {
            $operacion = trim($op[1]);
        }
        $datos['titulo'] = "$titulo - $operacion";
    } elseif (preg_match('/\*\s*([^*]+?)\s*\*/', $cuerpo, $m) && isset($m[1])) {
        $titulo = trim($m[1]);
        $empresa = (preg_match('/Empresa ordenante\s+([^\n\r<]+)/i', $cuerpo, $e) && isset($e[1])) ? trim($e[1]) : '';
        $datos['titulo'] = $empresa ? "$empresa - $titulo" : $titulo;
    }
    // === MONTO y MONEDA ===
    $montoRegExps = [
        '/Monto\s*<\/td>\s*<td[^>]*>\s*<b>\$\s*([\d,]+\.\d{2})\s*<\/b>/i',
        '/Monto<\/td>\s*<td[^>]*><b>S\/\s*([\d,]+\.\d{2})<\/b>/i',
        '/(?:Total del consumo|Total devuelto|Total retirado)<\/td>\s*<td[^>]*>\s*<b>(S\/|\$)\s([\d,\.]+)<\/b>/i',
        '/Monto (?:total|transferido)[^$\/]*([\$S]\/)\s*([\d,\.]+)/i',
        '/Monto enviado\s*\*(S\/|\$)\s*([\d.,]+)\*/i',
        '/Monto\s*(?:[\n\r]+)\s*(S\/|\$)\s*([\d\.,]+)/i',
        '/Monto<\/td>\s*<td[^>]*><b>(S\/|$)?\s*([\d,]+\.\d{2})<\/b>/i',
        '/Monto enviado\s*<\/td>\s*<td[^>]*>\s*<b>(S\/|\$)\s*([\d,]+\.\d{2})<\/b>/i',
        '/Monto pagado<\/td>\s*<td[^>]*>\s*<b>(S\/)\s*([\d,]+\.\d{2})<\/b>/i',
        '/Monto transferido\s*<\/td>\s*<td[^>]*>\s*<b>(S\/|\$)\s*([\d,]+\.\d{2})<\/b>/i',
        '/Monto pagado\s*<\/td>\s*<td[^>]*>\s*<b>\$\s*([\d,]+\.\d{2})\s*<\/b>/i',
        '/consumo de (S\/|\$)\s*([\d,]+\.\d{2})/i',
        '/Monto\*[^*]*\*(S\/)\s*([\d,]+\.\d{2})\*/i',
        '/Total abonado<\/td>\s*<td[^>]*>\s*<b>(S\/)\s*([\d,]+\.\d{2})<\/b>/i'
    ];
    foreach ($montoRegExps as $regex) {
        if (preg_match($regex, $cuerpo, $matches)) {
            // Algunas regex capturan en $matches[1], otras en $matches[2]
            $monto = isset($matches[2]) ? $matches[2] : $matches[1];
            $simboloMoneda = isset($matches[1]) && strpos($matches[1], '$') !== false ? '$' : (isset($matches[1]) ? $matches[1] : '');
            $datos['monto'] = str_replace(',', '', trim($monto));
            $datos['moneda'] = (stripos($simboloMoneda, 'S') !== false || $simboloMoneda === 'S/') ? 'SOLES' : 'DOLARES';
            break;
        }
    }
    // === ENVIADO A ===
    $enviadoRegExps = [
        '/Beneficiario[:\*\s]+([^\n<*]+)/i',
        '/(?:Empresa|Nombre del Comercio)<\/td>\s*<td[^>]*>\s*<b>([^<]+)<\/b>/i',
        '/Empresa:\s*\*([^\*]+)\*/i',
        '/Enviado a\s\*([^\*]+)\*/',
        '/Enviado a\s*\n(.+?)(?=\n\S|\Z)/s',
        '/Beneficiario\s+([\s\S]+?)(?=\n\w+|$)/i',
        '/Pagado a\s*<\/td>\s*<td[^>]*>\s*<b>([^<]+)<\/b>/i',
        '/Enviado a\s*<\/td>\s*<td[^>]*>\s*<b>([^<]+)<\/b>/i',
        '/Realizaste\s*\*([^\*]+)\*/i',
        '/Tarjeta de (?:Débito|Crédito) BCP (?:en|por) ([^.]+)\./i',
    ];
    foreach ($enviadoRegExps as $regex) {
        if (preg_match($regex, $cuerpo, $m) && isset($m[1])) {
            $datos['enviado_a'] = trim(html_entity_decode($m[1]));
            break;
        }
    }
    // Si no se encontró un valor para 'enviado_a', asignar 'Operación bancaria' por defecto
    if (empty($datos['enviado_a'])) {
        $datos['enviado_a'] = 'Operacion bancaria';
    }
    // === FECHA ===
    $fechaRegExps = [
        '/Fecha y hora\s*<\/td>\s*<td[^>]*>\s*<b>\s*(\d{1,2}\s+de\s+[a-záéíóúñ]+\s+de\s+\d{4}\s*-\s*\d{1,2}:\d{2}\s*[AP]M)\s*<\/b>/i',
        '/[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+,\s*(\d{1,2}\s+[a-záéíóúñ]+\s+\d{4}\s*-\s*\d{1,2}:\d{2}\s*[ap]\.\s*m\.)/i',
        '/Fecha y hora<\/td>\s*<td[^>]*>\s*<b><a[^>]*>([^<]+)<\/a><\/b>/i',
        '/Fecha y hora<\/td>\s*<td[^>]*><b>(\d{1,2}\s+de\s+\w+\s+de\s+\d{4}\s*-\s*\d{1,2}:\d{2}\s*[APM]{2})<\/b>/i',
        '/Fecha y hora:\s*\*([^\*]+)\*/i',
        '/Fecha y hora \*(\d{2}\sde\s\w+\sde\s\d{4}\s-\s\d{2}:\d{2}\s[APM]{2})\*/',
        '/\d{1,2}\s+[a-z]+\s+\d{4}\s*-\s*\d{1,2}:\d{2}\s*[a. m. p.]+/i',
        '/(\d{2}\/\d{2}\/\d{4}\s*-\s*\d{1,2}:\d{2}\s*[AP]M)/i',
        '/Fecha y hora \*(\d{2}\/\d{2}\/\d{4} \d{2}:\d{2})\*/i',
        '/Fecha y hora\s+(\d{2}\/\d{2}\/\d{4} - \d{2}:\d{2} [ap]\. m\.)/i',
        '/Fecha y hora\s*<\/td>\s*<td[^>]*>\s*<b>\s*(\d{2}\/\d{2}\/\d{4}\s*-\s*\d{2}:\d{2}:\d{2})\s*<\/b>/i',
        '/Fecha y hora \*(\d{1,2} de [a-záéíóúñ]+ de \d{4} \d{1,2}:\d{2} [AP]M)\*/i',
    ];

    foreach ($fechaRegExps as $regex) {
        if (preg_match($regex, $cuerpo, $m) && isset($m[1])) {
            $fecha_original = trim($m[1]);
            $datos['fecha_y_hora'] = formatearFechaISO($fecha_original);
            break;
        }
    }

    // === NÚMERO DE OPERACIÓN ===
    $nOperacionRegExps = [
        '/N.*?mero de operaci.*?n\s*<\/td>\s*<td[^>]*>\s*<b>\s*(\d+)\s*<\/b>/i',
        '/Número de operación<\/td>\s*<td[^>]*>\s*<b><a[^>]*>(\d+)<\/a><\/b>/i',
        '/N[^\w]*mero de operaci[^\w]*n\s*\*(\d{6,})\*/i',
        '/N[^\w]*mero de operaci[^\w]*n:\s*\*(\d{6,})\*/i',
        '/N[^\w]mero de operaci[^\w]n\s*(\d{6,})/i',
        '/Número de operación<\/td>\s*<td[^>]*><b>(\d+)<\/b>/i',
        '/N[^\w]*mero\s+de\s+operaci[^\w]*n[^<]*<\/td>\s*<td[^>]*>.*?(\d{6,}).*?<\/td>/si'
    ];
    foreach ($nOperacionRegExps as $regex) {
        if (preg_match($regex, $cuerpo, $m) && isset($m[1])) {
            $datos['noperacion'] = trim($m[1]);
            break;
        }
    }
    // === CÓDIGO DE CAJERO Y CUENTA ===
    if (preg_match_all('/<td[^>]*>(Código de cajero|Cuenta de (?:cargo|abono))<\/td>\s*<td[^>]*>\s*<b>([^<]+)<\/b>/i', $cuerpo, $matches, PREG_SET_ORDER)) {
        $extras = [];
        foreach ($matches as $match) {
            if (isset($match[2])) {
                $extras[] = trim($match[2]);
            }
        }
        if (!empty($extras)) {
            $datos['enviado_a'] = implode(' / ', $extras);
        }
    }
    // === 4. VALIDACIÓN FINAL: ¿Es un movimiento financiero válido? ===
    if (is_null($datos['monto'])) {
        return null;
    }
    if (isset($datos['enviado_a']) && preg_match('/creaci[^\w]n de (?:tu )?Clave de Internet/i', $datos['enviado_a'])) {
        return null;
    }
    return $datos; // Retorna datos válidos
}
function formatearFechaISO($fecha_original)
{
    // Normalizar caracteres y espacios
    $fecha_original = trim($fecha_original);
    $fecha_original = preg_replace('/\s+/', ' ', $fecha_original); // Elimina múltiples espacios
    $fecha_original = preg_replace('/a\.\s*m\./i', 'AM', $fecha_original); // Normaliza AM
    $fecha_original = preg_replace('/p\.\s*m\./i', 'PM', $fecha_original); // Normaliza PM

    // Formato "30 de abril de 2025 - 03:40 AM"
    if (preg_match('/(\d{1,2})\s+de\s+([a-záéíóúñ]+)\s+de\s+(\d{4})\s*-\s*(\d{1,2}:\d{2})\s*(AM|PM)/i', $fecha_original, $matches)) {
        $meses = [
            'enero' => '01',
            'febrero' => '02',
            'marzo' => '03',
            'abril' => '04',
            'mayo' => '05',
            'junio' => '06',
            'julio' => '07',
            'agosto' => '08',
            'septiembre' => '09',
            'octubre' => '10',
            'noviembre' => '11',
            'diciembre' => '12'
        ];

        $mes = $meses[strtolower($matches[2])] ?? '01';

        return sprintf(
            '%04d-%02d-%02d',
            $matches[3],
            $mes,
            str_pad($matches[1], 2, '0', STR_PAD_LEFT)
        );
    }
    // Formato "01 de junio de 2024 11:53 AM"
    elseif (preg_match('/(\d{1,2})\s+de\s+([a-záéíóúñ]+)\s+de\s+(\d{4})\s+(\d{1,2}:\d{2})\s*(AM|PM)/i', $fecha_original, $matches)) {
        $meses = [
            'enero' => '01',
            'febrero' => '02',
            'marzo' => '03',
            'abril' => '04',
            'mayo' => '05',
            'junio' => '06',
            'julio' => '07',
            'agosto' => '08',
            'septiembre' => '09',
            'octubre' => '10',
            'noviembre' => '11',
            'diciembre' => '12'
        ];

        $mes = $meses[strtolower($matches[2])] ?? '01';

        return sprintf(
            '%04d-%02d-%02d',
            $matches[3],
            $mes,
            str_pad($matches[1], 2, '0', STR_PAD_LEFT)
        );
    }
    // Formato "01 Abril 2025 - 04:57 PM"
    elseif (preg_match('/(\d{1,2})\s+([a-záéíóúñ]+)\s+(\d{4})\s*-\s*(\d{1,2}:\d{2})\s*(AM|PM)/i', $fecha_original, $matches)) {
        $meses = [
            'enero' => '01',
            'febrero' => '02',
            'marzo' => '03',
            'abril' => '04',
            'mayo' => '05',
            'junio' => '06',
            'julio' => '07',
            'agosto' => '08',
            'septiembre' => '09',
            'octubre' => '10',
            'noviembre' => '11',
            'diciembre' => '12'
        ];

        $mes = $meses[strtolower($matches[2])] ?? '01';

        return sprintf(
            '%04d-%02d-%02d',
            $matches[3],
            $mes,
            str_pad($matches[1], 2, '0', STR_PAD_LEFT)
        );
    }
    // Formato "30/04/2025 - 03:40 AM"
    elseif (preg_match('/(\d{2})\/(\d{2})\/(\d{4})\s*-\s*(\d{1,2}:\d{2})\s*(AM|PM)/i', $fecha_original, $matches)) {
        return "{$matches[3]}-{$matches[2]}-{$matches[1]}";
    }
    // Formato "03/05/2025 17:26" (NUEVO FORMATO)
    elseif (preg_match('/(\d{2})\/(\d{2})\/(\d{4})\s+(\d{1,2}:\d{2})/i', $fecha_original, $matches)) {
        return "{$matches[3]}-{$matches[2]}-{$matches[1]}";
    }
    // Formato "2024-06-27 15:30:00"
    elseif (preg_match('/^(\d{4}-\d{2}-\d{2})(?: \d{2}:\d{2}(:\d{2})?)?$/', $fecha_original, $matches)) {
        return $matches[1]; // Devuelve solo la parte de la fecha
    }
    // Formato "25/06/2024 - 18:09:37"
    elseif (preg_match('/(\d{2})\/(\d{2})\/(\d{4})\s*-\s*(\d{2}:\d{2}:\d{2})/i', $fecha_original, $matches)) {
        return "{$matches[3]}-{$matches[2]}-{$matches[1]}";
    }
    return $fecha_original; // Devuelve original si no se pudo convertir
}
