<?php

// Define the function to retrieve lessons completed by the user along with completion dates
function politeia_user_completed_lessons_with_dates() {
    global $wpdb;

    if (is_user_logged_in()) {
        $user_id = get_current_user_id();

        $query = "
            SELECT ld_user_activity.post_id as lesson_id, FROM_UNIXTIME(ld_user_activity.activity_completed) as completion_date
            FROM {$wpdb->prefix}learndash_user_activity as ld_user_activity
            WHERE ld_user_activity.activity_type = 'lesson'
            AND ld_user_activity.user_id = %d
            AND ld_user_activity.activity_completed IS NOT NULL
        ";

        $completed_lessons = $wpdb->get_results($wpdb->prepare($query, $user_id), ARRAY_A);

        $politeia_user_lessons_completed = [];

        foreach ($completed_lessons as $lesson) {
            $politeia_user_lessons_completed[] = [
                'lesson_id' => $lesson['lesson_id'],
                'completion_date' => $lesson['completion_date']
            ];
        }

        return $politeia_user_lessons_completed;
    } else {
        return [];
    }
}

function politeia_count_lessons_completed_per_day() {
    // Definir nombres de días en español comenzando con lunes
    $spanishDays = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];

    // Inicializar arrays para etiquetas y datos
    $labels_for_chart = $spanishDays;
    $lessons_count_per_day = array_fill(0, 7, 0); // Inicializar el conteo para cada día

    // Obtener las lecciones completadas con fechas
    $completed_lessons = politeia_user_completed_lessons_with_dates();

    // Crear un mapeo de números de día (1-7) a su índice correspondiente en el array $spanishDays
    $dayMapping = [
        'Mon' => 0,
        'Tue' => 1,
        'Wed' => 2,
        'Thu' => 3,
        'Fri' => 4,
        'Sat' => 5,
        'Sun' => 6
    ];

    // Obtener la fecha del lunes de esta semana
    $start_of_week = new DateTime();
    $start_of_week->modify('monday this week')->setTime(0, 0, 0);

    // Obtener la fecha del domingo de esta semana
    $end_of_week = clone $start_of_week;
    $end_of_week->modify('sunday this week')->setTime(23, 59, 59);

    // Contabilizar las lecciones completadas cada día de la semana actual
    foreach ($completed_lessons as $lesson) {
        $completionDate = new DateTime($lesson['completion_date']);

        // Verificar si la fecha de finalización está dentro de esta semana
        if ($completionDate >= $start_of_week && $completionDate <= $end_of_week) {
            $dayOfWeek = $completionDate->format('D'); // Obtener el día de la semana como una abreviatura de 3 letras (Mon, Tue, etc.)
            $dayIndex = $dayMapping[$dayOfWeek];
            $lessons_count_per_day[$dayIndex]++;
        }
    }

    return [
        'labels' => $labels_for_chart,
        'data' => $lessons_count_per_day
    ];
}

function politeia_display_user_orders() {
    // Get the current user's ID
    $current_user_id = get_current_user_id();
    
    // Prepare table layout
    echo '<table>';
    echo '<thead><tr><th>Order Number</th><th>Product Name</th><th>Price</th><th>Date Purchased</th><th>Product Author</th></tr></thead>';
    echo '<tbody>';
    
    // Fetch all orders
    $args = array(
        'limit' => -1, // Retrieve all orders
        'status' => 'completed', // Only completed orders
    );
    
    $orders = wc_get_orders($args);
    
    // Iterate through each order
    if (!empty($orders)) {
        foreach ($orders as $order) {
            // Iterate through each product in the order
            foreach ($order->get_items() as $item_id => $item) {
                $product_id = $item->get_product_id();
                $product_author = get_post_meta($product_id, '_product_author', true); // Retrieve the _product_author
                
                // If product_author matches the logged-in user (user ID)
                if ($product_author == $current_user_id) {
                    echo '<tr>';
                    echo '<td>' . esc_html($order->get_id()) . '</td>';
                    echo '<td>' . esc_html($item->get_name()) . '</td>';
                    echo '<td>' . wc_price($order->get_total()) . '</td>';
                    echo '<td>' . esc_html($order->get_date_created()->date('Y-m-d')) . '</td>';
                    
                    // Display the author or a dash if no author is set
                    echo '<td>' . (!empty($product_author) ? get_the_author_meta('display_name', $product_author) : '-') . '</td>';
                    
                    echo '</tr>';
                }
            }
        }
    } else {
        echo '<tr><td colspan="5">No orders found for this author.</td></tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
}
add_shortcode('politeia_user_orders', 'politeia_display_user_orders');

/* CODIGO QUE AGREGA COLUMNA A LA TABLA CUROSOS DE LEARNDASH PARA VER AL GURPO AL QUE PERTENECE */

// Hook into the admin columns for LearnDash courses
add_filter('manage_sfwd-courses_posts_columns', 'politeia_add_group_order_column');
add_action('manage_sfwd-courses_posts_custom_column', 'politeia_show_group_order_column_content', 10, 2);

function politeia_add_group_order_column($columns) {
    // Add a new column for the group and order
    $columns['group_order'] = 'Grupo y Orden';
    return $columns;
}

function politeia_show_group_order_column_content($column, $post_id) {
    if ($column === 'group_order') {
        // Get the LearnDash groups this course belongs to
        $groups = learndash_get_course_groups($post_id);
        if (!empty($groups)) {
            foreach ($groups as $group_id) {
                // Get the group name
                $group_name = get_the_title($group_id);

                // Get the order of the course within the group
                $group_order = get_post_meta($post_id, '_group_order', true);

                // Display the group name and order
                echo esc_html($group_name) . ' [' . esc_html($group_order) . ']';
            }
        } else {
            echo 'No pertenece a ningún grupo';
        }
    }
}


