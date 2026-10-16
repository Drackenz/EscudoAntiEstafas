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

    private const ACORTADORES = [
        'bit.ly', 'tinyurl.com', 't.co', 'cutt.ly', 'rebrand.ly', 'is.gd',
        'tiny.cc', 'ow.ly', 'rb.gy', 'lnkd.in', 'goo.gl',
    ];

    private const DOMINIOS_INUSUALES = ['xyz', 'top', 'click', 'work', 'loan', 'gq', 'tk', 'ml', 'cf', 'ga'];

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

        $enlaces = $this->enlacesDelTexto($mensaje);
        foreach ($enlaces as $enlace) {
            $host = strtolower((string) parse_url($enlace, PHP_URL_HOST));
            $partes = explode('.', $host);
            $dominioSuperior = end($partes);

            if (in_array($host, self::ACORTADORES, true) || $this->esSubdominioDeAcortador($host)) {
                $puntaje += 20;
                $banderas[] = 'enlace acortado';
            }

            if (in_array($dominioSuperior, self::DOMINIOS_INUSUALES, true)) {
                $puntaje += 20;
                $banderas[] = 'dominio de terminación inusual';
            }

            if (str_starts_with(strtolower($enlace), 'http://')) {
                $puntaje += 15;
                $banderas[] = 'enlace sin conexión cifrada';
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
            'enlaces_sospechosos' => $this->enlacesSospechosos($enlaces),
            'canal_detectado' => $this->detectarCanal($texto),
        ];
    }

    private function enlacesDelTexto(string $texto): array
    {
        preg_match_all('~\bhttps?://[^\s<>"\']+|\bwww\.[^\s<>"\']+~iu', $texto, $coincidencias);

        return array_values(array_unique(array_map(function (string $enlace): string {
            $enlace = rtrim($enlace, '.,;:!?)]}');

            return str_starts_with(strtolower($enlace), 'www.') ? 'http://'.$enlace : $enlace;
        }, $coincidencias[0] ?? [])));
    }

    private function esSubdominioDeAcortador(string $host): bool
    {
        foreach (self::ACORTADORES as $dominio) {
            if (str_ends_with($host, '.'.$dominio)) {
                return true;
            }
        }

        return false;
    }

    private function enlacesSospechosos(array $enlaces): array
    {
        return array_values(array_filter($enlaces, function (string $enlace): bool {
            $host = strtolower((string) parse_url($enlace, PHP_URL_HOST));
            $partes = explode('.', $host);

            return str_starts_with(strtolower($enlace), 'http://')
                || in_array($host, self::ACORTADORES, true)
                || $this->esSubdominioDeAcortador($host)
                || in_array(end($partes), self::DOMINIOS_INUSUALES, true);
        }));
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
