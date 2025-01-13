<?php
// Asegurarse de que este archivo no se acceda directamente
if (!defined('ABSPATH')) {
    exit;
}

// Incluir Chart.js desde un CDN
echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
?>

<?php
$current_user_id = get_current_user_id();
$has_products = false;

// Verificar si el usuario tiene productos asociados
$args = array(
    'author' => $current_user_id,
    'post_type' => 'product',
    'posts_per_page' => -1,
);
$products = get_posts($args);

if (!empty($products)) {
    $has_products = true;
}

// Mostrar banner solo si el usuario no tiene productos asociados
if (!$has_products) {
    politeia_display_banner();
}
?>
<div id="mis-ventas-blocks">
<div id="primer-block-mis-ventas">
    <div id="ventas-modulo-one">
        <?php
        // Incluir el template part lecciones-semana-chart.php
        include plugin_dir_path(__FILE__) . 'template-parts/total-ventas.php';
        ?>
    </div>
    <div id="ventas-modulo-two">
        <?php
        include plugin_dir_path(__FILE__) . 'template-parts/mis-ventas-blocks.php';
        ?>
    </div>
</div>

<div id="segundo-block-mis-ventas">
    <div id="ventas-modulo-three">
        <?php
        include plugin_dir_path(__FILE__) . 'template-parts/mejores-clientes.php';
        ?> 
        
    </div>
    <div id="ventas-modulo-four">
        <?php
        include plugin_dir_path(__FILE__) . 'template-parts/ventas-mensuales.php';
        ?> 
    </div>
</div>
</div>
