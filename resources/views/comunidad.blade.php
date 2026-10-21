<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Datos de la comunidad | Escudo AntiEstafas</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="barra">
        <div class="barra-interior">
            <a class="marca" href="{{ route('inicio') }}"><span class="marca-icono">✳</span>Escudo AntiEstafas</a>
            <nav class="navegacion" aria-label="Principal">
                <a class="enlace-juego" href="{{ route('inicio') }}">Volver al análisis</a>
                <a class="enlace-juego" href="{{ route('juego.inicio') }}">Jugar</a>
                <button class="boton-modo" id="alternar-modo" type="button" aria-pressed="false">Aa  Modo fácil de leer</button>
            </nav>
        </div>
    </header>

    <main class="contenedor">
        <section class="intro">
            <div>
                <p class="ceja">Aprender en comunidad</p>
                <h1>Señales que nos ayudan a cuidarnos.</h1>
                <p class="intro-texto">Estos conteos agrupados muestran qué señales conviene reconocer. No incluyen mensajes originales ni identifican a participantes.</p>
            </div>
            <aside class="sello"><strong>Datos anónimos</strong><span>Solo se muestran<br>cantidades agrupadas.</span></aside>
        </section>

        @if ($datosGraficas)
            @if ($esDemostracion)
                <div class="aviso aviso-demo" role="status"><strong>Datos de demostración:</strong> son cantidades ficticias para probar las gráficas; no representan respuestas reales de la comunidad.</div>
            @else
                <div class="aviso" role="status">Conteos agrupados registrados en la encuesta. Las gráficas no muestran respuestas individuales.</div>
            @endif

            <section class="tarjeta tarjeta-comunidad" aria-labelledby="pregunta-comunidad">
                <p class="ceja">Resumen de respuestas</p>
                <h2 id="pregunta-comunidad">{{ $pregunta }}</h2>
                <div class="rejilla-graficas">
                    <figure class="figura-grafica">
                        <figcaption>Respuestas acumuladas por señal</figcaption>
                        <div class="lienzo-grafica"><canvas id="grafica-respuestas" role="img" aria-label="Gráfica de respuestas acumuladas"></canvas></div>
                    </figure>
                    <figure class="figura-grafica">
                        <figcaption>Respuestas por fecha</figcaption>
                        <div class="lienzo-grafica"><canvas id="grafica-fechas" role="img" aria-label="Gráfica de respuestas agrupadas por fecha"></canvas></div>
                    </figure>
                </div>
                <p class="ayuda">Los valores son conteos agregados de la tabla encuesta_comunidad.</p>
            </section>
        @else
            <section class="tarjeta estado-vacio" role="status">
                <h2>Aún no hay datos para mostrar</h2>
                <p>Cuando se agreguen conteos agregados a la encuesta, las gráficas aparecerán aquí. Para una prueba local puedes cargar los datos ficticios con:</p>
                <code>php artisan db:seed --class=EncuestaComunidadSeeder</code>
            </section>
        @endif

        <footer class="pie">La información es educativa y orientativa; los conteos no predicen si un mensaje específico es seguro.</footer>
    </main>

    <script>
        window.datosComunidad = @json($datosGraficas);
    </script>
    <script src="{{ asset('js/vendor/chart.umd.min.js') }}" defer></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/comunidad.js') }}" defer></script>
</body>
</html>
