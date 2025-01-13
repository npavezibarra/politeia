<?php
/*
Plugin Name: Politeia Stats
Description: A plugin to manage and display statistics within the Politeia plugin.
Version: 1.0
Author: Nicolás Pavez AlmadenSpA

*/

// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'admin/assets/admin-functions.php';
require_once plugin_dir_path(__FILE__) . 'admin/admin-mis-ventas.php';
require_once plugin_dir_path(__FILE__) . 'admin/assets/admin.js';
require_once plugin_dir_path(__FILE__) . 'admin/assets/admin.css';
require_once plugin_dir_path(__FILE__) . 'admin/admin.php';
require_once plugin_dir_path(__FILE__) . 'profile-tabs/profile-tabs.php';
require_once plugin_dir_path(__FILE__) . 'functions.php';
require_once plugin_dir_path(__FILE__) . 'meta-fields.php';
require_once plugin_dir_path(__FILE__) . 'woo-author-metabox.php';
require_once plugin_dir_path(__FILE__) . 'shortcodes/panel-evaluaciones.php';
require_once plugin_dir_path(__FILE__) . 'templates/template-parts/banner-ventas.php';


// Enqueue styles for the frontend and admin pages
function politeia_stats_enqueue_scripts() {
    // Enqueue in admin pages
    if (is_admin()) {
        wp_enqueue_script('politeia-stats-admin-js', plugin_dir_url(__FILE__) . 'admin/assets/admin.js', array('jquery'), null, true);
        wp_enqueue_style('politeia-stats-admin-css', plugin_dir_url(__FILE__) . 'admin/assets/admin.css');
    }

    // Enqueue Profile Tabs CSS everywhere
    wp_enqueue_style('politeia-stats-profile-tabs-css', plugin_dir_url(__FILE__) . 'templates/assets/profile-tabs.css');
    wp_enqueue_style('politeia-stats-banner-ventas-css', plugin_dir_url(__FILE__) . 'templates/assets/banner-ventas.css');
    wp_enqueue_style('politeia-stats-mis-ventas-segundo-block-css', plugin_dir_url(__FILE__) . 'templates/assets/mis-ventas-segundo-block.css');
    wp_enqueue_style('politeia-stats-mejores-clientes-css', plugin_dir_url(__FILE__) . 'templates/assets/mejores-clientes.css');

    // Enqueue panel-evaluaciones CSS on the frontend
    wp_enqueue_style('politeia-panel-evaluaciones-css', plugin_dir_url(__FILE__) . 'shortcodes/css/panel-evaluaciones.css');
}
add_action('wp_enqueue_scripts', 'politeia_stats_enqueue_scripts');
add_action('admin_enqueue_scripts', 'politeia_stats_enqueue_scripts');






