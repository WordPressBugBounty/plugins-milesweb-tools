<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
add_action('admin_menu', 'milesweb_admin_menu');

function milesweb_admin_menu() {
    add_submenu_page(
        'milesweb', // parent slug
        'MilesWeb Security',
        'MilesWeb Security',
        'manage_options',
        'milesweb-security', // unique slug
        'mw_security_info'   // function to display
    );
}
function mw_security_info() {
    $base_path="";$base_path = trailingslashit(ABSPATH);
    $mw_security_data = get_mw_security_events_from_remote($base_path);

    include MILESWEB_PLUGIN_DIR . 'templates/milesweb-security.php';
}

function get_mw_security_events_from_remote($base_path = '') {
    $log_file = __DIR__ . '/mw_security-log.txt';

    if (empty($base_path)) {
        return ['error' => 'Missing base path'];
    }
    $url = "https://cart.milesweb.com/plugin/api/api.php?path=" . urlencode($base_path);
    $response = wp_remote_get($url, [
        'timeout' => 20,
        'headers' => [
            'Accept' => 'application/json',
            'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url()
        ]
    ]);

    if (is_wp_error($response)) {
        $err = $response->get_error_message();
        return ['error' => $err];
    }

    $body = wp_remote_retrieve_body($response);
    $decoded = json_decode($body, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        // echo "JSON Decode Error: " . esc_html( json_last_error_msg() );
        return;
    }
    return $decoded;
}
