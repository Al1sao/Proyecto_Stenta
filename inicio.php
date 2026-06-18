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
                    <svg class="gauge" width="280" height="200"></svg>
                    <p class="temp-valor">--°C</p>
                    <span class="temp-status">Cargando...</span>
                </div>
            </a>
            <div class="cam-div">
                <div class="cam-card sin-sensor">
                    <h1>CAMARA 2</h1>
                    <svg class="gauge" width="280" height="200"></svg>
                    <p class="temp-valor">--°C</p>
                    <span class="temp-status">Sin sensor</span>
                </div>
            </div>
            <div class="cam-div">
                <div class="cam-card sin-sensor">
                    <h1>CAMARA 3</h1>
                    <svg class="gauge" width="280" height="200"></svg>
                    <p class="temp-valor">--°C</p>
                    <span class="temp-status">Sin sensor</span>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="cam-div">
                <div class="cam-card sin-sensor">
                    <h1>CAMARA 4</h1>
                    <svg class="gauge" width="280" height="200"></svg>
                    <p class="temp-valor">--°C</p>
                    <span class="temp-status">Sin sensor</span>
                </div>
            </div>
            <div class="cam-div">
                <div class="cam-card sin-sensor">
                    <h1>CAMARA 5</h1>
                    <svg class="gauge" width="280" height="200"></svg>
                    <p class="temp-valor">--°C</p>
                    <span class="temp-status">Sin sensor</span>
                </div>
            </div>
            <div class="cam-div">
                <div class="cam-card sin-sensor">
                    <h1>CAMARA 6</h1>
                    <svg class="gauge" width="280" height="200"></svg>
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
        function cargarGrafico(valor) {

            const LIMITE = 20;
            const svg = document.getElementsByClassName("gauge");
            const cx = 140;
            const cy = 140;
            const r = 100;
            const temperatura = Math.max(-LIMITE, Math.min(LIMITE, valor));
            const activo = tempToAngle(temperatura);
            const ticksMayor = [-20,-10,0,10,20];
            const ticksMenor = [-15,-5,5,15];
            function polar(angulo, radio){
                const rad = (angulo - 90) * Math.PI / 180;
                return {
                    x: cx + radio * Math.cos(rad),
                    y: cy + radio * Math.sin(rad)
                };
            }
            function arco(inicio, fin){
                const p1 = polar(inicio, r);
                const p2 = polar(fin, r);
                return `
                    M ${p1.x} ${p1.y}
                    A ${r} ${r}
                    0 0 1
                    ${p2.x} ${p2.y}
                `;
            }
            function tempToAngle(t){
                const clamped = Math.max(-LIMITE, Math.min(LIMITE, t));
                return -90 + ((clamped + LIMITE) / (LIMITE * 2)) * 180;
            }

            for (let i = 0; i < svg.length; i++) {
                const aguja = (i === 0) ? activo : tempToAngle(0);
                const camaraActiva = (i === 0) ? `
                    <path d="${arco(-90,activo)}"
                        fill="none"
                        stroke="url(#grad)"
                        stroke-width="14"/>
                ` : '';
                svg[i].innerHTML = `
                    <defs>
                        <linearGradient id="grad">
                            <stop offset="0%" stop-color="#3dd5f3"/>
                            <stop offset="100%" stop-color="#b2dfff"/>
                        </linearGradient>
                    </defs>
                    <path d="${arco(-90,90)}"
                        fill="none"
                        stroke="#173a4b"
                        stroke-width="14"/>
                    ${camaraActiva}

                ${ticksMenor.map(v => {
                    const a = tempToAngle(v);
                    const p1 = polar(a, r+2);
                    const p2 = polar(a, r+10);
                    return `<line class="tick-menor"
                        x1="${p1.x}" y1="${p1.y}"
                        x2="${p2.x}" y2="${p2.y}" />`;
                }).join("")}

                ${ticksMayor.map(v => {
                    const a = tempToAngle(v);
                    const p1 = polar(a, r+2);
                    const p2 = polar(a, r+16);
                    const txt = polar(a, r+30);

                    return `
                        <line class="tick-mayor"
                            x1="${p1.x}" y1="${p1.y}"
                            x2="${p2.x}" y2="${p2.y}" />

                        <text class="tick-text"
                            x="${txt.x}" y="${txt.y}">
                            ${v}
                        </text>
                    `;
                }).join("")}

                <g transform="rotate(${aguja} ${cx} ${cy})">
                    <line x1="${cx}" y1="${cy}"
                          x2="${cx}" y2="${cy-r+20}"
                          stroke="#173a4b"/>
                </g>

                <circle cx="${cx}" cy="${cy}" r="5" fill="#173a4b"/>
            `;
            }
        }  
        async function actualizarTemperatura(idCamara) {
            try {
                const res = await fetch(`${API}/ActualTemp.php?id_camara=${idCamara}`);
                const data = await res.json();
                const card = document.querySelector(`.cam-card[data-camara="${idCamara}"]`);
                if (!card) return;
                if (data && data.valor !== undefined) {
                    cargarGrafico(data.valor);
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
