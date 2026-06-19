<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial - Stentitos</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header>
        <nav>
            <div id="icon">
                <a href="inicio.php"><img src="logo.png" alt="Logo"></a>
            </div>
            <div class="sec-link">
                <div class="header-link"><a href="inicio.php">Home</a></div>
                <div class="header-link"><a href="historial.php">Historial</a></div>
            </div>
        </nav>
    </header>

    <section>
        <div id="his">
            <div class="content" id="his-1">
                <h1>TEMPERATURA ACTUAL</h1>
                <h2 id="temp-actual">--°C</h2>
                <p id="status">Cargando...</p>
                <p id="ultima-actualizacion" style="font-size:13px;color:#888;margin-top:8px;"></p>
            </div>
            <div class="content" id="his-2">
                <h1 id="week">Historial Semanal - Cámara 1</h1><br>
                <div id="loading-chart" style="text-align:center;padding:40px;color:#888;">Cargando datos...</div>
                <canvas id="temp" style="display:none;"></canvas>
            </div>
        </div>
    </section>

    <footer>
        <h3>STENTITOS</h3>
    </footer>

    <script>
        const API = 'https://proyectostenta-production.up.railway.app';
        const ID_CAMARA = new URLSearchParams(window.location.search).get('id_camara') || 1;
        let chart = null;

        async function cargarTempActual() {
            try {
                const res = await fetch(`${API}/ActualTemp.php?id_camara=${ID_CAMARA}`);
                const data = await res.json();
                if (data && data.valor !== undefined) {
                    const val = parseFloat(data.valor).toFixed(1);
                    document.getElementById('temp-actual').textContent = val + '°C';
                    document.getElementById('ultima-actualizacion').textContent =
                        `Última lectura: ${data.fecha} ${data.hora}`;

                    const h2 = document.getElementById('temp-actual');
                    if (val > 30) {
                        h2.style.color = '#e05050';
                        document.getElementById('status').textContent = 'Temperatura alta!!!';
                    } else if (val < -55) {
                        h2.style.color = '#5080e0';
                        document.getElementById('status').textContent = 'Temperatura baja!!!';
                    } else {
                        h2.style.color = '#48989b';
                        document.getElementById('status').textContent = '✓ Temperatura normal';
                    }
                }
            } catch (e) {
                document.getElementById('status').textContent = 'Error al cargar';
            }
        }

        async function cargarHistorial() {
            try {
                const res = await fetch(`${API}/HistorialSemanal.php?id_camara=${ID_CAMARA}`);
                const data = await res.json();

                document.getElementById('loading-chart').style.display = 'none';
                document.getElementById('temp').style.display = 'block';

                if (!data || data.length === 0) {
                    document.getElementById('loading-chart').style.display = 'block';
                    document.getElementById('loading-chart').textContent = 'Sin datos en la ultima semana.';
                    document.getElementById('temp').style.display = 'none';
                    return;
                }

                // Agrupar por día
                const porDiaProm = {};
                const porDiaMax = {};
                const porDiaMin = {};

                data.forEach(row => {
                    const dia = row.fecha;
                    if (!porDiaProm[dia]) {
                        porDiaProm[dia] = [];
                        porDiaMax[dia] = [];
                        porDiaMin[dia] = [];
                    }
                    porDiaProm[dia].push(parseFloat(row.valor));
                    porDiaMax[dia].push(parseFloat(row.valor));
                    porDiaMin[dia].push(parseFloat(row.valor));
                });

                const labels = Object.keys(porDiaProm).map(f => {
                    const d = new Date(f + 'T00:00:00');
                    return d.toLocaleDateString('es-AR', { weekday: 'short' });
                });

                const valoresProm = Object.values(porDiaProm).map(arr =>
                    (arr.reduce((a, b) => a + b, 0) / arr.length).toFixed(1)
                );
                const valoresMax = Object.values(porDiaMax).map(arr =>
                    Math.max(...arr).toFixed(1)
                );
                const valoresMin = Object.values(porDiaMin).map(arr =>
                    Math.min(...arr).toFixed(1)
                );

                const ctx = document.getElementById('temp');
                if (chart) chart.destroy();
                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Promedio',
                                data: valoresProm,
                                borderColor: '#173a4b',
                                backgroundColor: 'rgba(23,58,75,0.1)',
                                borderWidth: 3,
                                tension: 0.3,
                                pointBackgroundColor: '#173a4b',
                                pointRadius: 4,
                            },
                            {
                                label: 'Mínimo',
                                data: valoresMin,
                                borderColor: '#2c569d',
                                backgroundColor: 'rgba(44,86,157,0.1)',
                                borderWidth: 3,
                                tension: 0.3,
                                pointBackgroundColor: '#2c569d',
                                pointRadius: 4,
                            },
                            {
                                label: 'Máximo',
                                data: valoresMax,
                                borderColor: '#d84b4b',
                                backgroundColor: 'rgba(216,75,75,0.1)',
                                borderWidth: 3,
                                tension: 0.3,
                                pointBackgroundColor: '#d84b4b',
                                pointRadius: 4,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: true }
                        },
                        scales: {
                            x: { title: { display: true, text: 'Día' } },
                            y: { title: { display: true, text: 'Temperatura °C' } }
                        }
                    }
                });
            } catch (e) {
                document.getElementById('loading-chart').textContent = 'Error al cargar el historial.';
            }
        }

        cargarTempActual();
        cargarHistorial();
        setInterval(cargarTempActual, 5000);
        setInterval(cargarHistorial, 60000);
    </script>
</body>
</html>