# Escudo AntiEstafas

Aplicación web académica para identificar posibles estafas en mensajes y explicar el riesgo con un semáforo claro. El proyecto apoya la protección de personas adultas mayores y se relaciona con el ODS 16.

## Requisitos

- PHP 8.2 o superior con extensiones de Laravel
- Composer
- MySQL
- Node.js y npm para compilar los estilos de Tailwind

Este inicio usa Laravel 12, compatible con el PHP 8.2 instalado en el equipo.

## Preparar el proyecto

1. Instalar las dependencias de PHP con `composer install`.
2. Copiar `.env.example` como `.env` si todavía no existe.
3. Completar los datos de MySQL y las variables de Gemini en `.env`.
4. Generar la clave de la aplicación con `php artisan key:generate`.
5. Crear la base de datos `escudo_antiestafas` en MySQL.
6. Crear las tablas con `php artisan migrate`.
7. Iniciar el servidor con `php artisan serve`.

Las variables `GEMINI_API_KEY` y `GEMINI_MODEL` se mantienen en `.env`; no se guardan claves en Git. Configura un nombre de modelo habilitado para tu cuenta. Si falta la clave, hay un error de conexión o Gemini devuelve una respuesta incompleta, el sistema usa el motor de reglas; para una imagen sin texto, el resultado de respaldo es cauteloso porque las reglas offline no pueden leer capturas.

El inspector de URLs revisa localmente los enlaces escritos en el texto para detectar acortadores, terminaciones poco habituales y direcciones HTTP. No abre los enlaces ni confirma si un sitio es seguro. Google Safe Browsing queda como una integración opcional futura.

## Datos guardados

- `analisis` conserva el hash SHA-256, canal, riesgo, veredicto, explicación, acción, método y fecha.
- `banderas_detectadas` conserva nombres de señales, sin fragmentos del mensaje original.
- `encuesta_comunidad` guarda conteos agregados por pregunta, respuesta y fecha, sin identificar participantes.

El texto original y las imágenes no se guardan.

## Estado

- Paso 0: proyecto Laravel inicializado.
- Paso 1: tablas de análisis anónimo, banderas y encuesta comunal preparadas.
- Paso 2: motor de reglas offline con ejemplos de prueba.
- Paso 3: formulario para texto e imágenes con validación y vista previa local.
- Paso 4: integración multimodal con Gemini y respaldo al motor de reglas.
- Paso 5: semáforo de resultados y guardado del resumen anonimizado.
- Paso 6: modo fácil de leer y lectura en voz alta del veredicto.
- Paso 7: inspector local de URLs con señales de acortadores y dominios sospechosos.
- Paso 8: comparación lado a lado de Gemini y el motor de reglas.
