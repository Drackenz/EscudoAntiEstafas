(function () {
    const datos = window.datosComunidad;
    if (!datos || !datos.respuestas || typeof Chart === 'undefined') return;

    const colores = ['#17665c', '#dc815e', '#e6b84d', '#55839a', '#8a6a9e', '#7c9a66'];
    const opcionesComunes = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { padding: 18, usePointStyle: true } },
        },
    };

    const lienzoRespuestas = document.getElementById('grafica-respuestas');
    if (!lienzoRespuestas) return;

    new Chart(lienzoRespuestas, {
        type: 'doughnut',
        data: {
            labels: datos.respuestas.etiquetas,
            datasets: [{
                data: datos.respuestas.cantidades,
                backgroundColor: colores,
                borderColor: '#fffdf8',
                borderWidth: 3,
            }],
        },
        options: {
            ...opcionesComunes,
            plugins: {
                ...opcionesComunes.plugins,
                legend: { ...opcionesComunes.plugins.legend, position: 'right' },
            },
        },
    });

    const lienzoFechas = document.getElementById('grafica-fechas');
    if (!lienzoFechas) return;

    new Chart(lienzoFechas, {
        type: 'line',
        data: {
            labels: datos.fechas.etiquetas,
            datasets: [{
                label: 'Respuestas',
                data: datos.fechas.cantidades,
                borderColor: '#17665c',
                backgroundColor: '#17665c24',
                pointBackgroundColor: '#dc815e',
                pointRadius: 5,
                fill: true,
                tension: 0.25,
            }],
        },
        options: {
            ...opcionesComunes,
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 }, title: { display: true, text: 'Cantidad' } },
                x: { title: { display: true, text: 'Fecha' } },
            },
        },
    });
}());
