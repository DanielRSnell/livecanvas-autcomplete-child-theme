<?php

add_action('rest_api_init', function () {
    register_rest_route('livecanvas/autocomplete/v1/lc', '/get_section', array(
        'methods' => 'GET',
        'callback' => 'wp_rest_api_get_lc_section_completions',
    ));
});

/**
 * REST API callback to get 'lc_section' completions.
 *
 * @param WP_REST_Request $request The REST request object.
 * @return WP_REST_Response The response with 'lc_section' completions.
 */
function wp_rest_api_get_lc_section_completions($request) {
    $lc_section_completions = get_lc_section_completions();
    return new WP_REST_Response($lc_section_completions, 200);
}

// Existing functions and hooks...


/**
 * Fetches all 'lc_section' posts and formats them for autocomplete completions.
 *
 * @return array Formatted completions from 'lc_section' posts.
 */
function get_lc_section_completions() {
    // Define the meta to be used for these completions
    $meta = 'LC Section';

    // Query for 'lc_section' posts
    $args = array(
        'post_type' => 'lc_partial',
        'posts_per_page' => -1, // Retrieve all posts
        'post_status' => 'publish'
    );
    $lc_sections = get_posts($args);

    // Map each post to the desired completion format
    return array_map(function ($post) use ($meta) {
    return [
        'caption' => 'lc:partial: ' . esc_html($post->post_title),
        'value'   => $post->post_content, // Encode HTML entities for JavaScript
        'meta'    => $meta
    ];
}, $lc_sections);

}

/**
 * Adds 'lc_section' completions to the completions array.
 *
 * @param array $completions The existing completions array.
 * @return array The updated completions array with 'lc_section' completions included.
 */
function add_lc_section_completions($completions) {
    $lc_section_completions = get_lc_section_completions();
    return array_merge($completions, $lc_section_completions);
}

add_filter('lc_modify_completions', 'add_lc_section_completions');