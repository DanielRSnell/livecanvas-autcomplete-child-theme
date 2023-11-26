<?php 

function pico_get_css_files($directory_path, $file_pattern, $meta) {
    $css_files = glob($directory_path . '/' . $file_pattern);

    if (!empty($css_files)) {
        $merged_css_contents = '';

        // Merge contents of all CSS files
        foreach ($css_files as $file) {
            $merged_css_contents .= file_get_contents($file);
        }

        // Clean and sort the merged contents
        $cleaned_data = pico_clean_and_sort_css($merged_css_contents);

        // Format and return the cleaned data
        return array_map(function ($item) use ($meta) {
            return [
                'caption' => $item,
                'value'   => $item,
                'meta'    => $meta
            ];
        }, $cleaned_data);
    } else {
        // Return an error message if no files are found
        return new WP_Error('files_not_found', 'No files found in the specified directory and file pattern.', array('status' => 404));
    }
}