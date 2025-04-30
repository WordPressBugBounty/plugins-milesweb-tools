<?php
if (!defined('ABSPATH')) {
    exit;
}

// Handle AJAX requests
add_action('wp_ajax_milesweb_save_setting', 'milesweb_save_setting');

function milesweb_save_setting() {
    check_ajax_referer('milesweb_ajax_nonce', 'nonce');

    $setting = isset($_POST['setting']) ? sanitize_text_field(wp_unslash($_POST['setting'])) : '';
    $value = isset($_POST['value']) ? filter_var(wp_unslash($_POST['value']), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : false;

    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Unauthorized']);
    }

    // Allowed settings
    $allowed_settings = ['maintenance_mode_enabled', 'force_https_redirect', 'file_editing_disabled', 'disable_xmlrpc'];

    if (in_array($setting, $allowed_settings, true)) {
        update_option($setting, $value);

        // Force a reload to apply XML-RPC settings
        if ($setting === 'disable_xmlrpc') {
            delete_transient('rest_api_init'); // Clear API cache
        }

        wp_send_json_success(['message' => ucfirst(str_replace('_', ' ', $setting)) . ' updated successfully.']);
    } else {
        wp_send_json_error(['message' => 'Invalid setting']);
    }
}
