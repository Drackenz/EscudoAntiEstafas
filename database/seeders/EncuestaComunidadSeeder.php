<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class EncuestaComunidadSeeder extends Seeder
{
    public function run(): void
    {
        $pregunta = '[DEMO] ¿Qué señal te haría desconfiar de un mensaje?';
        $respuestas = [
            'Pide una clave o código' => [8, 9, 11, 12, 15, 17],
            'Presiona para actuar rápido' => [6, 8, 9, 10, 12, 14],
            'Ofrece un premio inesperado' => [5, 6, 7, 9, 10, 11],
            'Trae un enlace desconocido' => [7, 9, 10, 12, 14, 16],
        ];

        DB::table('encuesta_comunidad')->where('pregunta', $pregunta)->delete();

        foreach ($respuestas as $respuesta => $cantidades) {
            foreach ($cantidades as $semana => $cantidad) {
                DB::table('encuesta_comunidad')->insert([
                    'pregunta' => $pregunta,
                    'respuesta' => $respuesta,
                    'cantidad' => $cantidad,
                    'fecha_registro' => Carbon::now()->subWeeks(5 - $semana)->toDateString(),
                ]);
            }
        }
    }
}
