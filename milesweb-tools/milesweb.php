<?php
/**
 * Plugin Name: MilesWeb Tools
 * Plugin URI: https://milesweb.com
 * Description: A plugin to manage maintenance mode, force HTTPS, disable file editing, track user login activity, display storage usage, and provide detailed insights into active/inactive themes and plugins.
 * Version: 1.0.1
 * Author: MilesWeb
 * Author URI: https://www.milesweb.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
// Define plugin constants
define('MILESWEB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MILESWEB_PLUGIN_URL', plugin_dir_url(__FILE__));
if (!defined('MILESWEB_PLUGIN_ASSETS_URL')) {
    define('MILESWEB_PLUGIN_ASSETS_URL', plugin_dir_url(__FILE__) . 'assets/');
}
// Include necessary files
require_once MILESWEB_PLUGIN_DIR . 'includes/admin-page.php';
require_once MILESWEB_PLUGIN_DIR . 'includes/ajax-handler.php';
require_once MILESWEB_PLUGIN_DIR . 'includes/maintenance-mode.php';
require_once MILESWEB_PLUGIN_DIR . 'includes/https-redirect.php';
require_once MILESWEB_PLUGIN_DIR . 'includes/security-settings.php';
require_once MILESWEB_PLUGIN_DIR . 'includes/user-logging.php';
require_once MILESWEB_PLUGIN_DIR . 'includes/storage-usage.php';
require_once MILESWEB_PLUGIN_DIR . 'includes/wp-update.php';
require_once MILESWEB_PLUGIN_DIR . 'includes/theme-plugin-info.php';
// Enqueue assets
function milesweb_enqueue_assets() {
    // Register CSS
    wp_register_style('milesweb-admin-css', MILESWEB_PLUGIN_URL . 'assets/css/mw-style.css', [], filemtime(plugin_dir_path(__FILE__) . 'assets/css/mw-style.css'), 'all');
    wp_enqueue_style('milesweb-admin-css');
    // Register JavaScript
    wp_register_script('milesweb-admin-js', MILESWEB_PLUGIN_URL . 'assets/js/mw-script.js', ['jquery'], filemtime(plugin_dir_path(__FILE__) . 'assets/js/mw-script.js'), true);
    wp_enqueue_script('milesweb-admin-js');
    // Register additional JavaScript files (for charting)
    wp_register_script('milesweb-chart-js', MILESWEB_PLUGIN_URL . 'assets/js/chart-script.js', ['jquery'], filemtime(plugin_dir_path(__FILE__) . 'assets/js/chart-script.js'), true);
    wp_enqueue_script('milesweb-chart-js');
    wp_register_script('chart-js', MILESWEB_PLUGIN_URL . 'assets/js/chart.js', ['jquery'], filemtime(plugin_dir_path(__FILE__) . 'assets/js/chart.js'), true);
    wp_enqueue_script('chart-js');
    // Localize script for AJAX
    wp_localize_script('milesweb-admin-js', 'mileswebAjax', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('milesweb_ajax_nonce'),
    ]);
    // Fetch storage data dynamically and localize for chart
    $storage_info = milesweb_get_storage_info();
    wp_localize_script('milesweb-chart-js', 'storageData', [
        'data' => $storage_info,
    ]);
}
add_action('admin_enqueue_scripts', 'milesweb_enqueue_assets');
// Plugin activation hook
function milesweb_plugin_activate() {
    // Add custom functionality on plugin activation, like adding options or creating database tables
    if (!get_option('milesweb_plugin_installed')) {
        add_option('milesweb_plugin_installed', true);
    }
}
register_activation_hook(__FILE__, 'milesweb_plugin_activate');
// Plugin deactivation hook
function milesweb_plugin_deactivate() {
    // Add custom functionality on plugin deactivation
    delete_option('milesweb_plugin_installed');
}
register_deactivation_hook(__FILE__, 'milesweb_plugin_deactivate');
