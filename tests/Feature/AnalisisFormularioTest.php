<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalisisFormularioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.gemini.api_key' => '',
            'services.gemini.model' => '',
        ]);
    }

    public function test_muestra_el_formulario_de_entrada(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Revisemos ese mensaje con calma.')
            ->assertSee('image/webp')
            ->assertSee('Modo fácil de leer')
            ->assertSee('css/app.css')
            ->assertSee('js/app.js');
    }

    public function test_pide_texto_o_imagen_y_no_rellena_el_texto_anterior(): void
    {
        $textoPrivadoDePrueba = 'Mensaje ficticio que no debe regresar en la respuesta';

        $this->post('/analizar', ['mensaje' => $textoPrivadoDePrueba])
            ->assertOk()
            ->assertViewIs('resultado')
            ->assertDontSee($textoPrivadoDePrueba);

        $registro = \Illuminate\Support\Facades\DB::table('analisis')->first();
        $this->assertSame(hash('sha256', $textoPrivadoDePrueba), $registro->hash_mensaje);
        $this->assertSame('reglas', $registro->metodo);
        $this->assertSame('SEGURO', $registro->veredicto);

        $this->post('/analizar', [])
            ->assertUnprocessable()
            ->assertSee('Escribe un mensaje o selecciona una imagen');
    }

    public function test_acepta_una_imagen_permitida_sin_guardarla(): void
    {
        $imagen = UploadedFile::fake()->image('captura.png');

        $this->post('/analizar', ['imagen' => $imagen])
            ->assertOk()
            ->assertViewIs('resultado')
            ->assertSee('SOSPECHOSO')
            ->assertSee('El texto original y la captura no se guardaron.');

        $registro = \Illuminate\Support\Facades\DB::table('analisis')->first();
        $this->assertSame(hash('sha256', ''), $registro->hash_mensaje);
        $this->assertSame('reglas', $registro->metodo);
        $this->assertSame(50, (int) $registro->riesgo);
        $this->assertDatabaseHas('banderas_detectadas', [
            'analisis_id' => $registro->id,
            'nombre' => 'imagen no analizada por el motor offline',
        ]);
    }

    public function test_rechaza_archivos_que_no_son_imagenes_permitidas(): void
    {
        $archivo = UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf');

        $this->post('/analizar', ['imagen' => $archivo])
            ->assertUnprocessable()
            ->assertSee('El archivo debe ser una imagen válida');
    }

    public function test_el_resumen_no_tiene_columnas_para_el_texto_original_ni_la_imagen(): void
    {
        $columnas = \Illuminate\Support\Facades\Schema::getColumnListing('analisis');

        $this->assertSame([
            'id', 'hash_mensaje', 'canal', 'riesgo', 'veredicto', 'explicacion',
            'accion_recomendada', 'metodo', 'fecha_analisis',
        ], $columnas);
    }
}
