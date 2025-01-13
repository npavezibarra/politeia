<?php
// Asegurarse de que este archivo no se acceda directamente
if (!defined('ABSPATH')) {
    exit;
}

// Incluir las evaluaciones del usuario logueado
function politeia_lista_evaluaciones() {
    global $wpdb;

    // Obtener el ID del usuario logueado
    $user_id = get_current_user_id();

    // Obtener las evaluaciones (quizzes) rendidas por el usuario
    $results = $wpdb->get_results($wpdb->prepare("
        SELECT 
            a.post_id as evaluation_name, 
            b.activity_meta_value as points, 
            c.activity_meta_value as percentage, 
            FROM_UNIXTIME(a.activity_completed) as date
        FROM 
            {$wpdb->prefix}learndash_user_activity a
        JOIN 
            {$wpdb->prefix}learndash_user_activity_meta b 
            ON a.activity_id = b.activity_id 
            AND b.activity_meta_key = 'points'
        JOIN 
            {$wpdb->prefix}learndash_user_activity_meta c 
            ON a.activity_id = c.activity_id 
            AND c.activity_meta_key = 'percentage'
        WHERE 
            a.user_id = %d 
            AND a.activity_type = 'quiz'
        ORDER BY 
            a.activity_completed DESC
    ", $user_id));

    // Mostrar la tabla de evaluaciones o un mensaje si no hay evaluaciones
    if (!empty($results)) {
        echo '<div class="politeia-core-groups-container">';
        echo '<h3 style="margin-bottom: 0px;">Listado de Evaluaciones Rendidas</h3>';
        echo '<table id="tabla-evaluaciones">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Evaluación</th>';
        echo '<th>Puntos</th>';
        echo '<th>% Correctas</th>';
        echo '<th>Fecha</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($results as $result) {
            echo '<tr>';
            echo '<td>' . esc_html(get_the_title($result->evaluation_name)) . '</td>';
            echo '<td>' . esc_html($result->points) . '</td>';
            echo '<td>' . esc_html($result->percentage) . '%</td>';
            echo '<td>' . esc_html(date('d/m/Y', strtotime($result->date))) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    } else {
        // Mostrar mensaje y botón si no hay evaluaciones
        echo '<h3 style="margin-bottom: 0px;">Listado de Evaluaciones Rendidas</h3>';
        echo '<p>No se encontraron evaluaciones rendidas.</p>';
        echo '<a href="' . esc_url(home_url('/evaluaciones/')) . '" class="button">Ir a Evaluaciones</a>';
    }
}

// Ejecutar la función para mostrar la lista de evaluaciones
politeia_lista_evaluaciones();
?>
