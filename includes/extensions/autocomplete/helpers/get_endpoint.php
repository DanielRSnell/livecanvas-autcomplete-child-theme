<?php

function pico_get_formatted_endpoint($endpoint_url, $meta) {
    // Perform a safe HTTP GET request to the specified endpoint URL
    $response = wp_safe_remote_get($endpoint_url);

    // Check if the request was successful
    if (is_wp_error($response)) {
        // Handle the error according to your needs
        error_log('Error fetching data from ' . $endpoint_url . ': ' . $response->get_error_message());
        return [];
    }

    // Retrieve and decode the JSON body from the response
    $body = wp_remote_retrieve_body($response);
    $parsed_data = json_decode($body, true); // true to get associative array

    // Check if the decoding was successful
    if (is_null($parsed_data)) {
        // Handle the decoding error
        error_log('Error decoding JSON from ' . $endpoint_url);
        return [];
    }

    // Map the parsed data to the desired format
    return array_map(function ($item) use ($meta) {
        return [
            'caption' => $item,
            'value'   => $item,
            'meta'    => $meta
        ];
    }, $parsed_data);
}