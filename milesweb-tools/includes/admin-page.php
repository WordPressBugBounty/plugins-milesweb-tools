<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
// Register plugin settings
add_action('admin_init', 'milesweb_settings_register');
function milesweb_settings_register() {
    register_setting('milesweb_settings_group', 'milesweb_maintenance_mode_enabled', 'sanitize_boolean' );
    register_setting('milesweb_settings_group', 'milesweb_force_https_redirect', 'sanitize_boolean');
    register_setting('milesweb_settings_group', 'milesweb_file_editing_disabled', 'sanitize_boolean');
    register_setting('milesweb_settings_group', 'milesweb_disable_xmlrpc', 'sanitize_boolean');
    // Ensure option exists
    if (get_option('milesweb_disable_xmlrpc') === false) {
        add_option('milesweb_disable_xmlrpc', 0);
    }
}
/**
 * Custom sanitize function for boolean values (0 or 1).
 */
function milesweb_sanitize_boolean($input) {
    return (bool) $input ? 1 : 0;
}
// Add admin menu
add_action('admin_menu', 'milesweb_settings_menu');
function milesweb_settings_menu() {
    add_menu_page(
        'MilesWeb',                  // Page title.
        'MilesWeb',                  // Menu title.
        'manage_options',            // Capability.
        'milesweb',                  // Menu slug.
        'milesweb_settings_page',    // Callback function.
        MILESWEB_PLUGIN_ASSETS_URL . 'images/milesweb.svg',
        2                          // Position in the menu.
    );
}
// Add the plugin to the admin toolbar (upper menu).
add_action('admin_bar_menu', 'milesweb_settings_toolbar_menu', 100);
function milesweb_settings_toolbar_menu($wp_admin_bar) {
    if (!current_user_can('manage_options')) {
        return; // Only show to users with proper capability.
    }
    $wp_admin_bar->add_node([
        'id'    => 'milesweb',               // Unique ID for the menu.
        'title' => '<span class="mw-plugin-bg-icon" style="background-image: url(' . MILESWEB_PLUGIN_ASSETS_URL . 'images/milesweb.svg); margin-right: 5px; vertical-align: middle; width: 20px; height: 20px; display: inline-block;"></span>MilesWeb',
        'href'  => admin_url('admin.php?page=milesweb'), // Link to settings page.
        'meta'  => [
            'class' => 'milesweb-toolbar-item',       // Optional CSS class.
            'title' => 'Go to MilesWeb Toolbar',     // Tooltip text.
        ],
    ]);
}
// Render the admin settings page
function milesweb_settings_page() {
    include MILESWEB_PLUGIN_DIR . 'templates/admin-view.php';
}
