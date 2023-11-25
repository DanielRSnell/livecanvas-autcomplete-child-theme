<?php

function get_cleaned_and_formatted_data($file_path, $meta) {
    // Check if the file exists at the given path
    if (file_exists($file_path)) {
        // Get the contents of the file
        $file_contents = file_get_contents($file_path);

        // Clean and sort the data from the file
        $cleaned_data = lc_clean_and_sort_css($file_contents);

        // Format the cleaned data and return it
        return array_map(function ($item) use ($meta) {
            return [
                'caption' => $item,
                'value'   => $item,
                'meta'    => $meta
            ];
        }, $cleaned_data);
        
    } else {
        // Return an error message if the file is not found
        return new WP_Error('file_not_found', "{$file_path} not found.", array('status' => 404));
    }
}