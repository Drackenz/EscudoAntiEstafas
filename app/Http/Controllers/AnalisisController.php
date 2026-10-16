<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\GeminiAnalizador;
use App\Services\MotorReglas;

class AnalisisController extends Controller
{
    public function inicio()
    {
        return view('inicio');
    }

    public function validarEntrada(Request $request, GeminiAnalizador $gemini, MotorReglas $motor)
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
        $resultado = $gemini->analizar($mensaje, $imagen);

        if ($resultado === null && trim($mensaje) !== '') {
            $resultado = $motor->analizarTexto($mensaje);

            if ($imagen !== null) {
                $resultado['banderas'][] = 'captura no analizada por el motor offline';
                $resultado['explicacion'] .= ' La captura no pudo ser revisada sin conexión con Gemini.';
            }
        }

        if ($resultado === null) {
            // El motor offline no puede leer una imagen, así que no la marca como segura.
            $resultado = [
                'riesgo' => 50,
                'veredicto' => 'SOSPECHOSO',
                'banderas' => ['imagen no analizada por el motor offline'],
                'explicacion' => 'No fue posible revisar la captura. No abras enlaces ni compartas datos hasta verificarla.',
                'accion' => 'Confirma el mensaje con la institución por un canal oficial antes de responder.',
                'enlaces_sospechosos' => [],
                'canal_detectado' => 'desconocido',
            ];
        }

        // Devolvemos solo el resultado; el texto y el archivo no se guardan.
        return response()->json($resultado);
    }
}
