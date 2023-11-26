<?php 

// Load Composer dependencies.
require_once get_stylesheet_directory() . '/vendor/autoload.php';

$timber_dir = get_stylesheet_directory() . '/includes/extensions/timber';

// Initialize Timber.
Timber\Timber::init();

/* Register Custom Paths for Twig Templates */
add_filter('timber/locations', function ($paths) {
    $paths['section'] = [
        get_stylesheet_directory() . '/template-livecanvas-sections',
    ];

    $paths['block'] = [
        get_stylesheet_directory() . '/template-livecanvas-blocks',
    ];

    // Add additional paths as needed

    return $paths;
});

/**
 * Determines the active custom fields plugin for Timber integration.
 *
 * This function checks if specific custom field plugins (ACF or MetaBox) are active
 * and returns a corresponding identifier for use in Timber extension configurations.
 * It's designed to be used in environments where conditional integration with 
 * custom field plugins is needed.
 *
 * @return string|bool Returns 'ACF' if ACF is active, 'METABOX' if MetaBox is active, 
 *                     or false if neither is active.
 */
function get_custom_fields_plugin_type_for_timber() {
    // Include WordPress plugin utility functions if not already available.
    // This is necessary for using 'is_plugin_active' function outside the admin area.
    if (!function_exists('is_plugin_active')) {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }

    // Check if ACF or ACF Pro is active.
    if (is_plugin_active('advanced-custom-fields/acf.php') || is_plugin_active('advanced-custom-fields-pro/acf.php')) {
        return 'ACF';
    }
    // Check if MetaBox is active.
    elseif (is_plugin_active('meta-box/meta-box.php')) {
        return 'METABOX';
    }
    // Return false if neither ACF nor MetaBox are active.
    else {
        return false;
    }
}

// Define the extend_timber array with a custom fields check for Timber integration.
$extend_timber = [
    'custom_fields' => get_custom_fields_plugin_type_for_timber(),
    'shortcodes' => [
        'singular' => [
            'file' => $timber_dir . '/shortcodes/singular.php',
            'status' => true,
        ],
        // Add more shortcodes as needed
        // 'archive' => [
        //     'file' => $timber_dir . '/shortcodes/archive.php',
        //     'status' => true
        // ]
    ],
    'helpers' => [
        'custom_fields' => [
            'file' => $timber_dir . '/helpers/query_vars/query_vars.php',
            'status' => true,
        ],
    ],
        // Add more helpers as needed
];

/**
 * Includes the extensions defined in the extend_timber array.
 */
function include_timber_extensions() {
    global $extend_timber, $timber_dir;

    // Include custom fields logic if plugin is active
    if (!empty($extend_timber['custom_fields']) && $extend_timber['custom_fields']) {
        // Custom fields integration logic can be included here
    }

    // Iterate over and include shortcodes
    if (!empty($extend_timber['shortcodes'])) {
        foreach ($extend_timber['shortcodes'] as $shortcode => $data) {
            if (!empty($data['status']) && file_exists($data['file'])) {
                require_once $data['file'];
            }
        }
    }

    // Iterate over and include helpers
    if (!empty($extend_timber['helpers'])) {
        foreach ($extend_timber['helpers'] as $helper => $data) {
            if (!empty($data['status']) && file_exists($data['file'])) {
                require_once $data['file'];
            }
        }
    }
}

// Call the function to include the extensions
include_timber_extensions();