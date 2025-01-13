<?php
// Ensure the file is not accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Hook your code to run after plugins are loaded
add_action('plugins_loaded', 'politeia_register_meta_boxes');

function politeia_register_meta_boxes() {
    // Check if the LearnDash function exists
    if (function_exists('learndash_get_course_groups')) {
        // Add your meta box here
        add_action('add_meta_boxes', 'politeia_add_meta_box');
    }
}

function politeia_add_meta_box() {
    add_meta_box(
        'politeia_course_order',
        'Orden del Grupo',
        'politeia_render_meta_box',
        'sfwd-courses',
        'side'
    );
}

function politeia_render_meta_box($post) {
    // Get the current order value
    $current_order = get_post_meta($post->ID, '_group_order', true);
    $groups = learndash_get_course_groups($post->ID);

    $assigned_orders = [];
    $group_names = [];

    if ($groups) {
        foreach ($groups as $group_id) {
            $group_name = get_the_title($group_id);
            $group_names[] = $group_name;
            $assigned_orders = array_merge($assigned_orders, get_assigned_orders($group_id));
        }
        $group_list = implode(', ', $group_names);
    } else {
        $group_list = 'No pertenece a ningún grupo';
    }
    ?>
    <p>Especifica el orden en que este curso debería aparecer dentro del grupo:</p>
    <select name="group_order">
        <?php for ($i = 1; $i <= 5; $i++) : ?>
            <option value="<?php echo $i; ?>" <?php selected($current_order, $i); ?>>
                <?php echo $i; ?>
            </option>
        <?php endfor; ?>
    </select>

    <p style="color: red;">Pertenece a los siguientes grupos: <?php echo esc_html($group_list); ?></p>

    <div>
        <?php for ($i = 1; $i <= 5; $i++) : ?>
            <span style="color: <?php echo in_array($i, $assigned_orders) ? 'red' : 'black'; ?>;"><?php echo $i; ?></span>
        <?php endfor; ?>
    </div>
    <?php
}

function get_assigned_orders($group_id) {
    global $wpdb;
    
    $assigned_orders = [];
    
    // Get all courses in the group
    $courses_in_group = learndash_group_enrolled_courses($group_id);
    
    if (!empty($courses_in_group)) {
        foreach ($courses_in_group as $course_id) {
            // Get the order for each course
            $order = get_post_meta($course_id, '_group_order', true);
            if ($order) {
                $assigned_orders[] = (int)$order;
            }
        }
    }
    
    return $assigned_orders;
}

// Save the meta box value
add_action('save_post', 'politeia_save_meta_box');

function politeia_save_meta_box($post_id) {
    if (isset($_POST['group_order'])) {
        update_post_meta($post_id, '_group_order', sanitize_text_field($_POST['group_order']));
    }
}
