<?php

/**
 * Adds Picostrap bundle completions to the array from the file system.
 *
 * This function fetches class names from a CSS file and formats them
 * for use as completions (e.g., in an editor or autocomplete feature).
 * The formatted completions are then merged into an existing array of completions.
 *
 * @param array $completions Existing array of completions to which new ones will be added.
 * @return array The updated array of completions with Picostrap completions added.
 */
function add_pico_completions($completions) {
    // Fetch completions from the Picostrap theme.
    $fetch_completions = lc_extract_and_respond_pico_classnames();

    // Format each fetched completion for use in the completions array.
    $classNames = array_map(function ($item) {
        return [
            'caption' => $item,
            'value'   => $item,
            'meta'    => 'Picostrap', // Metadata to identify the source of completion.
        ];
    }, $fetch_completions);

    // Merge the new completions with existing completions.
    return array_merge($completions, $classNames);
}

// Add the add_pico_completions function to the 'lc_modify_completions' filter hook.
add_filter('lc_modify_completions', 'add_pico_completions');


/* ---FILESYSTEM INTEGRATION---- */


/**
 * File System Callback to extract class names from the Picostrap CSS bundle.
 *
 * This function reads the CSS file specified in the Picostrap theme,
 * cleans it, sorts it, and extracts the class names for use in completions.
 *
 * @return array|string An array of cleaned and sorted class names, or an error message if the file is not found.
 */
function lc_extract_and_respond_pico_classnames() {
    // Define the path to the CSS file.
    $css_file_path = get_stylesheet_directory() . '/css-output/bundle.css';

    // Check if the CSS file exists.
    if (file_exists($css_file_path)) {
        // Read the contents of the CSS file.
        $css_contents = file_get_contents($css_file_path);

        // Clean and sort the CSS class names.
        $cleaned_classnames = pico_clean_and_sort_css($css_contents);

        // Return the cleaned and sorted class names.
        return $cleaned_classnames;
    } else {
        // Return an error message if the CSS file is not found.
        return [
            'status' => 404,
            'message' => 'Bundle.css was not found in the Picostrap theme.'
        ];
    }
}