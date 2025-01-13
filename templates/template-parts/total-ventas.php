<?php
// Asegurarse de que este archivo no se acceda directamente
if (!defined('ABSPATH')) {
    exit;
}

// Obtener los datos para las ventas de la semana
$current_user_id = get_current_user_id();
$chart_data = [
    'labels' => ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
    'sales_amounts' => [0, 0, 0, 0, 0, 0, 0]
];

$total_week_sales = 0; // Variable para almacenar el total de las ventas de la semana

// Establecer el inicio y fin de la semana
$start_of_week = strtotime('monday this week');
$end_of_week = strtotime('sunday this week 23:59:59');

// Obtener los pedidos completados de WooCommerce para el autor actual
$args = array(
    'limit' => -1,
    'status' => 'completed',
);
$orders = wc_get_orders($args);

// Procesar los datos de ventas
if (!empty($orders)) {
    foreach ($orders as $order) {
        foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product();
            if ($product) {
                $related_courses = get_post_meta($product->get_id(), '_related_course', true);
                $related_courses = maybe_unserialize($related_courses);
                if (is_array($related_courses) && !empty($related_courses)) {
                    foreach ($related_courses as $course_id) {
                        $course_author = get_post_field('post_author', $course_id);
                        if ($course_author == $current_user_id) {
                            $order_date = $order->get_date_created()->getTimestamp();
                            if ($order_date >= $start_of_week && $order_date <= $end_of_week) {
                                $day_of_week = date('N', $order_date) - 1; // 0 = Lunes, 6 = Domingo
                                $sale_amount = $order->get_total();
                                $chart_data['sales_amounts'][$day_of_week] += $sale_amount;
                                $total_week_sales += $sale_amount; // Sumar al total de ventas de la semana
                            }
                        }
                    }
                }
            }
        }
    }
}

?>
<div id="ventas-semana-container" style="display: flex; justify-content: space-between; align-items: center;">
    <div id="title-ventas" style="flex-grow: 1;">
        <h3 style="margin: 0;">Ventas esta semana</h3>
        <p style="margin: 0; font-size: 12px;">Lunes <?php echo date('d', $start_of_week); ?> hasta el Domingo <?php echo date('d', $end_of_week); ?></p>
    </div>
    <div id="ganancias" style="text-align: right;">
        <h3 style="margin: 0;"><?php echo wc_price($total_week_sales); ?></h3>
        <p style="margin: 0; font-size: 12px;">ganado esta semana</p>
    </div>
</div>
<div id="module-two-chart-container" style="margin-top: 20px;">
    <canvas id="module-two-chart" width="860" height="300"></canvas>
</div>

<script type="text/javascript">
    // Datos para el gráfico
    const ventasChartData = <?php echo json_encode($chart_data); ?>;

    // Calcular el valor máximo de la Y en función de las ventas
    let maxSalesAmount = Math.max(...ventasChartData.sales_amounts);
    let yMax;

    if (maxSalesAmount === 0) {
        // Si no hay ventas, establecer el máximo en 50,000 CLP
        yMax = 50000;
    } else {
        // Si hay ventas, establecer el máximo con un 15% más del máximo de ventas
        yMax = Math.ceil(maxSalesAmount * 1.15 / 5000) * 5000; // Redondear al múltiplo de 5,000 más cercano
    }

    // Configuración del gráfico de barras usando Chart.js
    const ctxModuleTwo = document.getElementById('module-two-chart').getContext('2d');
    const moduleTwoChart = new Chart(ctxModuleTwo, {
        type: 'bar',
        data: {
            labels: ventasChartData.labels, // Días de la semana
            datasets: [
                {
                    label: 'Total de Ventas (CLP)',
                    data: ventasChartData.sales_amounts, // Montos totales de ventas
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                },
            ],
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    max: yMax, // Valor máximo calculado dinámicamente
                    position: 'left', // Mantener el eje en el lado izquierdo
                    title: {
                        display: true,
                        text: 'Total de Ventas (CLP)',
                    },
                },
            },
            responsive: true,
            maintainAspectRatio: false, // Cambiar esto para permitir el control de altura personalizado
        },
    });
</script>

