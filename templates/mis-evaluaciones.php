<?php
// Asegurarse de que este archivo no se acceda directamente
if (!defined('ABSPATH')) {
    exit;
}

// Incluir Chart.js desde un CDN
echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
?>

<div id="primer-block-mis-evaluaciones">
    <div id="evaluaciones-modulo-two">
        <?php
        include plugin_dir_path(__FILE__) . 'template-parts/lista-evaluaciones.php';
        ?>
    </div>
    <!--<div id="evaluaciones-modulo-one">
        <?php
        // Incluir el template part lecciones-semana-chart.php
       // include plugin_dir_path(__FILE__) . 'template-parts/resumen-evaluaciones.php';
        ?>
    </div> -->
    
</div>
