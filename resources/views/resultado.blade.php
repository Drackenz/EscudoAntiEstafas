<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resultado del análisis | Escudo AntiEstafas</title>
    <style>
        :root { font-family: Arial, Helvetica, sans-serif; color: #16324f; background: #f3f7fb; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; }
        header { padding: 22px max(24px, calc((100% - 960px) / 2)); background: #123b5d; color: #fff; }
        header strong { font-size: 1.25rem; }
        main { width: min(100% - 32px, 760px); margin: 42px auto; }
        h1 { margin: 0 0 10px; font-size: clamp(1.8rem, 5vw, 2.5rem); }
        h2 { margin: 0 0 14px; font-size: 1.35rem; }
        p, li { line-height: 1.55; }
        .card { margin-top: 24px; padding: clamp(20px, 5vw, 36px); border: 1px solid #d4e0ea; border-radius: 18px; background: #fff; box-shadow: 0 12px 32px #16324f12; }
        .resumen { display: flex; flex-wrap: wrap; align-items: center; gap: 28px; }
        .semaforo { display: flex; flex-direction: column; gap: 8px; padding: 12px; border-radius: 18px; background: #17354c; }
        .luz { width: 42px; height: 42px; border-radius: 50%; background: #536675; border: 2px solid #dbe7ef; }
        .semaforo.seguro .verde { background: #35ad64; box-shadow: 0 0 0 4px #fff8; }
        .semaforo.sospechoso .amarillo { background: #efbb3d; box-shadow: 0 0 0 4px #fff8; }
        .semaforo.estafa .rojo { background: #dc4b4b; box-shadow: 0 0 0 4px #fff8; }
        .nivel { margin: 0 0 8px; font-size: 1.5rem; font-weight: 800; }
        .nivel.seguro { color: #176b39; }
        .nivel.sospechoso { color: #875600; }
        .nivel.estafa { color: #9f2020; }
        .puntaje { margin: 0; font-size: 1.2rem; font-weight: 700; }
        .muted { color: #4b6479; }
        .banderas { display: flex; flex-wrap: wrap; gap: 10px; padding: 0; list-style: none; }
        .banderas li { padding: 8px 12px; border-radius: 999px; background: #fff2dc; color: #704500; font-weight: 700; }
        .accion { border-left: 6px solid #d39a22; background: #fff9eb; }
        .enlaces { overflow-wrap: anywhere; }
        a { color: #075e9a; }
        .boton { display: inline-block; margin-top: 28px; padding: 14px 20px; border-radius: 10px; background: #116149; color: #fff; font-weight: 700; text-decoration: none; }
        .boton:focus { outline: 3px solid #f5c451; outline-offset: 3px; }
    </style>
</head>
<body>
    <header><strong>🛡️ Escudo AntiEstafas</strong></header>
    <main>
        <h1>Resultado del análisis</h1>
        <p class="muted">Este resultado es orientativo. Si tienes dudas, confirma siempre por un canal oficial.</p>

        @php
            $nivel = strtolower($resultado['veredicto']);
        @endphp

        <section class="card resumen" aria-labelledby="titulo-veredicto">
            <div class="semaforo {{ $nivel }}" role="img" aria-label="Semáforo de riesgo: {{ $resultado['veredicto'] }}">
                <span class="luz rojo"></span>
                <span class="luz amarillo"></span>
                <span class="luz verde"></span>
            </div>
            <div>
                <h2 id="titulo-veredicto" class="nivel {{ $nivel }}">{{ $resultado['veredicto'] }}</h2>
                <p class="puntaje">Riesgo: {{ $resultado['riesgo'] }} de 100</p>
                <p class="muted">Método: {{ $metodo === 'gemini' ? 'Gemini' : 'reglas offline' }} · Canal: {{ $resultado['canal_detectado'] }}</p>
            </div>
        </section>

        <section class="card" aria-labelledby="titulo-banderas">
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

        <section class="card" aria-labelledby="titulo-explicacion">
            <h2 id="titulo-explicacion">¿Por qué?</h2>
            <p>{{ $resultado['explicacion'] }}</p>
        </section>

        <section class="card accion" aria-labelledby="titulo-accion">
            <h2 id="titulo-accion">Qué hacer</h2>
            <p>{{ $resultado['accion'] }}</p>
        </section>

        @if (count($resultado['enlaces_sospechosos']) > 0)
            <section class="card enlaces" aria-labelledby="titulo-enlaces">
                <h2 id="titulo-enlaces">Enlaces que requieren precaución</h2>
                <ul>
                    @foreach ($resultado['enlaces_sospechosos'] as $enlace)
                        <li>{{ $enlace }}</li>
                    @endforeach
                </ul>
            </section>
        @endif

        <p class="muted">El resumen anónimo quedó guardado. El texto original y la captura no se guardaron.</p>
        <a class="boton" href="{{ route('inicio') }}">Analizar otro mensaje</a>
    </main>
</body>
</html>
