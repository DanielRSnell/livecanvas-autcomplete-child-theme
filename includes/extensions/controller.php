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
    'timber' => [ // make sure to run composer install in the theme directory to install Timber
        'file' => 'timber/timber.php',
        'status' => false
    ]

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


/**
 * Triggers a custom action for adding editor extensions before the closing body tag.
 *
 * This function serves as a hook point for adding various editor extensions that
 * should be executed before the closing body tag in the editor. It triggers a custom
 * action 'lc_add_editor_before_body_closing_extensions', which allows other parts
 * of the theme or plugins to add their own extensions to this point in the editor
 * without directly modifying this function.
 */
function lc_editor_before_body_closing_extensions() {
    /**
     * Custom action to add extensions to the editor before the closing body tag.
     *
     * This action allows other functions or plugins to add their own
     * extensions (like additional scripts, styles, etc.) to the editor at a point
     * just before the closing body tag. Extensions are hooked into this action using `add_action()`.
     */

    GUI_EXTENSION();

    do_action('lc_add_editor_before_body_closing_extensions');
}

/**
 * Attach the lc_editor_before_body_closing_extensions function to the 'lc_editor_before_body_closing' action hook.
 *
 * This attachment is done with a priority of 200, ensuring that it is executed
 * later in the sequence of functions hooked to 'lc_editor_before_body_closing'. This allows
 * the extensions to be added at an appropriate point just before the closing body tag.
 */
add_action('lc_editor_before_body_closing', 'lc_editor_before_body_closing_extensions', 200);


/**
 * GUI Extension for LiveCanvas.
 *
 * This function checks if Timber is active and then uses Timber to render
 * a simple GUI extension. It's designed to be triggered at a specific point
 * in the editor, for example, just before the closing body tag.
 *
 * @return string The rendered HTML markup or a message if Timber is not active.
 */
function GUI_EXTENSION() {
    
    echo '
    <style id="GUI-STYLES">
    #primary-tools {

        background: black!important;
        border-radius: 4px;
        padding-block: 8px;

    }
    </style>
    ';   
}