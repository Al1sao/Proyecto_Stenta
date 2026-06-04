const ctx = document.getElementById('temp');
const temperaturas =
    window.tempData.temperaturas;

new Chart(ctx, {
    type: 'line',
    data: {
        labels: [
            '1', '2', '3', '4', '5','6', '7', '8', '9', '10','11','12','13','14','15','16','17',
            '18','19','20','21','22','23','24','25','26','27','28','29','30'
        ],
        datasets: [{
            data: [
                0,2, 3, 1, 4, -5,
                2, 1, 3, 4, 2,1,3,4,-5,0,7,-7,-2,0,0,1,2,3,4,2,-8,2,-1,-2,
            ] /* temperaturas AÑADIR AL MOMENTO DE TENER DATOS REALES.*/,

            borderColor: '#48989b',
            backgroundColor: 'rgba(72, 138, 155, 0.2)',
            borderWidth: 3,
            tension: 0.3,
            /* fill: true, */
            pointBackgroundColor:'#3b5b6b',
            pointRadius: 3
        }]
    },

    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },

        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Día del mes'
                }
            },
            y: {
                title: {
                    display: true,
                    text: 'Temperatura °C'
                },
                beginAtZero: true
            }
        }
    }
});