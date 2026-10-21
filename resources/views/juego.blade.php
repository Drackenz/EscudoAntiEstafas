<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>¿Real o estafa? | Escudo AntiEstafas</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="barra">
        <div class="barra-interior">
            <a class="marca" href="{{ route('inicio') }}"><span class="marca-icono">✳</span>Escudo AntiEstafas</a>
            <nav class="navegacion" aria-label="Principal">
                <a class="enlace-juego" href="{{ route('inicio') }}">Volver al análisis</a>
                <a class="enlace-juego" href="{{ route('comunidad.inicio') }}">Comunidad</a>
                <button class="boton-modo" id="alternar-modo" type="button" aria-pressed="false">Aa  Modo fácil de leer</button>
            </nav>
        </div>
    </header>

    <main class="contenedor juego" id="juego" data-casos="{{ count($casos) }}">
        <section class="intro juego-intro">
            <div>
                <p class="ceja">Aprendamos a detectar señales</p>
                <h1>¿Real o estafa?</h1>
                <p class="intro-texto">Lee cada situación y decide con calma. Después verás qué señales conviene reconocer.</p>
            </div>
            <aside class="sello"><strong>Diez casos</strong><span>Un poco de práctica<br>puede hacer la diferencia.</span></aside>
        </section>

        <div class="nota-juego" role="note"><strong>Importante:</strong> todos los casos son ficticios y sirven para aprender. Ningún ejemplo confirma que un mensaje real sea auténtico. Si tienes dudas, verifica desde la aplicación, sitio o teléfono oficial que tú busques.</div>

        <section class="tarjeta tarjeta-juego" aria-labelledby="titulo-caso">
            <div class="juego-meta">
                <span class="etiqueta-caso" id="tema-caso"></span>
                <span id="progreso-texto">Caso 1 de {{ count($casos) }}</span>
                <span class="puntaje-juego">Puntos: <strong id="puntaje">0</strong></span>
            </div>
            <progress class="progreso-juego" id="progreso" max="{{ count($casos) }}" value="1" aria-label="Progreso del juego"></progress>
            <h2 class="titulo-caso" id="titulo-caso">Lee este mensaje</h2>
            <blockquote class="mensaje-caso" id="mensaje-caso"></blockquote>

            <div class="opciones-juego" id="opciones-juego">
                <button class="boton-opcion legitimo" type="button" data-respuesta="legitimo">Parece legítimo</button>
                <button class="boton-opcion estafa" type="button" data-respuesta="estafa">Parece una estafa</button>
            </div>
            <div class="explicacion-juego" id="explicacion" role="status" aria-live="polite" hidden></div>
            <div class="acciones-juego">
                <button class="boton" id="siguiente-caso" type="button" hidden>Siguiente caso <span aria-hidden="true">→</span></button>
            </div>
            <section class="final-juego" id="final-juego" aria-live="polite" hidden>
                <p class="ceja">Ronda terminada</p>
                <h2 id="resultado-final"></h2>
                <p id="consejo-final"></p>
                <button class="boton-secundario" id="jugar-de-nuevo" type="button">Jugar de nuevo</button>
            </section>
        </section>

        <footer class="pie">El puntaje solo dura mientras esta página está abierta; no se guarda ni se envía.</footer>
    </main>

    <script>
        window.casosEducativos = @json($casos);
    </script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/juego.js') }}" defer></script>
</body>
</html>
