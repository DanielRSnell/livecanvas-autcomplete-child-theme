<?php

/**
 * Fetches all HTML/Twig files from specific template directories and formats them for autocomplete completions.
 *
 * @return array Formatted completions from the files.
 */
function get_template_file_completions() {
    $default_templates = [
        [
            'path' => get_stylesheet_directory() . '/template-livecanvas-blocks',
            'type' => 'block',
            'meta' => 'LC Block'
        ],
        [
            'path' => get_stylesheet_directory() . '/template-livecanvas-sections',
            'type' => 'section',
            'meta' => 'LC Section'
        ]
    ];

    $registered_templates = apply_filters('filter_registered_templates', $default_templates);
    $completions = [];

    foreach ($registered_templates as $template) {
        if (!is_dir($template['path'])) {
            continue;
        }

        $files = array_merge(glob($template['path'] . '/*.html'), glob($template['path'] . '/*.twig'));

        foreach ($files as $file) {
            $slug = basename($file, '.' . pathinfo($file, PATHINFO_EXTENSION));
            $formattedSlug = format_slug_for_display($slug);
            $valueShortcode = '<div class="live-shortcode" lc-helper="shortcode">[lc_get_file type="' . esc_attr($template['type']) . '" name="' . esc_attr($slug) . '"]</div>';

            $completions[] = [
                'caption' => 'file:' . esc_html($template['type']) . ': ' . esc_html($formattedSlug),
                'value'   => $valueShortcode,
                'meta'    => $template['meta']
            ];
        }
    }

    return $completions;
}

/**
 * Formats a slug string into a human-readable format.
 *
 * @param string $slug The slug string to format.
 * @return string The formatted slug.
 */
function format_slug_for_display($slug) {
    $slug = str_replace(['-', '_'], ' ', $slug); // Replace hyphens and underscores with spaces
    $slug = ucwords($slug); // Capitalize the first letter of each word
    return $slug;
}

/**
 * Adds template file completions to the completions array.
 *
 * @param array $completions The existing completions array.
 * @return array The updated completions array with template file completions included.
 */
function add_template_file_completions($completions) {
    $template_file_completions = get_template_file_completions();
    return array_merge($completions, $template_file_completions);
}

add_filter('lc_modify_completions', 'add_template_file_completions');