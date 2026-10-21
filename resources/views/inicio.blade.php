<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Escudo AntiEstafas | Revisa con calma</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="barra">
        <div class="barra-interior">
            <a class="marca" href="{{ route('inicio') }}"><span class="marca-icono">✳</span>Escudo AntiEstafas</a>
            <nav class="navegacion" aria-label="Principal">
                <a class="enlace-juego" href="{{ route('juego.inicio') }}">Jugar: ¿Real o estafa?</a>
                <a class="enlace-juego" href="{{ route('comunidad.inicio') }}">Datos de la comunidad</a>
                <button class="boton-modo" id="alternar-modo" type="button" aria-pressed="false">Aa  Modo fácil de leer</button>
            </nav>
        </div>
    </header>

    <main class="contenedor">
        <section class="intro">
            <div>
                <p class="ceja">Un momento antes de responder</p>
                <h1>Revisemos ese mensaje con calma.</h1>
                <p class="intro-texto">Pega el texto o elige una captura de WhatsApp, SMS o correo. Te ayudamos a identificar señales de alerta y qué hacer después.</p>
            </div>
            <aside class="sello" aria-label="Consejo de seguridad">
                <strong>Tu seguridad<br>va primero</strong>
                <span>Si tienes dudas, no respondas todavía.</span>
            </aside>
        </section>

        @if (isset($mensajeEstado))
            <div class="aviso" role="status">{{ $mensajeEstado }}</div>
        @endif

        @if (isset($errores) && $errores->any())
            <div class="errores" role="alert" aria-labelledby="titulo-errores">
                <strong id="titulo-errores">Revisa estos datos:</strong>
                <ul>
                    @foreach ($errores->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="tarjeta formulario" method="POST" action="{{ route('analisis.validar') }}" enctype="multipart/form-data">
            @csrf
            <div class="campo campo-texto">
                <label for="mensaje">¿Qué dice el mensaje?</label>
                <textarea id="mensaje" name="mensaje" maxlength="10000" placeholder="Pega aquí el mensaje que quieres revisar…" aria-describedby="ayuda-mensaje"></textarea>
                <p class="ayuda" id="ayuda-mensaje">No compartas datos personales. No guardamos el texto ni la captura; el texto se envía a Gemini para analizarlo.</p>
            </div>

            <div class="campo">
                <label for="imagen">También puedes elegir una captura</label>
                <input id="imagen" name="imagen" type="file" accept="image/png,image/jpeg,image/webp">
                <p class="ayuda">PNG, JPG o WebP. Máximo 5 MB.</p>
                <img id="vista-previa" alt="Vista previa de la imagen seleccionada">
            </div>

            <div class="campo">
                <p class="ceja">Tómate tu tiempo</p>
                <p class="ayuda">Ningún banco ni institución debe presionarte para compartir una contraseña, PIN o código de seguridad.</p>
            </div>

            <div class="acciones-formulario">
                <button class="boton" type="submit">Revisar mensaje <span aria-hidden="true">→</span></button>
                <span class="ayuda">El análisis puede tardar unos segundos.</span>
            </div>
        </form>

        <footer class="pie">Escudo AntiEstafas · Una herramienta orientativa para ayudarte a decidir con más confianza.</footer>
    </main>
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
