<?php

namespace Tests\Unit;

use App\Services\InspectorUrls;
use PHPUnit\Framework\TestCase;

class InspectorUrlsTest extends TestCase
{
    public function test_detecta_acortador_terminacion_inusual_y_http(): void
    {
        $enlaces = (new InspectorUrls)->inspeccionar(
            'Revisa https://bit.ly/promo, https://tienda.xyz/oferta y http://ejemplo.com.'
        );

        $this->assertCount(3, $enlaces);
        $this->assertSame(['acortador'], $enlaces[0]['senales']);
        $this->assertSame(['terminacion_inusual'], $enlaces[1]['senales']);
        $this->assertSame(['http'], $enlaces[2]['senales']);
        $this->assertSame('https://bit.ly/promo', $enlaces[0]['url']);
        $this->assertTrue($enlaces[0]['sospechoso']);
        $this->assertStringContainsString('(.xyz)', $enlaces[1]['mensajes'][0]);
    }

    public function test_no_confunde_otro_dominio_con_un_acortador(): void
    {
        $enlaces = (new InspectorUrls)->inspeccionar(
            'Visita https://bit.ly.ejemplo.com/cuenta y https://ejemplo.com/inicio'
        );

        $this->assertCount(2, $enlaces);
        $this->assertFalse($enlaces[0]['sospechoso']);
        $this->assertFalse($enlaces[1]['sospechoso']);
        $this->assertSame([], $enlaces[0]['mensajes']);
    }

    public function test_normaliza_www_y_quita_puntuacion_final(): void
    {
        $enlaces = (new InspectorUrls)->inspeccionar('Abre www.ejemplo.com/prueba.');

        $this->assertCount(1, $enlaces);
        $this->assertSame('http://www.ejemplo.com/prueba', $enlaces[0]['url']);
        $this->assertContains('http', $enlaces[0]['senales']);
    }

    public function test_no_guarda_ni_consulta_las_direcciones(): void
    {
        $enlaces = (new InspectorUrls)->inspeccionar('https://tinyurl.com/prueba');

        $this->assertSame([
            [
                'url' => 'https://tinyurl.com/prueba',
                'sospechoso' => true,
                'senales' => ['acortador'],
                'mensajes' => ['Enlace acortado: puede ocultar el destino final.'],
            ],
        ], $enlaces);
    }
}
