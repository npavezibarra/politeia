<?php
// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

// Agregar pestañas al menú de perfil de BuddyBoss
function politeia_add_profile_tabs() {
    // Check if the logged-in user is viewing their own profile
    if (bp_loggedin_user_id() != bp_displayed_user_id()) {
        return; // Do not add the tabs if not viewing own profile
    }

    // Pestaña Mi Progreso
    bp_core_new_nav_item(array(
        'name' => __('Mi Progreso', 'politeia-stats'),
        'slug' => 'mi-progreso',
        'default_subnav_slug' => 'mi-progreso',
        'position' => 20,
        'screen_function' => 'politeia_mi_progreso_screen',
        'show_for_displayed_user' => true,
        'item_css_id' => 'mi-progreso'
    ));

    // Pestaña Mis Ventas
    bp_core_new_nav_item(array(
        'name' => __('Mis Ventas', 'politeia-stats'),
        'slug' => 'mis-ventas',
        'default_subnav_slug' => 'mis-ventas',
        'position' => 30,
        'screen_function' => 'politeia_mis_ventas_screen',
        'show_for_displayed_user' => true,
        'item_css_id' => 'mis-ventas'
    ));

    // Pestaña Mis Evaluaciones
    bp_core_new_nav_item(array(
        'name' => __('Mis Evaluaciones', 'politeia-stats'),
        'slug' => 'mis-evaluaciones',
        'default_subnav_slug' => 'mis-evaluaciones',
        'position' => 40,
        'screen_function' => 'politeia_mis_evaluaciones_screen',
        'show_for_displayed_user' => true,
        'item_css_id' => 'mis-evaluaciones'
    ));
}
add_action('bp_setup_nav', 'politeia_add_profile_tabs', 100);

// Funciones de pantalla para cada pestaña
function politeia_mi_progreso_screen() {
    add_action('bp_template_content', 'politeia_mi_progreso_content');
    bp_core_load_template('members/single/plugins');
}

function politeia_mi_progreso_content() {
    include plugin_dir_path(__FILE__) . '../templates/mi-progreso.php';
}

function politeia_mis_ventas_screen() {
    add_action('bp_template_content', 'politeia_mis_ventas_content');
    bp_core_load_template('members/single/plugins');
}

function politeia_mis_ventas_content() {
    include plugin_dir_path(__FILE__) . '../templates/mis-ventas.php';
}

function politeia_mis_evaluaciones_screen() {
    add_action('bp_template_content', 'politeia_mis_evaluaciones_content');
    bp_core_load_template('members/single/plugins');
}

function politeia_mis_evaluaciones_content() {
    include plugin_dir_path(__FILE__) . '../templates/mis-evaluaciones.php';
}
