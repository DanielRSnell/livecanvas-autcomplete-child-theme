<?php 

/**
 * Registers REST API routes for autocomplete functionality.
 */
function lc_complete_custom_completion_routes() {
    register_rest_route('livecanvas/v1/autocomplete', '/op/single', array(
        'methods' => 'GET',
        'callback' => 'lc_respond_single_file_classnames',
    ));
    register_rest_route('livecanvas/v1/autocomplete', '/op/multiple', array(
        'methods' => 'GET',
        'callback' => 'lc_respond_multiple_file_classnames',
    ));
}

add_action('rest_api_init', 'lc_complete_custom_completion_routes');

/**
 * Extracts and responds with class names from a single CSS file.
 *
 * @return WP_REST_Response|WP_Error The response object with class names or WP_Error on failure.
 */
function lc_respond_single_file_classnames() {
    $css_file_path = get_stylesheet_directory() . '/assets/op/op.css';

    if (file_exists($css_file_path)) {
        $css_contents = file_get_contents($css_file_path);
        $cleaned_classnames = pico_clean_and_sort_css($css_contents);
        return new WP_REST_Response($cleaned_classnames, 200);
    } else {
        return new WP_Error('css_file_not_found', 'op.css file not found in the /assets/op directory.', array('status' => 404));
    }
}

/**
 * Extracts and responds with class names from multiple CSS files in a specified directory.
 *
 * @return WP_REST_Response|WP_Error The response object with class names or WP_Error on failure.
 */
function lc_respond_multiple_file_classnames() {
    $directory_path = get_stylesheet_directory() . '/assets/op';
    $css_files = glob($directory_path . '/*.css');

    if (!empty($css_files)) {
        $merged_css_contents = '';

        foreach ($css_files as $file) {
            $merged_css_contents .= file_get_contents($file);
        }

        $cleaned_classnames = pico_clean_and_sort_css($merged_css_contents);

        return new WP_REST_Response($cleaned_classnames, 200);
    } else {
        return new WP_Error('css_files_not_found', 'No CSS files found in /assets/op directory.', array('status' => 404));
    }
}

/**
 * Adds theme completions to the completions array.
 *
 * @param array $completions The existing completions array.
 * @return array The updated completions array with theme completions added.
 */
function add_theme_completions($completions) {
    $endpoint_url_single = site_url() . '/wp-json/livecanvas/v1/autocomplete/op/single';
    $endpoint_url_multiple = site_url() . '/wp-json/livecanvas/v1/autocomplete/op/multiple';

    // Fetch completions from both endpoints and merge them
    $fetch_completions_single = get_theme_completions($endpoint_url_single);
    $fetch_completions_multiple = get_theme_completions($endpoint_url_multiple);
    
    $merged_completions = array_merge($completions, $fetch_completions_single, $fetch_completions_multiple);
    return $merged_completions;
}

add_filter('lc_modify_completions', 'add_theme_completions');