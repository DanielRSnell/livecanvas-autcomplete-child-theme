<?php

function get_any_var($var_name) {
    if (isset($_GET[$var_name])) {
        return sanitize_text_field($_GET[$var_name]);
    }
    return null;
}

function get_params_object() {
    // Get all query parameters
    $queryParams = $_GET;

    $attributes = [];
    foreach ($queryParams as $key => $value) {
        // If the parameter is not 'email', prefix it with 'metadata__'
        if ($key !== 'email') {
            $key = 'metadata__' . $key;
        }
        $attributes[$key] = sanitize_text_field($value);
    }

    return $attributes;
}


function get_string_vars() {
    $results = array(
        'output' => '',
        'vars' => array(),
        'values' => ''
    );

    if (!empty($_GET)) {
        // Sanitize each query variable
        $clean_vars = array_map('sanitize_text_field', $_GET);

        // Create a stringified version of the query vars
        $results['output'] = 'domain.com/string?' . http_build_query($clean_vars);

        // Create an array of strings, each in "key=value" format, and an array of values
        foreach ($clean_vars as $key => $value) {
            $results['vars'][$key] = $value;
        }

        // Create a string with the values separated by a comma and a space
        $results['values'] = implode(', ', $clean_vars);
    }

    return $results;
}

function has_query_params() {
    return !empty($_GET);
}

/**
 * Constructs a string representation of all query parameters in 'key: variable' format.
 *
 * @return string A comma-separated string of all query parameters.
 */
function get_query_params_as_string() {
    if (!empty($_GET)) {
        // Sanitize each query variable and convert them into 'key: value' format
        $formatted_vars = array_map(function ($key, $value) {
            return $key . ': ' . sanitize_text_field($value);
        }, array_keys($_GET), $_GET);

        // Join the formatted variables with commas
        return implode(', ', $formatted_vars);
    }
    return '';
}