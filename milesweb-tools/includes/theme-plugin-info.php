<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
function milesweb_get_active_theme_info() {
    $theme = wp_get_theme();
    if (!$theme) {
        return ['error' => 'No active theme found.'];
    }
    return [
        'name'       => $theme->get('Name'),
        'version'    => $theme->get('Version'),
        'author'     => $theme->get('Author'),
        'theme_uri'  => $theme->get('ThemeURI'),
        'screenshot' => $theme->get_screenshot(),
    ];
}
// Function to get inactive themes
function milesweb_get_inactive_themes() {
    $all_themes = wp_get_themes();
    $active_theme = wp_get_theme();
    $inactive_themes = [];
    foreach ($all_themes as $theme) {
        if ($theme->get('Name') !== $active_theme->get('Name')) {
            $inactive_themes[] = [
                'name'   => $theme->get('Name'),
                'slug'   => $theme->get_stylesheet(),
                'version'=> $theme->get('Version'),
                'author' => $theme->get('Author'),
            ];
        }
    }
    return $inactive_themes;
}
function milesweb_get_inactive_themes_info() {
    $all_themes = wp_get_themes();
    $active_theme = wp_get_theme()->get('Name');
    $inactive_themes = [];
    foreach ($all_themes as $theme_slug => $theme) {
        if ($theme->get('Name') !== $active_theme) { // Exclude active theme
            $size_in_mb = milesweb_get_directory_size(get_theme_root() . '/' . $theme_slug) / 1024 / 1024; // Convert to MB
            $inactive_themes[] = [
                'name'    => $theme->get('Name'),
                'version' => $theme->get('Version'),
                'author'  => $theme->get('Author'),
                'size'    => number_format($size_in_mb, 2) . ' MB', // Display in MB
            ];
        }
    }
    return $inactive_themes;
}
function milesweb_get_plugins_info() {
    $all_plugins = get_plugins();
    $active_plugins = get_option('active_plugins');
    $plugin_data = [];
    foreach ($all_plugins as $plugin_path => $plugin) {
        $is_active = in_array($plugin_path, $active_plugins);
        $size_in_mb = milesweb_get_directory_size(WP_PLUGIN_DIR . '/' . dirname($plugin_path)) / 1024 / 1024; // Convert to MB
        $plugin_data[] = [
            'name'       => $plugin['Name'],
            'version'    => $plugin['Version'],
            'author'     => $plugin['Author'],
            'is_active'  => $is_active ? 'Active' : 'Inactive',
            'size'       => number_format($size_in_mb, 2) . ' MB', // Show size in MB with 2 decimal places
            'active'     => $is_active ? 1 : 0, // For sorting (1 = Active, 0 = Inactive)
        ];
    }
    // Sort plugins: Active first, then Inactive
    usort($plugin_data, function ($a, $b) {
        return $b['active'] <=> $a['active']; // Sort by active status (1 first, 0 last)
    });
    return $plugin_data;
}
function milesweb_get_directory_size($dir) {
    $size = 0;
    if (!is_dir($dir)) {
        return $size;
    }
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)) as $file) {
        $size += $file->getSize();
    }
    return $size; // Size in bytes (converted to MB in `milesweb_get_plugins_info()`)
}
// Function to delete an inactive theme
function milesweb_delete_theme() {
    check_ajax_referer('milesweb_ajax_nonce', 'nonce');
    if (!current_user_can('delete_themes')) {
        wp_send_json_error(['message' => 'Unauthorized']);
    }
    // $theme_slug = isset($_POST['theme_slug']) ? wp_unslash($_POST['theme_slug']) : '';
    $theme_slug = isset($_POST['theme_slug']) ? sanitize_text_field(wp_unslash($_POST['theme_slug'])) : '';
    $theme_slug = sanitize_key($theme_slug);
    if (wp_delete_theme($theme_slug)) {
        wp_send_json_success(['message' => 'Theme deleted successfully.']);
    } else {
        wp_send_json_error(['message' => 'Failed to delete theme.']);
    }
}
add_action('wp_ajax_milesweb_delete_theme', 'milesweb_delete_theme');
// Function to delete an inactive plugin
function milesweb_delete_plugin() {
    check_ajax_referer('milesweb_ajax_nonce', 'nonce');
    // Ensure the current user has the capability to delete plugins
    if (!current_user_can('delete_plugins')) {
        wp_send_json_error(['message' => 'Unauthorized']);
    }
    // Sanitize and unslash the plugin slug
    //$plugin_slug = isset($_POST['plugin_slug']) ? wp_unslash($_POST['plugin_slug']) : '';
    $plugin_slug = isset($_POST['plugin_slug']) ? sanitize_text_field(wp_unslash($_POST['plugin_slug'])) : '';
    $plugin_slug = sanitize_file_name($plugin_slug);  // Use sanitize_file_name for plugin slugs
    // Get the full plugin path (e.g., plugin-directory/plugin-file.php)
    $plugin_path = plugin_dir_path($plugin_slug);
    // Ensure the plugin is not active before deletion
    if (is_plugin_active($plugin_path)) {
        wp_send_json_error(['message' => 'Cannot delete an active plugin.']);
    }
    // Try to delete the plugin
    if (delete_plugins([$plugin_slug])) {
        wp_send_json_success(['message' => 'Plugin deleted successfully.']);
    } else {
        wp_send_json_error(['message' => 'Failed to delete plugin.']);
    }
}
add_action('wp_ajax_milesweb_delete_plugin', 'milesweb_delete_plugin');
// Function to check security vulnerabilities
function milesweb_security_recommendations() {
    $security_issues = [];
    // Check if XML-RPC is enabled
    if (get_option('disable_xmlrpc') != 1) {
        $security_issues[] = '❌ XML-RPC is enabled. Disable it for security reasons.';
    }
    // Check if file editing is enabled
    if (!defined('DISALLOW_FILE_EDIT') || !DISALLOW_FILE_EDIT) {
        $security_issues[] = '❌ File editing is enabled. Disable it to prevent unauthorized modifications.';
    }
    // Check for outdated plugins
    $plugin_updates = get_site_transient('update_plugins');
    if (!empty($plugin_updates->response)) {
        $security_issues[] = '⚠️ Some plugins have updates available. Keep all plugins updated.';
    }
    // Check if WP_DEBUG is enabled
    if (defined('WP_DEBUG') && WP_DEBUG) {
        $security_issues[] = '⚠️ WP_DEBUG is enabled. Disable it on live sites for security.';
    }
    // Check if admin username is "admin"
    $admin_user = get_user_by('login', 'admin');
    if ($admin_user) {
        $security_issues[] = '❌ The username "admin" is commonly attacked. Use a different username.';
    }
    return $security_issues;
}