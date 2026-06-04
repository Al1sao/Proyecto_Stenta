<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stentitos - Monitor de Temperaturas</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body id="home">
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
        <div class="content">
            <a class="cam-div" href="historial.php?id_camara=1">
                <div id="cam1" class="cam-card" data-camara="1">
                    <h1>CAMARA 1</h1>
                    <p class="temp-valor">--°C</p>
                    <span class="temp-status">Cargando...</span>
                </div>
            </a>
            <div class="cam-div">
                <div class="cam-card sin-sensor">
                    <h1>CAMARA 2</h1>
                    <p class="temp-valor">--°C</p>
                    <span class="temp-status">Sin sensor</span>
                </div>
            </div>
            <div class="cam-div">
                <div class="cam-card sin-sensor">
                    <h1>CAMARA 3</h1>
                    <p class="temp-valor">--°C</p>
                    <span class="temp-status">Sin sensor</span>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="cam-div">
                <div class="cam-card sin-sensor">
                    <h1>CAMARA 4</h1>
                    <p class="temp-valor">--°C</p>
                    <span class="temp-status">Sin sensor</span>
                </div>
            </div>
            <div class="cam-div">
                <div class="cam-card sin-sensor">
                    <h1>CAMARA 5</h1>
                    <p class="temp-valor">--°C</p>
                    <span class="temp-status">Sin sensor</span>
                </div>
            </div>
            <div class="cam-div">
                <div class="cam-card sin-sensor">
                    <h1>CAMARA 6</h1>
                    <p class="temp-valor">--°C</p>
                    <span class="temp-status">Sin sensor</span>
                </div>
            </div>
        </div>
    </section>
    <footer>
        <h3>STENTITOS</h3>
    </footer>
    <script>
        const API = 'https://proyectostenta-production.up.railway.app';

        async function actualizarTemperatura(idCamara) {
            try {
                const res = await fetch(`${API}/ActualTemp.php?id_camara=${idCamara}`);
                const data = await res.json();
                const card = document.querySelector(`.cam-card[data-camara="${idCamara}"]`);
                if (!card) return;
                if (data && data.valor !== undefined) {
                    card.querySelector('.temp-valor').textContent = parseFloat(data.valor).toFixed(1) + '°C';
                    card.querySelector('.temp-status').textContent = '✓ En línea';
                    card.classList.remove('sin-sensor');
                    card.classList.add('online');
                } else {
                    card.querySelector('.temp-status').textContent = 'Sin datos';
                }
            } catch (e) {
                console.error('Error camara', idCamara, e);
            }
        }

        actualizarTemperatura(1);
        setInterval(() => actualizarTemperatura(1), 5000);
    </script>
</body>
</html>
