<?php 

/**
 * Registers REST API routes for autocomplete functionality.
 */
function lc_complete_custom_completion_routes() {
    register_rest_route('livecanvas/v1/autocomplete', '/op/single', array(
        'methods' => 'GET',
        'callback' => 'lc_extract_and_respond_theme_classnames',
    ));
    // Register a new REST route for the /assets/op CSS files
    register_rest_route('livecanvas/v1/autocomplete', '/op/multiple', array(
        'methods' => 'GET',
        'callback' => 'lc_extract_and_respond_op_classnames',
    ));
}

add_action('rest_api_init', 'lc_complete_custom_completion_routes');

/**
 * Retrieves theme completion options from a REST API endpoint.
 *
 * @param string $endpoint_url The URL of the REST API endpoint.
 * @return array The formatted completion options.
 */
function get_theme_completions($endpoint_url) {
    $response = wp_safe_remote_get($endpoint_url);
    $body = wp_remote_retrieve_body($response);
    $parsed_data = json_decode($body, true);

    return array_map(function ($item) {
        return [
            'caption' => $item,
            'value'   => $item,
            'meta'    => 'Theme',
        ];
    }, $parsed_data);
}

/**
 * Adds theme completions to the completions array.
 *
 * @param array $completions The existing completions array.
 * @return array The updated completions array with theme completions added.
 */
function add_theme_completions($completions) {
    $endpoint_url = site_url() . '/wp-json/livecanvas/v1/autocomplete/theme';
    $fetch_completions = get_theme_completions($endpoint_url);
    return array_merge($completions, $fetch_completions);
}

/**
 * Extracts and responds with theme classnames from the style.css file.
 *
 * @return WP_REST_Response|WP_Error The response object with class names or WP_Error on failure.
 */
function lc_extract_and_respond_theme_classnames() {
    $css_file_path = get_stylesheet_directory() . '/style.css';

    if (file_exists($css_file_path)) {
        $css_contents = file_get_contents($css_file_path);
        $cleaned_classnames = pico_clean_and_sort_css($css_contents);
        return new WP_REST_Response($cleaned_classnames, 200);
    } else {
        return new WP_Error('css_file_not_found', 'style.css file not found in the theme directory.', array('status' => 404));
    }
}

// Hook to add theme completions
add_filter('lc_modify_completions', 'add_theme_completions');

/**
 * Extracts and responds with class names from CSS files in the specified directory.
 *
 * @return WP_REST_Response|WP_Error The response object with class names or WP_Error on failure.
 */
function lc_extract_and_respond_op_classnames() {
    $directory_path = get_stylesheet_directory() . '/assets/op';
    $css_files = glob($directory_path . '/*.css');

    if (!empty($css_files)) {
        $merged_css_contents = '';

        // Merge the contents of all CSS files
        foreach ($css_files as $file) {
            $merged_css_contents .= file_get_contents($file);
        }

        // Clean and sort the class names extracted from the CSS contents
        $cleaned_classnames = pico_clean_and_sort_css($merged_css_contents);

        return new WP_REST_Response($cleaned_classnames, 200);
    } else {
        return new WP_Error('css_files_not_found', 'No CSS files found in /assets/op directory.', array('status' => 404));
    }
}