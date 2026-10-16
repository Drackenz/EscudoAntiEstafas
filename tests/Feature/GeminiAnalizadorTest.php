<?php

namespace Tests\Feature;

use Illuminate\Http\Client\Request as SolicitudHttp;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeminiAnalizadorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.gemini.api_key' => 'clave-falsa-de-prueba',
            'services.gemini.model' => 'modelo-prueba',
        ]);
    }

    public function test_envia_salida_json_y_php_calcula_el_veredicto(): void
    {
        Http::fake([
            '*' => Http::response($this->respuestaGemini([
                'riesgo' => 70,
                'veredicto' => 'SEGURO',
                'banderas' => ['solicitud de credenciales'],
                'explicacion' => 'Solicita un dato privado.',
                'accion' => 'Verifica por un canal oficial.',
                'enlaces_sospechosos' => [],
                'canal_detectado' => 'desconocido',
            ])),
        ]);

        $this->post('/analizar', ['mensaje' => 'Comparte tu clave ahora.'])
            ->assertOk()
            ->assertJsonPath('riesgo', 70)
            ->assertJsonPath('veredicto', 'ESTAFA');

        Http::assertSent(function (SolicitudHttp $solicitud): bool {
            return str_contains($solicitud->url(), '/models/modelo-prueba:generateContent');
        });

        Http::assertSent(function (SolicitudHttp $solicitud): bool {
            return $solicitud->hasHeader('x-goog-api-key', 'clave-falsa-de-prueba');
        });

        Http::assertSent(function (SolicitudHttp $solicitud): bool {
            $cuerpo = $solicitud->data();

            return data_get($cuerpo, 'generationConfig.responseFormat.text.mimeType') === 'application/json'
                && data_get($cuerpo, 'generationConfig.responseFormat.text.schema.type') === 'object'
                && data_get($cuerpo, 'generationConfig.responseFormat.text.schema.required.0') === 'riesgo';
        });
    }

    public function test_usa_el_motor_de_reglas_si_gemini_devuelve_json_incompleto(): void
    {
        Http::fake([
            '*' => Http::response($this->respuestaGemini([
                'riesgo' => 90,
                'veredicto' => 'ESTAFA',
            ])),
        ]);

        $this->post('/analizar', ['mensaje' => 'URGENTE: responde ahora mismo.'])
            ->assertOk()
            ->assertJsonPath('riesgo', 15)
            ->assertJsonPath('veredicto', 'SEGURO');
    }

    public function test_usa_el_motor_de_reglas_si_gemini_responde_con_error(): void
    {
        Http::fake(['*' => Http::response(['error' => 'quota'], 429)]);

        $this->post('/analizar', ['mensaje' => 'Ganaste un premio en el sorteo.'])
            ->assertOk()
            ->assertJsonPath('riesgo', 20)
            ->assertJsonPath('banderas.0', 'premio');
    }

    public function test_usa_el_motor_de_reglas_si_gemini_no_tiene_conexion(): void
    {
        Http::fake(function () {
            throw new ConnectionException('Fallo de red simulado');
        });

        $this->post('/analizar', ['mensaje' => 'Ganaste un premio en el sorteo.'])
            ->assertOk()
            ->assertJsonPath('riesgo', 20)
            ->assertJsonPath('veredicto', 'SEGURO');
    }

    public function test_envia_la_imagen_en_linea_y_no_persiste_la_captura(): void
    {
        Http::fake([
            '*' => Http::response($this->respuestaGemini([
                'riesgo' => 35,
                'veredicto' => 'SOSPECHOSO',
                'banderas' => ['enlace sospechoso'],
                'explicacion' => 'La captura muestra una solicitud dudosa.',
                'accion' => 'Confirma el mensaje por otro medio.',
                'enlaces_sospechosos' => [],
                'canal_detectado' => 'whatsapp',
            ])),
        ]);

        $imagen = UploadedFile::fake()->image('captura.png');

        $this->post('/analizar', ['imagen' => $imagen])
            ->assertOk()
            ->assertJsonPath('riesgo', 35);

        Http::assertSent(function (SolicitudHttp $solicitud): bool {
            $parteImagen = data_get($solicitud->data(), 'contents.0.parts.1.inlineData');

            return is_array($parteImagen)
                && $parteImagen['mimeType'] === 'image/png'
                && $parteImagen['data'] !== '';
        });
    }

    private function respuestaGemini(array $resultado): array
    {
        return [
            'candidates' => [[
                'content' => [
                    'parts' => [['text' => json_encode($resultado, JSON_UNESCAPED_UNICODE)]],
                ],
            ]],
        ];
    }
}
