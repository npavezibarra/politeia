<?php
// Asegurarse de que este archivo no se acceda directamente
if (!defined('ABSPATH')) {
    exit;
}

// Incluir Chart.js desde un CDN
echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
?>

<div id="primer-block-mi-progreso">
    <div id="progreso-modulo-one">
        <?php
        // Incluir el template part lecciones-semana-chart.php
        include plugin_dir_path(__FILE__) . 'template-parts/lecciones-semana-chart.php';
        ?>
    </div>
    <div id="progreso-modulo-two">
        <?php
        include plugin_dir_path(__FILE__) . 'template-parts/malla-general.php';
        ?>
    </div>
</div>
