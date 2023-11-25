<?php

/* This is documentation of creating a framework extensions */

/**
 * Fetches autocomplete data from the Core Framework plugin.
 *
 * This function checks if the Core Framework plugin is active and if the required
 * Helper class exists. If so, it retrieves class names and CSS variables from the plugin.
 * CSS variables are prefixed with '--' to match the CSS variable syntax.
 *
 * @return array The combined array of class names and CSS variables, or an error message.
 */
function lc_autocomplete_core_framework_callback() {
    // Check if the Core Framework plugin is active and the Helper class exists
    if (is_plugin_active('core-framework/core-framework.php') && class_exists('CoreFramework\Helper')) {
        // Initialize the Helper class from Core Framework
        $helper = new CoreFramework\Helper();

        // Fetch class names and variables with 'group_by_category' set to false
        $class_names = $helper->getClassNames(['group_by_category' => false]);
        $variables = $helper->getVariables(['group_by_category' => false]);

        // Prepend CSS variables with '--' to match CSS variable syntax
        $updated_variables = array_map(function($variable) {
            return '--' . $variable;
        }, $variables);

        // Combine class names and updated variables into a single array
        return array_merge($class_names, $updated_variables);
    } else {
        // Return an error array if the Core Framework plugin is not active or the Helper class is not found
        return [
            'status' => false,
            'message' => 'Core Framework plugin is not active or Helper class not found.'
        ];
    }
}


/**
 * Adds Core Framework completions to the array, fetched from a remote endpoint.
 *
 * This function retrieves dynamic completion options from the remote endpoint
 * and then maps them to the desired format before merging them with existing completions.
 *
 * @param array $completions The existing array of completions to which new ones will be added.
 * @return array The updated array of completions with Core Framework completions added.
 */
function add_core_framework_completions($completions) {
    // Fetch the completions from the remote endpoint
    $body = lc_autocomplete_core_framework_callback();

    // Map dynamic options to the desired format
    $corefw_completions = array_map(function ($item) {
        return [
            'caption' => $item,
            'value'   => $item,
            'meta'    => 'Core Framework',
        ];
    }, $body);

    // Merge the fetched completions with existing completions
    return array_merge($completions, $corefw_completions);
}