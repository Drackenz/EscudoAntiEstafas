<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class JuegoController extends Controller
{
    public function inicio(): View
    {
        $casos = [
            [
                'mensaje' => '“Tu cuenta de Banco Agrícola será suspendida hoy. Confirma tu contraseña en este enlace para evitar el bloqueo.”',
                'es_estafa' => true,
                'explicacion' => 'Es una señal de alerta: combina urgencia, amenaza de bloqueo y una solicitud de contraseña mediante un enlace. No lo abras. Entra a la app oficial por tu cuenta o llama al número que aparece en tu tarjeta.',
                'tema' => 'Banco y urgencia',
            ],
            [
                'mensaje' => '“Recibiste un aviso de tu banco. Para revisar movimientos, abre la aplicación que ya tienes instalada o comunícate con el número oficial de tu tarjeta. Nunca compartas tu PIN ni códigos.”',
                'es_estafa' => false,
                'explicacion' => 'El ejemplo evita enlaces y pide usar un canal oficial que tú iniciaste; además, recuerda que no debes compartir claves. Aun así, este caso es ficticio y no confirma la autenticidad de ningún mensaje real.',
                'tema' => 'Aviso bancario',
            ],
            [
                'mensaje' => '“Tigo: ganaste un teléfono. Paga hoy el envío y envíanos el código de seis dígitos que te llegó por SMS para reclamarlo.”',
                'es_estafa' => true,
                'explicacion' => 'Pedir un código de verificación o un pago inesperado para entregar un premio es peligroso. No compartas códigos; consulta promociones solo desde los canales oficiales que busques tú mismo.',
                'tema' => 'Premio y código',
            ],
            [
                'mensaje' => '“Recordatorio: tu paquete está listo para recoger. Revisa el número de seguimiento en la aplicación o página oficial de la empresa de paquetería que utilizaste.”',
                'es_estafa' => false,
                'explicacion' => 'Un aviso puede ser legítimo cuando esperas un envío y verificas el seguimiento desde el canal oficial. El texto por sí solo no prueba quién lo envió; evita usar enlaces inesperados.',
                'tema' => 'Seguimiento de paquete',
            ],
            [
                'mensaje' => '“Claro: tu línea será desactivada en 30 minutos. Instala esta aplicación de soporte remoto y dicta al agente el código que aparecerá.”',
                'es_estafa' => true,
                'explicacion' => 'La presión para actuar rápido, instalar acceso remoto y dictar códigos puede permitir que otra persona controle tu dispositivo o tus cuentas. Cuelga y contacta a la compañía por un canal oficial.',
                'tema' => 'Soporte remoto',
            ],
            [
                'mensaje' => '“La aplicación Chivo Wallet muestra una actualización disponible. Si deseas revisar tu cuenta, abre la aplicación instalada desde tu teléfono; nunca compartas tu frase de recuperación ni PIN.”',
                'es_estafa' => false,
                'explicacion' => 'El consejo de seguridad es abrir la aplicación por cuenta propia y proteger las credenciales. Es un caso ficticio: no verifiques actualizaciones desde enlaces recibidos en mensajes.',
                'tema' => 'Billetera digital',
            ],
            [
                'mensaje' => '“Somos de la Policía. Hay una orden a tu nombre; transfiere dinero ahora para evitar el arresto y no le cuentes a nadie.”',
                'es_estafa' => true,
                'explicacion' => 'Una amenaza, el secreto y la exigencia de transferir dinero son señales fuertes de fraude. No pagues ni compartas datos; busca por tu cuenta los canales oficiales de la institución y pide apoyo a alguien de confianza.',
                'tema' => 'Falsa autoridad',
            ],
            [
                'mensaje' => '“Gracias por tu compra. Consulta el recibo entrando a la tienda desde su aplicación o escribiendo tú mismo la dirección oficial que ya conoces.”',
                'es_estafa' => false,
                'explicacion' => 'Consultar una compra desde una aplicación conocida o una dirección escrita por ti reduce el riesgo de caer en un enlace falso. Si no reconoces la compra, contacta a la tienda por su canal oficial.',
                'tema' => 'Recibo de compra',
            ],
            [
                'mensaje' => '“Actualiza los datos de tu cuenta Claro con tu DUI, tarjeta y clave en este formulario. Si no lo haces hoy, perderás tu número.”',
                'es_estafa' => true,
                'explicacion' => 'Solicita datos personales y financieros por un formulario, con amenaza y plazo corto. No respondas ni abras el enlace. Confirma directamente con el proveedor mediante un canal oficial.',
                'tema' => 'Datos personales',
            ],
            [
                'mensaje' => '“Tu solicitud fue recibida. Para comprobar su estado, visita la sucursal o llama al número publicado en el sitio oficial que tú busques.”',
                'es_estafa' => false,
                'explicacion' => 'El mensaje sugiere comprobar por un canal que tú localizas, sin pedir claves, pagos ni códigos. Los casos son ficticios; siempre verifica la identidad por separado.',
                'tema' => 'Consulta de trámite',
            ],
        ];

        return view('juego', compact('casos'));
    }
}
