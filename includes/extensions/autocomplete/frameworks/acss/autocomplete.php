<?php 


/**
 * Retrieves and adds ACSS completions to the provided completions array.
 *
 * This function fetches ACSS completion options from a remote endpoint, formats them
 * according to a specified structure, and then merges these formatted completions
 * with an existing array of completions.
 *
 * @param array $completions The existing array of completions to which ACSS completions will be added.
 * @return array The updated array of completions with ACSS completions included.
 */
function add_acss_completions_to_array($completions) {
    // Fetch ACSS class names from the remote endpoint
    $acss_class_names = lc_extract_and_respond_acss_classnames();

    // Check if the response is valid and an array
    if (is_array($acss_class_names)) {
        // Map the ACSS options to the desired format
        $formatted_acss_completions = array_map(function ($item) {
            return [
                'caption' => $item,
                'value'   => $item,
                'meta'    => 'ACSS',
            ];
        }, $acss_class_names);

        // Merge the formatted ACSS completions with existing completions and return
        return array_merge($completions, $formatted_acss_completions);
    } else {
        // If the response is not valid, return the original completions
        return $completions;
    }
}

add_filter('lc_modify_completions', 'add_acss_completions_to_array');


/**
 * Extracts and responds with ACSS (Automatic CSS) class names.
 *
 * This function scans the specified directory for CSS files, merges their contents,
 * and processes them to extract ACSS class names. It is designed to work with the 
 * Automatic CSS system, consolidating class names from multiple CSS files.
 *
 * @return array An array of cleaned class names or an error message if no files are found.
 */
function lc_extract_and_respond_acss_classnames() {
    // Define the path to the directory containing ACSS CSS files
    $directory_path = WP_CONTENT_DIR . '/uploads/automatic-css';

    // Get all CSS files in the directory
    $css_files = glob($directory_path . '/*.css');

    // Check if there are any CSS files found
    if (!empty($css_files)) {
        $merged_css_contents = '';

        // Merge the contents of all CSS files
        foreach ($css_files as $file) {
            $merged_css_contents .= file_get_contents($file);
        }

        // Clean and sort the class names extracted from the CSS contents
        $cleaned_classnames = pico_clean_and_sort_css($merged_css_contents);

        // Return the array of cleaned class names
        return $cleaned_classnames;
    } else {
        // Return an error message if no CSS files are found
        return [
            'status' => false,
            'message' => 'No CSS files found for ACSS.'
        ];
    }
}