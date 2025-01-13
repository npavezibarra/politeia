<?php
// Hook to add the meta box
add_action('add_meta_boxes', 'politeia_add_author_metabox');

function politeia_add_author_metabox() {
    add_meta_box(
        'woo_author_metabox',             // ID of the Meta Box
        'Autor del Producto',             // Title of the Meta Box
        'politeia_author_metabox_callback', // Callback to display the contents of the Meta Box
        'product',                        // Post type (WooCommerce products)
        'side',                           // Context (display in the sidebar)
        'high'                            // Priority
    );
}

// Callback to display the contents of the Meta Box
function politeia_author_metabox_callback($post) {
    // Get the saved author ID
    $author_id = get_post_meta($post->ID, '_product_author', true);

    // Get the display name of the saved author (if exists)
    $author_name = '';
    if (!empty($author_id)) {
        $user = get_user_by('ID', $author_id);
        $author_name = $user ? $user->display_name : '';
    }
    
    ?>
    <label for="product_author">Selecciona el autor:</label>
    <input type="text" id="product_author_display" value="<?php echo esc_attr($author_name); ?>" style="width: 100%;" placeholder="Busca al autor" />
    <input type="hidden" id="product_author" name="product_author" value="<?php echo esc_attr($author_id); ?>" />
    <p>Comienza a escribir el nombre del autor para seleccionarlo.</p>
    <div id="author-message" style="color: red; display: none;">No hay autores con ese nombre.</div>

    <script>
        jQuery(document).ready(function($) {
            $('#product_author_display').autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: ajaxurl,
                        dataType: 'json',
                        data: {
                            action: 'politeia_search_users',
                            term: request.term
                        },
                        success: function(data) {
                            if (data.length === 0) {
                                $('#author-message').show(); // Show message if no results
                            } else {
                                $('#author-message').hide(); // Hide message if results found
                            }

                            response($.map(data, function(item) {
                                return {
                                    label: item.display_name,
                                    value: item.ID // Save the user ID
                                };
                            }));
                        }
                    });
                },
                minLength: 2,
                select: function(event, ui) {
                    $('#product_author_display').val(ui.item.label); // Show selected author's display name
                    $('#product_author').val(ui.item.value); // Save the user ID in the hidden field
                    return false;
                }
            });
        });
    </script>
    <?php
}

// Save the meta box value (save the user ID)
add_action('save_post', 'politeia_save_author_metabox');
function politeia_save_author_metabox($post_id) {
    if (isset($_POST['product_author'])) {
        update_post_meta($post_id, '_product_author', sanitize_text_field($_POST['product_author'])); // Save the user ID
    }
}

// AJAX action to search for users
add_action('wp_ajax_politeia_search_users', 'politeia_search_users_callback');
function politeia_search_users_callback() {
    global $wpdb;

    $term = sanitize_text_field($_GET['term']);
    $users = $wpdb->get_results($wpdb->prepare("
        SELECT ID, display_name 
        FROM $wpdb->users 
        WHERE display_name LIKE %s
        LIMIT 10
    ", '%' . $wpdb->esc_like($term) . '%'));

    $result = array();
    foreach ($users as $user) {
        $result[] = array(
            'ID' => $user->ID,
            'display_name' => $user->display_name
        );
    }

    wp_send_json($result); // Send the results back to the autocomplete
}
?>
