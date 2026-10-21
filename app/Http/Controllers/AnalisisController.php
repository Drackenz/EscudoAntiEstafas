<?php

namespace App\Http\Controllers;

use App\Models\Analisis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\GeminiAnalizador;
use App\Services\InspectorUrls;
use App\Services\MotorReglas;

class AnalisisController extends Controller
{
    public function inicio()
    {
        return view('inicio');
    }

    public function validarEntrada(
        Request $request,
        GeminiAnalizador $gemini,
        MotorReglas $motor,
        InspectorUrls $inspectorUrls
    )
    {
        $validador = Validator::make($request->all(), [
            'mensaje' => ['nullable', 'string', 'max:10000', 'required_without:imagen'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'mensaje.required_without' => 'Escribe un mensaje o selecciona una imagen para continuar.',
            'mensaje.max' => 'El texto no puede superar los 10,000 caracteres.',
            'imagen.image' => 'El archivo debe ser una imagen válida.',
            'imagen.mimes' => 'La imagen debe estar en formato PNG, JPG o WebP.',
            'imagen.max' => 'La imagen no puede superar los 5 MB.',
        ]);

        if ($validador->fails()) {
            // No devolvemos el texto anterior: podría contener datos privados.
            return response()->view('inicio', [
                'errores' => $validador->errors(),
            ], 422);
        }

        $mensaje = (string) $request->input('mensaje', '');
        $imagen = $request->file('imagen');
        $resultadoGemini = $gemini->analizar($mensaje, $imagen);
        $resultadoReglas = null;

        if (trim($mensaje) !== '') {
            $resultadoReglas = $motor->analizarTexto($mensaje);

            if ($imagen !== null) {
                $resultadoReglas['banderas'][] = 'captura no analizada por el motor offline';
                $resultadoReglas['explicacion'] .= ' La captura no pudo ser revisada sin conexión con Gemini.';
            }
        }

        if ($resultadoReglas === null) {
            // El motor offline no puede leer una imagen, así que no la marca como segura.
            $resultadoReglas = [
                'riesgo' => 50,
                'veredicto' => 'SOSPECHOSO',
                'banderas' => ['imagen no analizada por el motor offline'],
                'explicacion' => 'No fue posible revisar la captura. No abras enlaces ni compartas datos hasta verificarla.',
                'accion' => 'Confirma el mensaje con la institución por un canal oficial antes de responder.',
                'enlaces_sospechosos' => [],
                'canal_detectado' => 'desconocido',
            ];
        }

        if ($resultadoGemini !== null) {
            $resultadoGemini = $this->ocultarDatosResultado($resultadoGemini);
        }
        $resultadoReglas = $this->ocultarDatosResultado($resultadoReglas);

        $resultado = $resultadoGemini ?? $resultadoReglas;
        $metodo = $resultadoGemini !== null ? 'gemini' : 'reglas';

        $resultado['detalle_enlaces'] = $inspectorUrls->inspeccionar($mensaje);
        foreach ($resultado['detalle_enlaces'] as $detalle) {
            foreach ($detalle['senales'] as $senal) {
                $bandera = 'enlace acortado';

                if ($senal === 'terminacion_inusual') {
                    $bandera = 'dominio de terminación inusual';
                } elseif ($senal === 'http') {
                    $bandera = 'enlace sin conexión cifrada';
                }

                if (! in_array($bandera, $resultado['banderas'], true)) {
                    $resultado['banderas'][] = $bandera;
                }
            }
        }

        DB::transaction(function () use ($mensaje, $resultado, $metodo): void {
            $analisis = Analisis::create([
                // En análisis solo con imagen, el mensaje es vacío y se calcula su hash vacío.
                'hash_mensaje' => hash('sha256', $mensaje),
                'canal' => $resultado['canal_detectado'],
                'riesgo' => $resultado['riesgo'],
                'veredicto' => $resultado['veredicto'],
                'explicacion' => $resultado['explicacion'],
                'accion_recomendada' => $resultado['accion'],
                'metodo' => $metodo,
                'fecha_analisis' => now(),
            ]);

            $nombresGuardados = [];
            foreach ($resultado['banderas'] as $bandera) {
                $nombre = mb_substr(trim($bandera), 0, 100, 'UTF-8');

                if ($nombre !== '' && ! in_array($nombre, $nombresGuardados, true)) {
                    $analisis->banderasDetectadas()->create(['nombre' => $nombre]);
                    $nombresGuardados[] = $nombre;
                }
            }
        });

        return view('resultado', [
            'resultado' => $resultado,
            'metodo' => $metodo,
            'comparacion' => [
                'gemini' => $resultadoGemini,
                'reglas' => $resultadoReglas,
            ],
        ]);
    }

    private function ocultarDatosResultado(array $resultado): array
    {
        $resultado['explicacion'] = $this->ocultarDatosPrivados($resultado['explicacion']);
        $resultado['accion'] = $this->ocultarDatosPrivados($resultado['accion']);

        $banderasSeguras = [];
        foreach ($resultado['banderas'] as $bandera) {
            $banderasSeguras[] = $this->ocultarDatosPrivados($bandera);
        }
        $resultado['banderas'] = $banderasSeguras;

        return $resultado;
    }

    private function ocultarDatosPrivados(string $texto): string
    {
        $patrones = [
            '/\b(?:contrase(?:ña|na)|clave)\s*(?::|=|es)\s*[^\s,.;]{3,}/iu',
            '/\b(?:pin|token|c[oó]digo(?:\s+de\s+verificaci[oó]n)?)\s*(?::|=|es)?\s*[\p{L}\p{N}!@#$%^&*_.-]{4,}/iu',
            '/(?<!\d)(?:\d[ -]?){13,19}(?!\d)/u',
            '/(?<!\d)\+?\d[\d\s().-]{7,}\d(?!\d)/u',
        ];

        foreach ($patrones as $patron) {
            $texto = preg_replace($patron, '[dato oculto]', $texto) ?? $texto;
        }

        return $texto;
    }
}
