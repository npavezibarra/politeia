<?php
// Ensure this file is not accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Retrieve the selected group category from the options table
$selected_category = get_option('selected_group_category', '');

// Fetch all LearnDash groups with the selected category
$args = array(
    'post_type' => 'groups',
    'tax_query' => array(
        array(
            'taxonomy' => 'ld_group_category', // Adjust this to match your taxonomy name
            'field' => 'slug',
            'terms' => $selected_category,
        ),
    ),
    'orderby' => 'title',
    'order'   => 'ASC',
);
$groups = get_posts($args);

// Check if any groups were found
if ($groups) {
    echo '<div class="ld-group-list">';
    echo '<h3>Malla General Politeia</h3>';
    echo '<div id="malla-general-container">';
    
    foreach ($groups as $group) {
        // Use the LearnDash function to get the course IDs associated with the group
        $course_ids = learndash_group_enrolled_courses($group->ID);

        echo '<div class="ld-group-item">';
        echo '<div id="group-name-title">' . esc_html($group->post_title) . '</div>';
        echo '<div class="courses">';

        if (!empty($course_ids)) {
            // Create an array to hold courses sorted by "politeia_course_order"
            $ordered_courses = [];

            // Loop through courses and get their order
            foreach ($course_ids as $course_id) {
                $order = get_post_meta($course_id, '_group_order', true);
                
                // Check if the meta field exists and is not empty
                if ($order !== '' && $order !== false) {
                    $ordered_courses[(int)$order] = $course_id;
                }
            }

            // Sort courses by their order key
            ksort($ordered_courses);

            // Display circles in order with appropriate links
            for ($i = 1; $i <= 5; $i++) {
                if (isset($ordered_courses[$i])) {
                    $course_id = $ordered_courses[$i];
                    $course_status = learndash_course_status($course_id);

                    // Determine the class based on course status
                    if ($course_status === 'Completed') {
                        $circle_class = 'completed';
                    } elseif ($course_status === 'In Progress') {
                        $circle_class = 'enrolled';
                    } else {
                        $circle_class = 'exists';
                    }

                    // Display the circle with a link to the course
                    $course_url = get_permalink($course_id);
                    echo '<a href="' . esc_url($course_url) . '" class="course-circle ' . esc_attr($circle_class) . '"></a>';
                } else {
                    // Display an empty circle if no course is assigned to this order
                    echo '<span class="course-circle empty"></span>';
                }
            }
        } else {
            echo 'No hay cursos asociados';
        }
        echo '</div>'; // Close courses div
        echo '</div>'; // Close ld-group-item div
    }
    echo '</div>'; // Close malla general div
    echo '</div>'; // Close ld-group-list div
} else {
    echo '<p>No group categories found.</p>';
}
?>
