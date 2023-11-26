<?php

/**
 * Shortcode handler for rendering content using Timber with support for query variables.
 *
 * This shortcode enables the rendering of content within Timber's context, including
 * all query variables and optionally handling prerendering of shortcodes. It provides
 * flexibility for content management by integrating Timber's rendering capabilities
 * with WordPress shortcodes.
 *
 * @param array $atts Shortcode attributes.
 * @param string|null $content The content to be rendered within the Timber context.
 * @return string Rendered content or a message if Timber is not active.
 */
function timber_singular_shortcode($atts, $content = null) {
    // Ensure Timber is available before proceeding.
    if (!class_exists('Timber')) {
        return 'Timber is not active.';
    }
    
    global $post;
    // Initialize Timber context and assign the current post to it.
    $context = Timber::context();

    $context['post'] = Timber::get_post( $post->ID );


    // Include shortcode attributes in the Timber context.
    foreach ($atts as $key => $value) {
        $context['attribute'][$key] = $value;
    }

    $prepare = do_shortcode($content);

    // Compile the content using Timber and return.
    return Timber::compile_string($prepare, $context);
}

// Register the shortcode in WordPress.
add_shortcode('pico_singular', 'timber_singular_shortcode');