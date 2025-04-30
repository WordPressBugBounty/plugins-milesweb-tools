<?php
// Function to check WordPress version and return message
function milesweb_check_wordpress_version() {
    // Get the current WordPress version
    $current_version = get_bloginfo('version');
    // Fetch the latest WordPress version using the WordPress API
    $response = wp_remote_get('https://api.wordpress.org/core/version-check/1.7/');
    if (is_wp_error($response)) {
        return '<p style="color: red;">'. esc_html__('Could not check for the latest WordPress version.', 'milesweb-tools').'</p>';
    }
    // Decode the JSON response
    $data = json_decode(wp_remote_retrieve_body($response));
    if (!isset($data->offers[0]->current)) {
        return '<p style="color: red;">'.esc_html__('Unable to get the latest version data.', 'milesweb-tools') . '</p>';
    }
    $latest_version = $data->offers[0]->current;
    // Compare the current and latest versions
    if ($current_version === $latest_version) {
        return '<p style="color: green;">'.esc_html__('You have the latest version of WordPress.', 'milesweb-tools') . '</p>';
    } else {
        return '<p style="color: orange;">'.esc_html__('Your WordPress version is outdated. The latest version is ', 'milesweb-tools') . esc_html($latest_version).'. </p>';
    }
}
