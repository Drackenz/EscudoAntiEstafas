<?php

namespace App\Services;

class MotorReglas
{
    private const REGLAS = [
        'urgencia' => [15, '/\b(urgente|inmediatamente|ahora mismo|ultim[oa] aviso|ultimas? horas|hoy mismo|en\s+\d+\s+minutos)\b/u'],
        'posible estafa del familiar con teléfono dañado' => [35, '/\b(?:pap[aá]|mam[aá]|hijo|hija|hermano|hermana)\b[\s\S]{0,220}\b(?:tel[eé]fono|celular|sim|tarjeta sim)\b[\s\S]{0,180}\b(?:agua|moj[oó]|da[nñ]ad[oa]|rot[oa]|nuevo n[uú]mero)\b|\b(?:tel[eé]fono|celular|sim|tarjeta sim)\b[\s\S]{0,180}\b(?:agua|moj[oó]|da[nñ]ad[oa]|rot[oa])\b[\s\S]{0,220}\b(?:pap[aá]|mam[aá]|hijo|hija|hermano|hermana)\b/u'],
        'solicitud de contacto por WhatsApp' => [15, '/\b(?:whats\s*app|whatsapp|wa\.me)\b/u'],
        'posible suplantación de autoridad de tránsito' => [25, '/\b(?:dgt|direcci[oó]n general de tr[aá]fico)\b[\s\S]{0,160}\b(?:multa|infracci[oó]n|recargo|pago)\b|\b(?:multa|infracci[oó]n|recargo|pago)\b[\s\S]{0,160}\b(?:dgt|direcci[oó]n general de tr[aá]fico)\b/u'],
        'cobro de multa o infracción pendiente' => [30, '/\b(?:multa|infracci[oó]n|sanci[oó]n)\b(?=[\s\S]{0,100}\b(?:pendiente|impagad[ao]|pagar|pago|recargo|vencid[ao])\b)|\b(?:pendiente|impagad[ao]|pagar|pago|recargo|vencid[ao])\b(?=[\s\S]{0,100}\b(?:multa|infracci[oó]n|sanci[oó]n)\b)/u'],
        'presión por recargo o plazo de pago' => [20, '/\b(?:recargo|evitar (?:el )?recargo|paga(?:r)? hoy|plazo final|último plazo|vencimiento hoy)\b/u'],
        'premio' => [20, '/\b(premio|ganaste|ganador[oa]?|sorteo|regalo|bono gratis)\b/u'],
        'solicitud de credenciales o códigos' => [35, '/\b(contrase[nñ]a|clave|pin|c[oó]digo de verificaci[oó]n|token|datos bancarios|n[uú]mero de tarjeta)\b/u'],
        'cambio de número de contacto' => [25, '/\b(?:n[uú]mero nuevo|nuevo n[uú]mero|cambi[eé] de n[uú]mero|este es mi nuevo n[uú]mero|perd[ií] mi tel[eé]fono)\b/u'],
    ];

    /** Analiza el texto en memoria y devuelve señales sin persistir el mensaje. */
    public function analizarTexto(string $mensaje): array
    {
        $texto = mb_strtolower($mensaje, 'UTF-8');
        $puntaje = 0;
        $banderas = [];

        foreach (self::REGLAS as $nombre => [$puntos, $patron]) {
            if (preg_match($patron, $texto) === 1) {
                $puntaje += $puntos;
                $banderas[] = $nombre;
            }
        }

        $detallesEnlaces = (new InspectorUrls)->inspeccionar($mensaje);
        $enlacesSospechosos = [];
        foreach ($detallesEnlaces as $detalle) {
            foreach ($detalle['senales'] as $senal) {
                if ($senal === 'acortador') {
                    $puntaje += 20;
                    $banderas[] = 'enlace acortado';
                } elseif ($senal === 'terminacion_inusual') {
                    $puntaje += 20;
                    $banderas[] = 'dominio de terminación inusual';
                } elseif ($senal === 'http') {
                    $puntaje += 15;
                    $banderas[] = 'enlace sin conexión cifrada';
                }
            }

            if ($detalle['sospechoso']) {
                $enlacesSospechosos[] = $detalle['url'];
            }
        }

        $puntaje = min(100, $puntaje);
        $veredicto = match (true) {
            $puntaje >= 65 => 'ESTAFA',
            $puntaje >= 35 => 'SOSPECHOSO',
            default => 'SEGURO',
        };

        return [
            'riesgo' => $puntaje,
            'veredicto' => $veredicto,
            'banderas' => array_values(array_unique($banderas)),
            'explicacion' => $banderas === []
                ? 'No se detectaron señales de riesgo en el texto analizado.'
                : 'Se detectaron estas señales: '.implode(', ', array_values(array_unique($banderas))).'.',
            'accion' => $this->accionPara($veredicto),
            'enlaces_sospechosos' => $enlacesSospechosos,
            'canal_detectado' => $this->detectarCanal($texto),
        ];
    }

    private function detectarCanal(string $texto): string
    {
        if (str_contains($texto, 'whatsapp') || str_contains($texto, 'wa.me')) {
            return 'whatsapp';
        }

        if (preg_match('/\b(sms|mensaje de texto)\b/u', $texto) === 1) {
            return 'sms';
        }

        if (str_contains($texto, 'asunto:') || str_contains($texto, 'remitente:')
            || preg_match('/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}\b/i', $texto) === 1) {
            return 'correo';
        }

        return 'desconocido';
    }

    private function accionPara(string $veredicto): string
    {
        return match ($veredicto) {
            'ESTAFA' => 'No respondas ni abras los enlaces. Verifica por un canal oficial y reporta el mensaje.',
            'SOSPECHOSO' => 'No compartas datos ni abras enlaces hasta confirmar la solicitud por un canal oficial.',
            default => 'Mantén precaución y verifica cualquier solicitud sensible por un canal oficial.',
        };
    }
}
