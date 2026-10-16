<?php

namespace Tests\Unit;

use App\Services\MotorReglas;
use PHPUnit\Framework\TestCase;

class MotorReglasTest extends TestCase
{
    public function test_analiza_mas_de_diez_ejemplos_sin_guardar_el_texto(): void
    {
        $motor = new MotorReglas;
        $ejemplos = [
            ['Hola, nos vemos mañana.', 0, 'SEGURO', 'desconocido'],
            ['Tu paquete está en camino.', 0, 'SEGURO', 'desconocido'],
            ['URGENTE: responde ahora mismo.', 15, 'SEGURO', 'desconocido'],
            ['Ganaste un premio en nuestro sorteo.', 20, 'SEGURO', 'desconocido'],
            ['Envía tu contraseña para validar la cuenta.', 35, 'SOSPECHOSO', 'desconocido'],
            ['Cambié de número, escríbeme por WhatsApp.', 25, 'SEGURO', 'whatsapp'],
            ['Revisa este enlace https://bit.ly/ejemplo', 20, 'SEGURO', 'desconocido'],
            ['Consulta https://tienda.xyz/oferta', 20, 'SEGURO', 'desconocido'],
            ['Abre http://sitio.example/oferta', 15, 'SEGURO', 'desconocido'],
            ['Urgente: ganaste un premio; envía tu PIN.', 70, 'ESTAFA', 'desconocido'],
            ['SMS: gana un premio urgente, entra https://bit.ly/promo', 55, 'SOSPECHOSO', 'sms'],
            ['WhatsApp: este es mi nuevo número, necesito el código de verificación.', 60, 'SOSPECHOSO', 'whatsapp'],
            ['Asunto: aviso importante. Remitente: equipo@example.com', 0, 'SEGURO', 'correo'],
            ['Aviso: http://oferta.xyz y https://bit.ly/ahora', 55, 'SOSPECHOSO', 'desconocido'],
            ['Urgente, ganaste un premio; envía tu contraseña. http://oferta.xyz https://bit.ly/x', 100, 'ESTAFA', 'desconocido'],
        ];

        foreach ($ejemplos as [$mensaje, $riesgo, $veredicto, $canal]) {
            $resultado = $motor->analizarTexto($mensaje);

            $this->assertSame($riesgo, $resultado['riesgo'], $mensaje);
            $this->assertSame($veredicto, $resultado['veredicto'], $mensaje);
            $this->assertSame($canal, $resultado['canal_detectado'], $mensaje);
            $this->assertArrayHasKey('banderas', $resultado);
            $this->assertArrayHasKey('explicacion', $resultado);
            $this->assertArrayHasKey('accion', $resultado);
            $this->assertArrayHasKey('enlaces_sospechosos', $resultado);
            $this->assertArrayNotHasKey('mensaje', $resultado);
        }
    }

    public function test_devuelve_solo_enlaces_sospechosos(): void
    {
        $resultado = (new MotorReglas)->analizarTexto(
            'Visita https://ejemplo.com y https://acortador.bit.ly/campana'
        );

        $this->assertSame(['https://acortador.bit.ly/campana'], $resultado['enlaces_sospechosos']);
        $this->assertContains('enlace acortado', $resultado['banderas']);
    }
}
