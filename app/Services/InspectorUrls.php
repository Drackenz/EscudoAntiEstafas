<?php

namespace App\Services;

class InspectorUrls
{
    private const ACORTADORES = [
        'bit.ly', 'tinyurl.com', 't.co', 'cutt.ly', 'rebrand.ly', 'is.gd',
        'tiny.cc', 'ow.ly', 'rb.gy', 'lnkd.in', 'goo.gl', 'qrco.de',
    ];

    private const TERMINACIONES_INUSUALES = ['xyz', 'top', 'click', 'work', 'loan', 'gq', 'tk', 'ml', 'cf', 'ga'];

    /** Revisa la estructura de las direcciones sin abrirlas ni consultar servicios externos. */
    public function inspeccionar(string $texto): array
    {
        preg_match_all('~\bhttps?://[^\s<>"\']+|\bwww\.[^\s<>"\']+|\bwa\.me/\s*\d+~iu', $texto, $coincidencias);
        $enlaces = [];

        foreach (array_unique($coincidencias[0] ?? []) as $enlace) {
            $enlace = rtrim($enlace, '.,;:!?)]}');

            // WhatsApp suele escribirse sin protocolo y puede quedar partido al pegarlo.
            $enlace = preg_replace('~(wa\.me/)\s+~iu', '$1', $enlace) ?? $enlace;

            if (str_starts_with(strtolower($enlace), 'www.')) {
                $enlace = 'http://'.$enlace;
            } elseif (str_starts_with(strtolower($enlace), 'wa.me/')) {
                $enlace = 'https://'.$enlace;
            }

            $host = strtolower((string) parse_url($enlace, PHP_URL_HOST));
            $host = rtrim($host, '.');
            $partes = explode('.', $host);
            $terminacion = end($partes);
            $senales = [];
            $mensajes = [];

            if ($this->esAcortador($host)) {
                $senales[] = 'acortador';
                $mensajes[] = 'Enlace acortado: puede ocultar el destino final.';
            }

            if (in_array($terminacion, self::TERMINACIONES_INUSUALES, true)) {
                $senales[] = 'terminacion_inusual';
                $mensajes[] = 'Terminación de dominio poco habitual (.'.$terminacion.').';
            }

            if (str_starts_with(strtolower($enlace), 'http://')) {
                $senales[] = 'http';
                $mensajes[] = 'Usa HTTP sin cifrado.';
            }

            $enlaces[] = [
                'url' => $enlace,
                'sospechoso' => $senales !== [],
                'senales' => $senales,
                'mensajes' => $mensajes,
            ];
        }

        return $enlaces;
    }

    private function esAcortador(string $host): bool
    {
        foreach (self::ACORTADORES as $dominio) {
            if ($host === $dominio || str_ends_with($host, '.'.$dominio)) {
                return true;
            }
        }

        return false;
    }
}
