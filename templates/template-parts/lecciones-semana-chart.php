<?php
// Asegurarse de que este archivo no se acceda directamente
if (!defined('ABSPATH')) {
    exit;
}

// Obtener los datos para las lecciones completadas durante los últimos 7 días
if (function_exists('politeia_count_lessons_completed_per_day')) {
    $chart_data = politeia_count_lessons_completed_per_day();
} else {
    $chart_data = [
        'labels' => ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
        'data' => [0, 0, 0, 0, 0, 0, 0], // Datos por defecto en caso de que la función no exista
        'dates' => [ // Fechas asociadas con cada día de la semana
            date('d/m/Y', strtotime('monday this week')),
            date('d/m/Y', strtotime('tuesday this week')),
            date('d/m/Y', strtotime('wednesday this week')),
            date('d/m/Y', strtotime('thursday this week')),
            date('d/m/Y', strtotime('friday this week')),
            date('d/m/Y', strtotime('saturday this week')),
            date('d/m/Y', strtotime('sunday this week'))
        ]
    ];
}

// Asegurarse de que se muestran datos solo hasta el día de hoy
$today_day_of_week = date('N') - 1; // 0 para Lunes, 6 para Domingo
for ($i = $today_day_of_week + 1; $i < 7; $i++) {
    $chart_data['data'][$i] = 0; // Asegurarse de que no haya datos para días futuros
}

?>

<h3>Lecciones esta semana</h3>
<p>Lunes <?php echo date('d', strtotime('monday this week')); ?> hasta el Domingo <?php echo date('d', strtotime('sunday this week')); ?></p>
<div id="module-two-chart-container">
    <canvas id="module-two-chart"></canvas>
</div>

<script type="text/javascript">
    // Datos para el gráfico
    const lessonsCompletedChartData = <?php echo json_encode($chart_data); ?>;

    // Calcular el valor máximo para el eje Y
    const maxLessonsCompleted = Math.max(...lessonsCompletedChartData.data);
    const yAxisMaxValue = maxLessonsCompleted > 10 ? maxLessonsCompleted : 10;

    // Configuración del gráfico de barras usando Chart.js
    const ctxModuleTwo = document.getElementById('module-two-chart').getContext('2d');
    const moduleTwoChart = new Chart(ctxModuleTwo, {
        type: 'bar',
        data: {
            labels: lessonsCompletedChartData.labels, // Etiquetas para los días
            datasets: [{
                label: 'Lecciones Completadas',
                data: lessonsCompletedChartData.data, // Datos para las lecciones completadas
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    min: 0, // Valor mínimo del eje Y
                    max: yAxisMaxValue, // Valor máximo del eje Y
                    ticks: {
                        stepSize: 1, // Tamaño de los pasos en el eje Y
                    },
                    grid: {
                        drawBorder: false,
                        color: function(context) {
                            if (context.tick.value % 2 === 0) {
                                return 'rgba(0, 0, 0, 0.1)'; // Línea sólida para cada dos unidades
                            } else {
                                return 'rgba(0, 0, 0, 0.1)'; // Línea punteada para cada unidad
                            }
                        },
                        borderDash: function(context) {
                            if (context.tick.value % 2 !== 0) {
                                return [5, 5]; // Pespunteado para las líneas entre las unidades
                            }
                            return []; // Línea sólida
                        }
                    }
                }
            },
            responsive: true,
            maintainAspectRatio: true
        }
    });
</script>
