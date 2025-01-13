<?php
function politeia_display_banner() {
    // Retrieve the saved page ID from the database
    $saved_page_id = get_option('politeia_ventas_page', 0); // Fallback to 0 if no page is saved

    // Get the permalink of the saved page
    $saved_url = ($saved_page_id) ? get_permalink($saved_page_id) : '#'; // Fallback to '#' if no valid page

    // Get current user ID
    $current_user_id = get_current_user_id();
    
    // Initialize sales count
    $has_sales = false;
    
    // Fetch WooCommerce completed orders
    $args = array(
        'limit' => -1,
        'status' => 'completed'
    );
    $orders = wc_get_orders($args);

    if (!empty($orders)) {
        foreach ($orders as $order) {
            foreach ($order->get_items() as $item_id => $item) {
                $product_id = $item->get_product_id();
                $product_author = get_post_meta($product_id, '_product_author', true);

                // Check if the current user is the product author
                if ($product_author == $current_user_id) {
                    $has_sales = true;
                    break 2; // Exit both loops once a sale is found
                }
            }
        }
    }

    // Show banner only if the user has not made any sales
    if (!$has_sales) {
        ?>
        <div id="banner-ventas">
            <h3>¿Quieres vender en Politeia?</h3>
            <p>Si quieres vender cursos o libros en Politeia, lee nuestras condiciones. En el dashboard de abajo verás el reporte de tus ventas.</p>
            <a href="<?php echo esc_url($saved_url); ?>" class="btn-mas-informacion">Más Información</a>
        </div>
        <?php
    }
}
