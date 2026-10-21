# Escudo AntiEstafas

Aplicación web académica para practicar la identificación de posibles estafas en mensajes de texto o capturas. Presenta un nivel de riesgo, señales observadas y una recomendación en lenguaje sencillo. Está pensada para apoyar la alfabetización digital y la protección de personas adultas mayores.

El proyecto aporta a la prevención y al acceso a información clara, en línea con el **ODS 16: Paz, justicia e instituciones sólidas**. Es una herramienta orientativa: no certifica la identidad del remitente ni reemplaza la consulta directa con una institución.

## Funciones

- Análisis de texto e imágenes PNG, JPG y WebP con Gemini cuando está configurado, y respaldo con reglas locales.
- Semáforo de riesgo, señales, explicación y acciones recomendadas.
- Comparación del resultado de Gemini con el motor de reglas.
- Inspector local de URLs que identifica algunas señales sin abrir los enlaces.
- Modo fácil de leer y lectura en voz alta del veredicto.
- Juego educativo “¿Real o estafa?” con diez casos ficticios.
- Módulo comunal con gráficas de conteos agregados y datos de demostración opcionales.

## Tecnologías y requisitos

- PHP 8.2 o superior y Composer.
- Laravel 12.
- MySQL.
- Node.js y npm para gestionar la dependencia Chart.js. El archivo del navegador está incluido en el repositorio.
- Navegador moderno con JavaScript habilitado. La lectura en voz alta depende de la disponibilidad de Web Speech API del navegador.

La interfaz usa Blade y CSS del proyecto. Chart.js 4.5.1 dibuja las gráficas comunales.

## Instalación local

1. Clona el repositorio y entra en la carpeta del proyecto.
2. Instala dependencias PHP:

   ```powershell
   composer install
   ```

3. Crea el archivo local de configuración y la clave de Laravel:

   ```powershell
   Copy-Item .env.example .env
   php artisan key:generate
   ```

4. Crea la base de datos `escudo_antiestafas` en MySQL. Completa en `.env` los valores de `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD`.
5. Instala las dependencias de JavaScript:

   ```powershell
   npm install
   ```

6. Ejecuta las migraciones:

   ```powershell
   php artisan migrate
   ```

7. Inicia el servidor:

   ```powershell
   php artisan serve
   ```

Abre `http://127.0.0.1:8000` en el navegador.

## Variables de entorno

| Variable | Uso |
| --- | --- |
| `APP_KEY` | Clave local de Laravel; se genera con `php artisan key:generate`. |
| `APP_ENV` | Entorno de ejecución; usa `local` durante el desarrollo. |
| `APP_DEBUG` | Detalles de errores; mantenlo activo solo en desarrollo y desactívalo al desplegar. |
| `APP_URL` | Dirección de la aplicación local o desplegada. |
| `DB_HOST`, `DB_PORT` | Servidor y puerto de MySQL. |
| `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Base de datos y credenciales de MySQL. |
| `GEMINI_API_KEY` | Clave privada de Gemini. Déjala vacía para usar las reglas locales. |
| `GEMINI_MODEL` | Nombre de un modelo habilitado para la clave configurada. No está fijado en el código. |

La clave de Gemini se configura únicamente en `.env`. No la escribas en el código, README, capturas, pruebas ni mensajes de Git. `.env` está ignorado por Git; `.env.example` contiene valores vacíos o de ejemplo.

## Privacidad y seguridad

- El texto original y la imagen no se guardan en la base de datos ni en archivos de la aplicación. `analisis` conserva un hash SHA-256 y un resumen; `banderas_detectadas` conserva nombres de señales. La encuesta comunal guarda únicamente pregunta, respuesta agregada, cantidad y fecha.
- Cuando `GEMINI_API_KEY` y `GEMINI_MODEL` están configurados, el texto y/o la imagen se envían a la API de Gemini para el análisis. No incluyas contraseñas, PIN, códigos, tarjetas, teléfonos ni otros datos personales en mensajes o capturas. La aplicación oculta patrones de datos sensibles en el resumen antes de guardarlo, pero esa limpieza no reemplaza la precaución del usuario.
- El motor de reglas y el inspector de URLs funcionan localmente. El inspector no visita los enlaces ni confirma que un sitio sea seguro.
- El puntaje del juego solo permanece en la página abierta y no se envía ni se almacena.
- Los registros del seeder están identificados con `[DEMO]`, son ficticios y se excluyen cuando existen conteos no demostrativos.
- En desarrollo local se usa `APP_DEBUG=true`; para un despliegue configura `APP_ENV=production`, `APP_DEBUG=false`, HTTPS y credenciales privadas fuera del repositorio.

## Rutas de la aplicación

- `/` — formulario de análisis.
- `/juego` — juego “¿Real o estafa?”.
- `/comunidad` — gráficas comunales.

## Datos de demostración

Para cargar o reemplazar los conteos ficticios en una base local:

```powershell
php artisan db:seed --class=EncuestaComunidadSeeder
```

El módulo muestra una etiqueta visible cuando presenta esos datos. Cuando hay registros reales, usa esos registros en su lugar. No cargues datos de demostración en una base de producción.

## Pruebas

```powershell
php artisan test
```

Las pruebas cubren el formulario, el motor de reglas, la validación de respuestas Gemini, el almacenamiento de resúmenes, el juego y el módulo comunal.

## Capturas para la exposición

La aplicación se puede mostrar localmente en estas pantallas:

1. **Inicio:** `http://127.0.0.1:8000/` — formulario, consejos y acceso al modo fácil de leer.
2. **Juego:** `http://127.0.0.1:8000/juego` — caso de práctica, opciones y explicación.
3. **Comunidad:** `http://127.0.0.1:8000/comunidad` — carga primero los datos de demostración y captura ambas gráficas.

Para añadir las imágenes al repositorio, guárdalas en `docs/capturas/` y enlázalas aquí con rutas relativas. Evita que las capturas incluyan claves, datos personales, mensajes reales o credenciales.

## Equipo

- Steven Bran
- Diego Zúñiga
- Jorge Villatoro
- Germán Sigarán

## Estado del proyecto

Los pasos 0 al 10 del plan de trabajo están implementados. La etiqueta `v1.0` identifica este cierre académico inicial; futuras mejoras pueden desarrollarse en nuevas ramas.
