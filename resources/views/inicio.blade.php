<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Escudo AntiEstafas</title>
    <style>
        :root { color-scheme: light; font-family: Arial, Helvetica, sans-serif; color: #16324f; background: #f3f7fb; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; }
        header { padding: 22px max(24px, calc((100% - 960px) / 2)); background: #123b5d; color: #fff; }
        header strong { font-size: 1.25rem; }
        main { width: min(100% - 32px, 760px); margin: 42px auto; }
        .intro { margin-bottom: 24px; }
        h1 { margin: 0 0 10px; font-size: clamp(1.8rem, 5vw, 2.5rem); }
        p { line-height: 1.55; }
        .card { padding: clamp(20px, 5vw, 36px); border: 1px solid #d4e0ea; border-radius: 18px; background: #fff; box-shadow: 0 12px 32px #16324f12; }
        label, legend { display: block; margin-bottom: 10px; font-weight: 700; }
        textarea { display: block; width: 100%; min-height: 180px; padding: 14px; resize: vertical; border: 2px solid #9bb2c5; border-radius: 10px; font: inherit; line-height: 1.5; }
        textarea:focus, input:focus, button:focus { outline: 3px solid #f5c451; outline-offset: 2px; }
        .hint { margin: 8px 0 24px; color: #4b6479; font-size: .95rem; }
        input[type=file] { width: 100%; padding: 12px; border: 2px dashed #9bb2c5; border-radius: 10px; background: #f8fbfd; font: inherit; }
        #vista-previa { display: none; max-width: 100%; max-height: 360px; margin-top: 16px; border: 1px solid #d4e0ea; border-radius: 10px; }
        button { width: 100%; margin-top: 26px; padding: 15px 20px; border: 0; border-radius: 10px; background: #116149; color: #fff; font: inherit; font-weight: 700; cursor: pointer; }
        button:hover { background: #0b4d3a; }
        .notice, .error-box { margin-bottom: 20px; padding: 14px 16px; border-radius: 10px; }
        .notice { border: 1px solid #91c9ae; background: #effaf3; color: #155c35; }
        .error-box { border: 1px solid #df9b9b; background: #fff3f3; color: #8a2020; }
        .error-box ul { margin: 8px 0 0; padding-left: 22px; }
        footer { margin: 24px 0; color: #4b6479; font-size: .9rem; text-align: center; }
    </style>
</head>
<body>
    <header><strong>🛡️ Escudo AntiEstafas</strong></header>
    <main>
        <section class="intro">
            <h1>Revisa un mensaje sospechoso</h1>
            <p>Pega el texto o selecciona una captura de WhatsApp, SMS o correo. En este paso verificamos el formulario; el análisis se agregará después.</p>
        </section>

        @if (isset($mensajeEstado))
            <div class="notice" role="status">{{ $mensajeEstado }}</div>
        @endif

        @if (isset($errores) && $errores->any())
            <div class="error-box" role="alert" aria-labelledby="titulo-errores">
                <strong id="titulo-errores">Revisa estos datos:</strong>
                <ul>
                    @foreach ($errores->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="card" method="POST" action="{{ route('analisis.validar') }}" enctype="multipart/form-data">
            @csrf
            <label for="mensaje">Texto del mensaje</label>
            <textarea id="mensaje" name="mensaje" maxlength="10000" placeholder="Pega aquí el mensaje que quieres revisar…" aria-describedby="ayuda-mensaje"></textarea>
            <p class="hint" id="ayuda-mensaje">No pedimos ni guardamos contraseñas, PIN, tokens, números de tarjeta o teléfonos.</p>

            <label for="imagen">O selecciona una captura</label>
            <input id="imagen" name="imagen" type="file" accept="image/png,image/jpeg,image/webp">
            <p class="hint">Formatos permitidos: PNG, JPG o WebP. Tamaño máximo: 5 MB. La vista previa se muestra en este dispositivo.</p>
            <img id="vista-previa" alt="Vista previa de la imagen seleccionada">

            <button type="submit">Continuar</button>
        </form>
        <footer>El mensaje y la imagen no se conservan después de validar el formulario.</footer>
    </main>

    <script>
        const selectorImagen = document.getElementById('imagen');
        const vistaPrevia = document.getElementById('vista-previa');
        let urlPrevia = null;

        selectorImagen.addEventListener('change', function () {
            if (urlPrevia) {
                URL.revokeObjectURL(urlPrevia);
                urlPrevia = null;
            }

            const archivo = selectorImagen.files[0];
            if (!archivo) {
                vistaPrevia.removeAttribute('src');
                vistaPrevia.style.display = 'none';
                return;
            }

            urlPrevia = URL.createObjectURL(archivo);
            vistaPrevia.src = urlPrevia;
            vistaPrevia.style.display = 'block';
        });
    </script>
</body>
</html>
