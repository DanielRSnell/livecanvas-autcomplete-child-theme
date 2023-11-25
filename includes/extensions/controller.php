<?php

/**
 * Initializes and manages the extensions for the theme.
 *
 * This script sets up an array of extensions, each with a file path and a status indicating
 * whether or not to include it. It then iterates over this array, including each extension file
 * whose status is set to true.
 */

// Define the extensions array with the relative path and status for each extension.

/* This will be replaced with admin options and filter API */
$extensions = [
    'autocomplete' => [
        'file' => 'autocomplete/autocomplete.php',
        'status' => true
    ],
    // Add more extensions as needed.
];

// Base directory path for the extensions.
$extensions_base_dir = get_stylesheet_directory() . '/includes/extensions/';

// Iterate over each extension in the array.
foreach ($extensions as $extension => $data) {
    // Check if the extension is marked for inclusion.
    if ($data['status']) {
        // Construct the full path to the extension file.
        $extension_file = $extensions_base_dir . $data['file'];

        // Include the extension file if it exists.
        if (file_exists($extension_file)) {
            require_once $extension_file;
        } else {
            // Log an error if the extension file is not found.
            error_log("Extension file not found for {$extension}: {$extension_file}");
        }
    }
}



/**
 * Triggers a custom action for adding editor extensions.
 *
 * This function serves as a hook point for adding various editor extensions.
 * It triggers a custom action 'lc_add_editor_extensions', which allows other
 * parts of the theme or plugins to add their own extensions to the editor
 * without directly modifying this function.
 */
function lc_editor_extensions() {
    /**
     * Custom action to add extensions to the editor.
     *
     * This action allows other functions or plugins to add their own
     * extensions (like autocomplete, custom styles, etc.) to the editor.
     * Extensions are hooked into this action using `add_action()`.
     */
    do_action('lc_add_editor_extensions');
}

/**
 * Attach the lc_editor_extensions function to the 'lc_editor_header' action hook.
 *
 * This attachment is done with a priority of 200, ensuring that it is executed
 * later in the sequence of functions hooked to 'lc_editor_header'. This allows
 * the extensions to be added after the basic editor setup is complete.
 */
add_action('lc_editor_header', 'lc_editor_extensions', 200);