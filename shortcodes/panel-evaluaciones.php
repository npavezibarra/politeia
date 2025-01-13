<?php
function politeia_quiz_list_shortcode($atts) {
    // Atributos del shortcode
    $atts = shortcode_atts(
        array(
            'group_name' => '',
        ),
        $atts,
        'quiz_list'
    );

    // Validar que el nombre del grupo esté presente
    if (empty($atts['group_name'])) {
        return '<p>Por favor, proporcione un nombre de grupo.</p>';
    }

    // Obtener el grupo de LearnDash por nombre
    $group = get_page_by_title($atts['group_name'], OBJECT, 'groups');

    if (!$group) {
        return '<p>El grupo no fue encontrado.</p>';
    }

    // Obtener los cursos asociados al grupo
    $course_ids = learndash_group_enrolled_courses($group->ID);

    if (empty($course_ids)) {
        return '<p>No hay cursos asociados a este grupo.</p>';
    }

    // Crear la salida HTML
    ob_start();

    foreach ($course_ids as $course_id) {
        $quizzes = learndash_get_course_quiz_list($course_id);

        if (!empty($quizzes)) {
            foreach ($quizzes as $quiz) {
                // Obtener el ID del quiz
                $quiz_id = $quiz['post']->ID;
                $user_id = get_current_user_id();

                // Obtener los intentos de quiz del usuario
                $quiz_attempts = get_user_meta($user_id, '_sfwd-quizzes', true);

                // Inicializar variables para almacenar el estado del quiz
                $first_attempt_percentage = '';
                $latest_attempt_percentage = '';
                $latest_attempt_time = 0;
                $has_completed_quiz = false;

                if (!empty($quiz_attempts)) {
                    foreach ($quiz_attempts as $attempt) {
                        if ($attempt['quiz'] == $quiz_id) {
                            $has_completed_quiz = true;
                            $attempt_percentage = round(($attempt['score'] / $attempt['count']) * 100, 2);

                            if ($first_attempt_percentage === '') {
                                // Guardar el porcentaje del primer intento
                                $first_attempt_percentage = $attempt_percentage;
                            }

                            if ($attempt['time'] > $latest_attempt_time) {
                                // Guardar el porcentaje del último intento
                                $latest_attempt_percentage = $attempt_percentage;
                                $latest_attempt_time = $attempt['time'];
                            }
                        }
                    }
                }

                // Obtener el permalink para el quiz "-pre-curso"
                $pre_curso_quiz_url = get_permalink(get_page_by_path($quiz['post']->post_name . '-pre-curso', OBJECT, 'sfwd-quiz'));
                if (!$pre_curso_quiz_url) {
                    // If no "-pre-curso" quiz exists, fallback to the original quiz link
                    $pre_curso_quiz_url = get_permalink($quiz_id);
                }

                ?>
                <div class="quiz-container">
                    <div class="quiz-title">
                        <h3><?php echo esc_html($quiz['post']->post_title); ?></h3>
                        <a href="<?php echo get_permalink($course_id); ?>">Ir al curso</a>
                    </div>
                    <div class="quiz-buttons">
                        <?php if ($has_completed_quiz): ?>
                            <!-- Mostrar el porcentaje del primer intento -->
                            <div class="quiz-result" style="display: flex; align-items: center;">
                                <span 
                                    style="
                                        display: inline-block; 
                                        width: 20px; 
                                        height: 20px; 
                                        border-radius: 50%; 
                                        background-color: <?php echo $first_attempt_percentage >= 85 ? '#f1b433' : '#000'; ?>;
                                        margin-right: 10px;
                                    ">
                                </span>
                                <strong><?php echo esc_html($first_attempt_percentage); ?>%</strong>
                            </div>
                        <?php else: ?>
                            <!-- Si el quiz no se ha completado, mostrar el botón -->
                            <a href="<?php echo esc_url($pre_curso_quiz_url); ?>" class="quiz-button pre-course">
                                EVALUACIÓN PRE CURSO
                            </a>
                        <?php endif; ?>

                        <?php if ($has_completed_quiz): ?>
                            <!-- Mostrar el porcentaje del último intento -->
                            <div class="quiz-result" style="display: flex; align-items: center;">
                                <span 
                                    style="
                                        display: inline-block; 
                                        width: 20px; 
                                        height: 20px; 
                                        border-radius: 50%; 
                                        background-color: <?php echo $latest_attempt_percentage >= 85 ? '#f1b433' : '#000'; ?>;
                                        margin-right: 10px;
                                    ">
                                </span>
                                <strong><?php echo esc_html($latest_attempt_percentage); ?>%</strong>
                            </div>

                            <?php if ($first_attempt_percentage !== '' && $latest_attempt_percentage !== '' && $first_attempt_percentage !== $latest_attempt_percentage): ?>
                                <div class="quiz-increment" style="display: flex; align-items: center; margin-left: 10px;">
                                    <img src="<?php echo plugin_dir_url(__FILE__) . 'svg/' . ($latest_attempt_percentage > $first_attempt_percentage ? 'GreenUpArrow.svg' : 'RedDownArrow.svg'); ?>" alt="Arrow" style="width: 10px; height: 10px; margin-right: 5px;" />
                                    <strong><?php echo esc_html($latest_attempt_percentage - $first_attempt_percentage); ?>%</strong>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="<?php echo get_permalink($quiz_id); ?>" class="quiz-button post-course">
                                EVALUACIÓN POST CURSO
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<p>No hay quizzes asociados al curso ID: ' . esc_html($course_id) . '</p>';
        }
    }
    
    return ob_get_clean();
}
add_shortcode('quiz_list', 'politeia_quiz_list_shortcode');
?>
