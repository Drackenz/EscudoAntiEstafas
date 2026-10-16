<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AnalisisFormularioTest extends TestCase
{
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
            ->assertSee('Revisa un mensaje sospechoso')
            ->assertSee('image/webp');
    }

    public function test_pide_texto_o_imagen_y_no_rellena_el_texto_anterior(): void
    {
        $textoPrivadoDePrueba = 'Mensaje ficticio que no debe regresar en la respuesta';

        $this->post('/analizar', ['mensaje' => $textoPrivadoDePrueba])
            ->assertOk()
            ->assertJsonStructure([
                'riesgo', 'veredicto', 'banderas', 'explicacion', 'accion',
                'enlaces_sospechosos', 'canal_detectado',
            ])
            ->assertDontSee($textoPrivadoDePrueba);

        $this->post('/analizar', [])
            ->assertUnprocessable()
            ->assertSee('Escribe un mensaje o selecciona una imagen');
    }

    public function test_acepta_una_imagen_permitida_sin_guardarla(): void
    {
        $imagen = UploadedFile::fake()->image('captura.png');

        $this->post('/analizar', ['imagen' => $imagen])
            ->assertOk()
            ->assertJsonPath('riesgo', 50)
            ->assertJsonPath('veredicto', 'SOSPECHOSO');
    }

    public function test_rechaza_archivos_que_no_son_imagenes_permitidas(): void
    {
        $archivo = UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf');

        $this->post('/analizar', ['imagen' => $archivo])
            ->assertUnprocessable()
            ->assertSee('El archivo debe ser una imagen válida');
    }
}
