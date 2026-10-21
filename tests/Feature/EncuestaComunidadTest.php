<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EncuestaComunidadTest extends TestCase
{
    use RefreshDatabase;

    public function test_muestra_estado_vacio_cuando_no_hay_conteos(): void
    {
        $this->get('/comunidad')
            ->assertOk()
            ->assertSee('Aún no hay datos para mostrar')
            ->assertSee('EncuestaComunidadSeeder');
    }

    public function test_grafica_conteos_agrupados_sin_mostrar_respuestas_individuales(): void
    {
        DB::table('encuesta_comunidad')->insert([
            [
                'pregunta' => '¿Qué señal reconoces?',
                'respuesta' => 'Pide una clave',
                'cantidad' => 3,
                'fecha_registro' => '2026-10-01',
            ],
            [
                'pregunta' => '¿Qué señal reconoces?',
                'respuesta' => 'Pide una clave',
                'cantidad' => 2,
                'fecha_registro' => '2026-10-08',
            ],
            [
                'pregunta' => '¿Qué señal reconoces?',
                'respuesta' => 'Ofrece un premio',
                'cantidad' => 4,
                'fecha_registro' => '2026-10-08',
            ],
        ]);

        $this->get('/comunidad')
            ->assertOk()
            ->assertSee('¿Qué señal reconoces?')
            ->assertSee('grafica-respuestas')
            ->assertSee('grafica-fechas')
            ->assertSee('Pide una clave')
            ->assertSee('"cantidades":[4,5]', false)
            ->assertSee('"cantidades":[3,6]', false);
    }

    public function test_datos_de_demostracion_se_indican_y_se_agrupan(): void
    {
        $this->seed(\Database\Seeders\EncuestaComunidadSeeder::class);
        $this->seed(\Database\Seeders\EncuestaComunidadSeeder::class);

        $this->assertSame(24, DB::table('encuesta_comunidad')->count());

        $this->get('/comunidad')
            ->assertOk()
            ->assertSee('Datos de demostración')
            ->assertSee('¿Qué señal te haría desconfiar de un mensaje?')
            ->assertSee('"etiquetas":["2026-', false)
            ->assertDontSee('[DEMO]');
    }
}
