(function () {
    const casos = window.casosEducativos || [];
    const elemento = (id) => document.getElementById(id);
    const progreso = elemento('progreso');
    const botonesRespuesta = document.querySelectorAll('[data-respuesta]');

    if (!casos.length || !progreso || !botonesRespuesta.length) return;

    let indice = 0;
    let puntos = 0;

    function mostrarCaso() {
        const caso = casos[indice];
        elemento('tema-caso').textContent = caso.tema;
        elemento('progreso-texto').textContent = 'Caso ' + (indice + 1) + ' de ' + casos.length;
        progreso.value = indice + 1;
        elemento('titulo-caso').textContent = 'Lee este mensaje';
        elemento('mensaje-caso').textContent = caso.mensaje;
        elemento('puntaje').textContent = String(puntos);
        elemento('explicacion').hidden = true;
        elemento('siguiente-caso').hidden = true;
        elemento('opciones-juego').hidden = false;
        botonesRespuesta.forEach((boton) => { boton.disabled = false; });
    }

    function responder(respuesta) {
        const caso = casos[indice];
        const acerto = (respuesta === 'estafa') === caso.es_estafa;

        if (acerto) puntos += 1;
        elemento('puntaje').textContent = String(puntos);
        elemento('opciones-juego').hidden = true;
        const explicacion = elemento('explicacion');
        explicacion.className = 'explicacion-juego ' + (acerto ? 'respuesta-correcta' : 'respuesta-revisar');
        explicacion.innerHTML = '';
        const titulo = document.createElement('strong');
        titulo.textContent = acerto ? '¡Así es!' : 'Veamos las señales';
        const texto = document.createElement('p');
        texto.textContent = (caso.es_estafa ? 'Este ejemplo muestra señales de estafa. ' : 'Este ejemplo parece más seguro, aunque no se puede verificar solo con el texto. ') + caso.explicacion;
        explicacion.append(titulo, texto);
        explicacion.hidden = false;
        elemento('siguiente-caso').hidden = false;
    }

    function terminar() {
        elemento('progreso-texto').textContent = 'Completaste los ' + casos.length + ' casos';
        elemento('titulo-caso').hidden = true;
        elemento('mensaje-caso').hidden = true;
        progreso.value = casos.length;
        elemento('opciones-juego').hidden = true;
        elemento('explicacion').hidden = true;
        elemento('siguiente-caso').hidden = true;
        const final = elemento('final-juego');
        final.hidden = false;
        elemento('resultado-final').textContent = 'Tu puntaje: ' + puntos + ' de ' + casos.length;
        elemento('consejo-final').textContent = puntos === casos.length
            ? '¡Excelente! Sigue verificando por canales oficiales y nunca compartas claves ni códigos.'
            : 'Cada caso deja una pista para aprender. Ante la duda, detente y confirma por un canal oficial.';
    }

    botonesRespuesta.forEach((boton) => {
        boton.addEventListener('click', function () {
            if (!boton.disabled) {
                botonesRespuesta.forEach((opcion) => { opcion.disabled = true; });
                responder(boton.dataset.respuesta);
            }
        });
    });

    elemento('siguiente-caso').addEventListener('click', function () {
        indice += 1;
        if (indice >= casos.length) {
            terminar();
            return;
        }
        mostrarCaso();
    });

    elemento('jugar-de-nuevo').addEventListener('click', function () {
        indice = 0;
        puntos = 0;
        elemento('final-juego').hidden = true;
        elemento('titulo-caso').hidden = false;
        elemento('mensaje-caso').hidden = false;
        mostrarCaso();
    });

    mostrarCaso();
}());
