const botonModo = document.getElementById('alternar-modo');
const raiz = document.documentElement;
const claveModo = 'escudo-modo-facil';

function actualizarModo(activar) {
    raiz.classList.toggle('modo-facil', activar);
    botonModo.setAttribute('aria-pressed', activar ? 'true' : 'false');
    botonModo.textContent = activar ? 'Aa  Modo fácil: activado' : 'Aa  Modo fácil de leer';

    try {
        localStorage.setItem(claveModo, activar ? 'activado' : 'desactivado');
    } catch (error) {
        // El modo funciona aunque el navegador no permita guardar preferencias.
    }
}

try {
    actualizarModo(localStorage.getItem(claveModo) === 'activado');
} catch (error) {
    actualizarModo(false);
}

botonModo.addEventListener('click', function () {
    actualizarModo(!raiz.classList.contains('modo-facil'));
});

const selectorImagen = document.getElementById('imagen');
const vistaPrevia = document.getElementById('vista-previa');

if (selectorImagen && vistaPrevia) {
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
}

const botonAudio = document.getElementById('leer-veredicto');
if (botonAudio) {
    botonAudio.addEventListener('click', function () {
        const estadoAudio = document.getElementById('estado-audio');

        if (!('speechSynthesis' in window) || !('SpeechSynthesisUtterance' in window)) {
            estadoAudio.textContent = 'Este navegador no permite leer en voz alta.';
            return;
        }

        window.speechSynthesis.cancel();
        const veredicto = botonAudio.dataset.veredicto;
        const mensaje = new SpeechSynthesisUtterance('El resultado es: ' + veredicto + '.');
        mensaje.lang = 'es-SV';
        mensaje.rate = 0.9;
        mensaje.onstart = function () { estadoAudio.textContent = 'Leyendo el veredicto…'; };
        mensaje.onend = function () { estadoAudio.textContent = 'Lectura terminada.'; };
        mensaje.onerror = function () { estadoAudio.textContent = 'No se pudo iniciar la lectura en voz alta.'; };

        const voces = window.speechSynthesis.getVoices();
        for (const voz of voces) {
            if (voz.lang.toLowerCase().startsWith('es')) {
                mensaje.voice = voz;
                break;
            }
        }

        window.speechSynthesis.speak(mensaje);
    });
}
