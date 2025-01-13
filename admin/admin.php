<?php

// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

// Add a menu item in the WordPress admin panel
function politeia_stats_add_admin_menu() {
    // Main menu: Politeia Stats
    add_menu_page(
        __('Politeia Stats', 'politeia-stats'),
        __('Politeia Stats', 'politeia-stats'),
        'manage_options',
        'politeia-stats',
        'politeia_stats_admin_page',
        'dashicons-chart-bar'
    );

    // Submenu: Mi Progreso
    add_submenu_page(
        'politeia-stats',
        __('Mi Progreso', 'politeia-stats'),
        __('Mi Progreso', 'politeia-stats'),
        'manage_options',
        'mi-progreso',
        'politeia_stats_mi_progreso_page'
    );

    // Submenu: Mis Evaluaciones
    add_submenu_page(
        'politeia-stats',
        __('Mis Evaluaciones', 'politeia-stats'),
        __('Mis Evaluaciones', 'politeia-stats'),
        'manage_options',
        'mis-evaluaciones',
        'politeia_stats_mis_evaluaciones_page'
    );

    // Submenu: Mis Ventas (The selector for pages should be here)
    add_submenu_page(
        'politeia-stats',
        __('Mis Ventas', 'politeia-stats'),
        __('Mis Ventas', 'politeia-stats'),
        'manage_options',
        'mis-ventas-settings',
        'politeia_stats_mis_ventas_settings_page'
    );
}
add_action('admin_menu', 'politeia_stats_add_admin_menu');

// Main admin page callback for Politeia Stats
function politeia_stats_admin_page() {
    echo '<div class="wrap"><h1>' . __('Politeia Stats', 'politeia-stats') . '</h1></div>';
}

// Submenu page: Mi Progreso
function politeia_stats_mi_progreso_page() {
    include plugin_dir_path(__FILE__) . 'admin-mi-progreso.php';
}

// Submenu page: Mis Evaluaciones
function politeia_stats_mis_evaluaciones_page() {
    include plugin_dir_path(__FILE__) . 'admin-mis-evaluaciones.php';
}

// Submenu page: Mis Ventas Settings with page selector
function politeia_stats_mis_ventas_settings_page() {
    if (isset($_POST['politeia_ventas_page'])) {
        $page_id = intval($_POST['politeia_ventas_page']);
        update_option('politeia_ventas_page', $page_id);
        echo '<div class="updated"><p>Página actualizada correctamente.</p></div>';
    }

    // Get saved page ID
    $saved_page_id = get_option('politeia_ventas_page', 0);

    ?>
    <div class="wrap">
        <h2>Mis Ventas Settings</h2>
        <form method="post">
            <label for="politeia_ventas_page">Selecciona la página para "Más Información":</label><br/>
            <?php
            // Output the dropdown for pages
            wp_dropdown_pages(array(
                'selected' => $saved_page_id,
                'name' => 'politeia_ventas_page',
                'show_option_none' => 'Selecciona una página',
                'option_none_value' => 0
            ));
            ?>
            <br/>
            <input type="submit" class="button button-primary" value="Guardar">
        </form>
    </div>
    <?php
}
