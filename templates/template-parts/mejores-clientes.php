<div class="mejores-clientes-container">
    <h3>Mejores clientes</h3>
    <?php
    // Obtener el ID del usuario logueado
    $current_user_id = get_current_user_id();

    // Obtener los 5 mejores clientes de la base de datos (por ejemplo, aquellos que han comprado más productos)
    $args = array(
        'limit' => -1, 
        'status' => 'completed', 
    );

    $orders = wc_get_orders($args);
    $clients_data = [];

    // Recopilar datos de clientes y sus compras
    if (!empty($orders)) {
        foreach ($orders as $order) {
            foreach ($order->get_items() as $item) {
                $product_id = $item->get_product_id();
                // Verificar si el autor del producto es el usuario logueado
                $product_author = get_post_meta($product_id, '_product_author', true);

                if ($product_author == $current_user_id) {
                    $customer_id = $order->get_customer_id();
                    if (!isset($clients_data[$customer_id])) {
                        $clients_data[$customer_id] = [
                            'name' => get_user_meta($customer_id, 'first_name', true) . ' ' . get_user_meta($customer_id, 'last_name', true),
                            'avatar' => get_avatar_url($customer_id),
                            'total' => 0,
                        ];
                    }
                    // Sumar el total de la compra
                    $clients_data[$customer_id]['total'] += $order->get_total();
                }
            }
        }
    }

    if (!empty($clients_data)) {
        // Ordenar clientes por el total de compras
        usort($clients_data, function($a, $b) {
            return $b['total'] - $a['total'];
        });

        // Mostrar los 5 mejores clientes
        $top_clients = array_slice($clients_data, 0, 5);

        echo '<table><tbody>';
        foreach ($top_clients as $client) {
            echo '<tr>';
            echo '<td>';
            echo '<img src="' . esc_url($client['avatar']) . '" alt="' . esc_html($client['name']) . '" class="client-avatar" />';
            echo '<span>' . esc_html($client['name']) . '</span>';
            echo '</td>';
            echo '<td>' . wc_price($client['total']) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<p>No tienes clientes aún.</p>';
    }
    ?>
</div>

<style>

</style>
