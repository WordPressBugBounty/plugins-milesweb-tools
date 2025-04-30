<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
// Disable file editing
if (get_option('file_editing_disabled', false)) {
    define('DISALLOW_FILE_EDIT', true);
}
// // Disable XML-RPC
// add_filter('xmlrpc_enabled', function () {
//     return !get_option('disable_xmlrpc', false);
// });
// Function to disable XML-RPC
function milesweb_disable_xmlrpc() {
    if (get_option('disable_xmlrpc', false)) {
        // Fully disable XML-RPC
        add_filter('xmlrpc_enabled', '__return_false');
        // Remove X-Pingback Header
        add_filter('wp_headers', function ($headers) {
            unset($headers['X-Pingback']);
            return $headers;
        });
        // Block direct access to xmlrpc.php
        if (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) {
            exit('XML-RPC is disabled');
        }
    }
}
// Hook into WordPress **before** XML-RPC loads
add_action('plugins_loaded', 'milesweb_disable_xmlrpc', 1);