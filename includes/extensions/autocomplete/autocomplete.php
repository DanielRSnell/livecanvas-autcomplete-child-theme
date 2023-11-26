<?php 

// Define the configuration for extending autocomplete

/* This will be replaced with admin options and filter API */ 
$extend_autocomplete = [
    'helpers' => [
        'clean_sort' => [
            'file' => '/includes/extensions/autocomplete/helpers/clean_sort.php',
            'status' => true
        ],
        'get_file' => [
            'file' => '/includes/extensions/autocomplete/helpers/get_file.php',
            'status' => true
        ],
        'get_files' => [
            'file' => '/includes/extensions/autocomplete/helpers/get_files.php',
            'status' => true
        ],
        'get_json_file' => [
            'file' => '/includes/extensions/autocomplete/helpers/get_json_file.php',
            'status' => true
        ],
        // Add more helpers as needed
    ],
    'frameworks' => [
        'picostrap' => [
            'file' => '/includes/extensions/autocomplete/frameworks/picostrap/autocomplete.php',
            'status' => false // Set to true to enable if Picostrap is active
        ],
        'custom' => [
            'file' => '/includes/extensions/autocomplete/frameworks/custom/autocomplete.php',
            'status' => false 
        ],
        'winden_v1' => [
            'file' => '/includes/extensions/autocomplete/frameworks/winden_v1/autocomplete.php',
            'status' => false // Set to true to enable if Winden is active
        ],
        // Add more frameworks as needed, see Core Framework example or Picostrap to extend into another.
    ]
];


// Function to include files based on the provided configuration
function include_autocomplete_files($config) {
    $base_dir = get_stylesheet_directory();

    foreach ($config as $type => $extensions) {
        foreach ($extensions as $extension => $data) {
            if ($data['status']) {
                $file_path = $base_dir . $data['file'];

                if (file_exists($file_path)) {
                    require_once $file_path;
                } else {
                    error_log("File not found for {$type} extension '{$extension}': {$file_path}");
                }
            }
        }
    }
}

// Include the files based on the $extend_autocomplete configuration
include_autocomplete_files($extend_autocomplete);

/**
 * Generates the JavaScript payload for autocomplete suggestions.
 *
 * This function creates a JavaScript snippet that initializes a constant
 * with autocomplete suggestions. These suggestions are obtained through
 * a filter hook, allowing other functions or plugins to modify or add to the list.
 */
function generate_autocomplete_script() {
    // Apply a filter to get the completions array. 
    // Other functions can modify this array via the 'lc_modify_completions' filter hook.
    $completions = apply_filters('lc_modify_completions', []);

    // Encode the completions array to JSON for use in JavaScript
    $ready_json = json_encode($completions);

    // Echo out the JavaScript code. The suggestions are stored in a const variable.
    echo '<script id="autocomplete-payload">
    const suggestions = ' . $ready_json . ';
    </script>';
}

// Attach the generate_autocomplete_script function to the lc_editor_header action hook.
// The priority is set to 120, which determines the order in which the function is executed relative to others.
add_action('lc_editor_header', 'generate_autocomplete_script', 120);


/**
 * Adds the autocomplete script tag to the page.
 *
 * This function outputs a script tag for the 'autocomplete.js' file, with the 'defer' attribute.
 */
function lc_add_autocomplete_extension() {
    $script_url = get_stylesheet_directory_uri() . '/includes/extensions/autocomplete/assets/autocomplete.js';
    echo '<script src="' . esc_url($script_url) . '" defer></script>';
}

add_action('lc_add_editor_extensions', 'lc_add_autocomplete_extension');