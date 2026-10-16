<?php

namespace App\Services;

class MotorReglas
{
    private const REGLAS = [
        'urgencia' => [15, '/\b(urgente|inmediatamente|ahora mismo|ultim[oa] aviso|ultimas? horas|hoy mismo|en\s+\d+\s+minutos)\b/u'],
        'premio' => [20, '/\b(premio|ganaste|ganador[oa]?|sorteo|regalo|bono gratis)\b/u'],
        'solicitud de credenciales o códigos' => [35, '/\b(contrase[nñ]a|clave|pin|c[oó]digo de verificaci[oó]n|token|datos bancarios|n[uú]mero de tarjeta)\b/u'],
        'cambio de número de contacto' => [25, '/\b(n[uú]mero nuevo|cambi[eé] de n[uú]mero|este es mi nuevo n[uú]mero|perd[ií] mi tel[eé]fono)\b/u'],
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
