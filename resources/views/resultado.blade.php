<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resultado del análisis | Escudo AntiEstafas</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="barra">
        <div class="barra-interior">
            <a class="marca" href="{{ route('inicio') }}"><span class="marca-icono">✳</span>Escudo AntiEstafas</a>
            <button class="boton-modo" id="alternar-modo" type="button" aria-pressed="false">Aa  Modo fácil de leer</button>
        </div>
    </header>

    <main class="contenedor">
        @php
            $nivel = strtolower($resultado['veredicto']);
        @endphp

        <div class="resultado-cabecera">
            <div class="resultado-titulo">
                <p class="ceja">Ya revisamos el mensaje</p>
                <h1>Este es el resultado.</h1>
                <p class="intro-texto">Tómalo como una guía. Si algo no te da confianza, confirma con la institución usando su número o sitio oficial.</p>
            </div>
        </div>

        <section class="resumen" aria-labelledby="titulo-veredicto">
            <div>
                <p class="ceja">Nivel de riesgo</p>
                <h2 id="titulo-veredicto" class="nivel">{{ $resultado['veredicto'] }}</h2>
                <p class="puntaje">Riesgo: {{ $resultado['riesgo'] }} de 100 puntos · {{ $metodo === 'gemini' ? 'Análisis con IA' : 'Revisión básica' }}</p>
                <div class="audio">
                    <button class="boton" id="leer-veredicto" type="button" data-veredicto="{{ $resultado['veredicto'] }}">▶ Escuchar el resultado</button>
                    <p class="audio-estado" id="estado-audio" role="status" aria-live="polite"></p>
                </div>
            </div>
            <div class="semaforo {{ $nivel }}" role="img" aria-label="Semáforo de riesgo: {{ $resultado['veredicto'] }}">
                <span class="luz rojo"></span>
                <span class="luz amarillo"></span>
                <span class="luz verde"></span>
            </div>
        </section>

        <div class="rejilla-detalle">
            <section class="tarjeta" aria-labelledby="titulo-banderas">
                <p class="ceja">Las pistas</p>
                <h2 id="titulo-banderas">Señales detectadas</h2>
                @if (count($resultado['banderas']) > 0)
                    <ul class="banderas">
                        @foreach ($resultado['banderas'] as $bandera)
                            <li>{{ $bandera }}</li>
                        @endforeach
                    </ul>
                @else
                    <p>No se detectaron señales específicas.</p>
                @endif
            </section>

            <section class="tarjeta" aria-labelledby="titulo-explicacion">
                <p class="ceja">En palabras sencillas</p>
                <h2 id="titulo-explicacion">¿Por qué?</h2>
                <p>{{ $resultado['explicacion'] }}</p>
            </section>
        </div>

        <section class="tarjeta accion" aria-labelledby="titulo-accion">
            <p class="ceja">Un paso seguro</p>
            <h2 id="titulo-accion">Qué puedes hacer</h2>
            <p>{{ $resultado['accion'] }}</p>
        </section>

        @if (count($resultado['detalle_enlaces']) > 0)
            <section class="tarjeta enlaces" aria-labelledby="titulo-inspector">
                <p class="ceja">Revisión local de la dirección</p>
                <h2 id="titulo-inspector">Inspector de enlaces</h2>
                <p>Revisamos la forma del enlace sin abrirlo. Una alerta es una señal para tener cuidado; no confirma por sí sola que el sitio sea una estafa.</p>
                <ul>
                    @foreach ($resultado['detalle_enlaces'] as $detalle)
                        <li>
                            <strong>{{ $detalle['url'] }}</strong>
                            @if ($detalle['sospechoso'])
                                <ul>
                                    @foreach ($detalle['mensajes'] as $mensajeEnlace)
                                        <li>{{ $mensajeEnlace }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="muted">No vimos estas señales en su estructura. Esto no garantiza que el sitio sea seguro.</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
                <p class="muted">No visitamos los sitios ni consultamos Google Safe Browsing.</p>
            </section>
        @endif

        @if (count($resultado['enlaces_sospechosos']) > 0)
            <section class="tarjeta enlaces" aria-labelledby="titulo-enlaces">
                <p class="ceja">Señalados por el análisis</p>
                <h2 id="titulo-enlaces">Otros enlaces que requieren precaución</h2>
                <ul>
                    @foreach ($resultado['enlaces_sospechosos'] as $enlace)
                        <li>{{ $enlace }}</li>
                    @endforeach
                </ul>
            </section>
        @endif

        <p class="nota-privacidad muted">El resumen anónimo quedó guardado. El texto original y la captura no se guardaron.</p>
        <a class="boton" href="{{ route('inicio') }}">← Revisar otro mensaje</a>
        <footer>Escudo AntiEstafas · Tu seguridad va primero.</footer>
    </main>
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
