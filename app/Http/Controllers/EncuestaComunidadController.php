<?php

namespace App\Http\Controllers;

use App\Models\EncuestaComunidad;
use Illuminate\View\View;

class EncuestaComunidadController extends Controller
{
    public function inicio(): View
    {
        $registros = EncuestaComunidad::query()
            ->where('pregunta', 'not like', '[DEMO] %')
            ->orderByDesc('fecha_registro')
            ->get();
        $esDemostracion = false;

        if ($registros->isEmpty()) {
            $registros = EncuestaComunidad::query()
                ->where('pregunta', 'like', '[DEMO] %')
                ->orderBy('fecha_registro')
                ->get();
            $esDemostracion = $registros->isNotEmpty();
        }

        $datosGraficas = [];
        $pregunta = null;

        if ($registros->isNotEmpty()) {
            $preguntaReciente = $registros->sortByDesc('fecha_registro')->first()->pregunta;
            $registros = $registros->where('pregunta', $preguntaReciente);
            $pregunta = $esDemostracion
                ? substr($preguntaReciente, 7)
                : $preguntaReciente;

            $conteoPorRespuesta = $registros
                ->groupBy('respuesta')
                ->map(function ($filas) {
                    return $filas->sum('cantidad');
                })
                ->sortKeys();

            $conteoPorFecha = $registros
                ->groupBy(function ($fila) {
                    return $fila->fecha_registro->format('Y-m-d');
                })
                ->map(function ($filas) {
                    return $filas->sum('cantidad');
                })
                ->sortKeys();

            $datosGraficas = [
                'respuestas' => [
                    'etiquetas' => $conteoPorRespuesta->keys()->values(),
                    'cantidades' => $conteoPorRespuesta->values(),
                ],
                'fechas' => [
                    'etiquetas' => $conteoPorFecha->keys()->values(),
                    'cantidades' => $conteoPorFecha->values(),
                ],
            ];
        }

        return view('comunidad', compact('datosGraficas', 'esDemostracion', 'pregunta'));
    }
}
