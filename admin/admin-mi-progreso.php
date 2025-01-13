<?php
// Asegurarse de que este archivo no se acceda directamente
if (!defined('ABSPATH')) {
    exit;
}

// Obtener todas las categorías de grupos de LearnDash
$group_categories = get_terms([
    'taxonomy' => 'ld_group_category', // Asegúrate de usar el nombre correcto de la taxonomía
    'hide_empty' => false,
]);

// Guardar la categoría seleccionada cuando se envíe el formulario
if (isset($_POST['submit_group_category'])) {
    $selected_category = sanitize_text_field($_POST['group_category']);
    update_option('selected_group_category', $selected_category);
}

// Obtener la categoría seleccionada actualmente
$current_category = get_option('selected_group_category', '');

?>
<div class="wrap">
    <h1>Configurar sección "Mi Progreso"</h1>
    <div id="admin-module-two">
        <form method="post">
            <h3>Modulo 2</h3>
            <p>Selecciona la categoría de grupo a mostrar en el módulo 2</p>
            <?php if ($group_categories && !is_wp_error($group_categories)): ?>
                <ul>
                    <?php foreach ($group_categories as $category): ?>
                        <li>
                            <label>
                                <input type="radio" name="group_category" value="<?php echo esc_attr($category->slug); ?>" <?php checked($current_category, $category->slug); ?>>
                                <?php echo esc_html($category->name); ?>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No hay categorías de grupo disponibles.</p>
            <?php endif; ?>
            <p>
                <input type="submit" name="submit_group_category" value="Guardar" class="button button-primary">
            </p>
        </form>
    </div>
</div>
