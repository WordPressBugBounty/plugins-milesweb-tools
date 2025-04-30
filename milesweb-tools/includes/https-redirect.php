<?php
if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// Force HTTPS Redirect (Frontend & Admin)
add_action('init', 'milesweb_force_https_redirect', 1);
function milesweb_force_https_redirect() {
    // Skip redirect for AJAX requests
    if (wp_doing_ajax()) {
        return;
    }

    // Get HTTPS setting
    $force_https = filter_var(get_option('force_https_redirect', false), FILTER_VALIDATE_BOOLEAN);

    // Redirect if not HTTPS
    if ($force_https && !is_ssl()) {
        $host = isset($_SERVER['HTTP_HOST']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST'])) : '';
        $uri  = isset($_SERVER['REQUEST_URI']) ? esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])) : '';

        // Now sanitize after unslashing
        $host = sanitize_text_field($host);
        $uri  = esc_url_raw($uri);

        // Ensure valid values before redirecting
        if (!empty($host) && !empty($uri)) {
            $redirect_url = 'https://' . $host . $uri;
            wp_safe_redirect($redirect_url, 301);
            exit;
        }
    }
}
