<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Ventas</title>
</head>
<body>

<div class="politeia-core-groups-container">
    <h3 style="margin-bottom: 0px;">Ventas mensuales</h3>
    <div id="ventas-mensuales">
        <?php
        // Llamar a la función que mostrará la lista de ventas mensuales
        politeia_display_sales_by_month();
        ?>
    </div>
</div>

</body>
</html>

<?php
function politeia_display_sales_by_month() {
    // Obtener el ID del usuario logueado
    $current_user_id = get_current_user_id();
    
    // Inicializar un array para almacenar los totales de los últimos 5 meses
    $monthly_totals = array_fill(0, 5, 0);
    
    // Array con los nombres de los meses en español
    $months_in_spanish = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
    
    // Obtener el mes y año actual
    $current_month = date('n');  // Mes actual (1-12)
    $current_year = date('Y');   // Año actual

    // Loop para los últimos 5 meses
    for ($i = 0; $i < 5; $i++) {
        // Calcular el mes a partir del mes actual restando $i meses
        $month_to_show = $current_month - $i;
        $year_to_show = $current_year;

        if ($month_to_show <= 0) {
            $month_to_show += 12;
            $year_to_show -= 1;
        }

        $start_date = date("Y-m-01", strtotime("$year_to_show-$month_to_show-01"));
        $end_date = date("Y-m-t", strtotime("$year_to_show-$month_to_show-01"));

        // Preparar argumentos para la consulta de órdenes de WooCommerce
        $args = array(
            'limit' => -1, // Sin límite de resultados
            'status' => 'completed', // Solo ventas completadas
            'type'   => 'shop_order', // Solo órdenes de tipo shop_order
            'date_query' => array(
                array(
                    'after' => $start_date,
                    'before' => $end_date,
                    'inclusive' => true,
                ),
            ),
        );

        // Obtener las órdenes completadas de WooCommerce
        $orders = wc_get_orders($args);

        // Iterar a través de las órdenes para calcular el total por mes
        if (!empty($orders)) {
            foreach ($orders as $order) {
                // Iterar a través de los productos de cada orden
                foreach ($order->get_items() as $item_id => $item) {
                    $product_id = $item->get_product_id();

                    // Verificar si el autor del producto es el usuario logueado
                    $product_author = get_post_meta($product_id, '_product_author', true);
                    if ($product_author == $current_user_id) {
                        // Sumar el total de la venta al mes correspondiente
                        $monthly_totals[$i] += $order->get_total();
                    }
                }
            }
        }
    }

    // Mostrar la lista de ventas mensuales
    echo '<ul class="ventas-mensuales-list">';
    for ($i = 0; $i < 5; $i++) {
        // Obtener el nombre del mes correspondiente
        $month_index = ($current_month - $i - 1 + 12) % 12; // Ajustar índice para manejar retroceso de meses
        $month_name = ($i === 0) ? 'Este mes' : $months_in_spanish[$month_index];

        // Mostrar la fila
        echo '<li><span>' . $month_name . '</span><span>' . wc_price($monthly_totals[$i]) . '</span></li>';
    }
    echo '</ul>';
}
