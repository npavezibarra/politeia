<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Ventas</title>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
</head>
<body>

<div class="politeia-core-groups-container">
    <h3 style="margin-bottom: 0px;">Mis Ventas</h3>
    <table id="mis-ventas" class="display">
        <thead>
            <tr>
                <th>Comprador</th>
                <th>Producto</th>
                <th>Fecha</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $args = array(
                'limit' => -1, // Obtener todas las órdenes
                'status' => 'completed', // Solo órdenes completadas
            );

            $orders = wc_get_orders($args);

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
                                        $customer_id = $order->get_customer_id();
                                        $customer = get_userdata($customer_id);
                                        $customer_name = $customer ? $customer->display_name : 'N/A';
                                        echo '<tr>';
                                        echo '<td>' . esc_html($customer_name) . '</td>';
                                        echo '<td>' . esc_html($product->get_name()) . '</td>';
                                        echo '<td>' . esc_html($order->get_date_created()->date('d/m/Y')) . '</td>';
                                        echo '<td>' . esc_html($order->get_total()) . '</td>';
                                        echo '</tr>';
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                echo '<tr><td colspan="4">No se encontraron ventas.</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

<script>
    jQuery(document).ready(function($) {
        $('#mis-ventas').DataTable({
            "pageLength": 5, // Always show 5 entries per page
            "paging": true,  // Enable pagination
            "dom": 'ftip',   // Keep search (f), table (t), information (i), and pagination (p), but hide length control (l)
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.11.3/i18n/Spanish.json", 
                "zeroRecords": "No se encontraron registros",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                "infoEmpty": "Mostrando 0 a 0 de 0 entradas",
                "infoFiltered": "(filtrado de _MAX_ entradas totales)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "emptyTable": "No tienes ventas aun."
            }
        });
    });
</script>

</body>
</html>
