<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnalisisController extends Controller
{
    public function inicio()
    {
        return view('inicio');
    }

    public function validarEntrada(Request $request)
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

        // En este paso solo comprobamos la entrada; no almacenamos texto ni imagen.
        return view('inicio', [
            'mensajeEstado' => 'La entrada es válida. El análisis se habilitará en el siguiente paso.',
        ]);
    }
}
