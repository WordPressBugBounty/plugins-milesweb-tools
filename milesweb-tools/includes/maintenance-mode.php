<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Hooks for maintenance mode and styles
add_action('template_redirect', 'milesweb_maintenance_mode');
add_action('wp_enqueue_scripts', 'milesweb_enqueue_maintenance_stylesheet');

/**
 * Enqueues the maintenance mode stylesheet.
 */
function milesweb_enqueue_maintenance_stylesheet() {
    if (get_option('maintenance_mode_enabled', false)) {
        $css_file_path = plugin_dir_path(__DIR__) . 'assets/css/maintenance-mode.css'; // Adjusted path
        $css_file_url  = MILESWEB_PLUGIN_ASSETS_URL . 'css/maintenance-mode.css';

        // Check if the file exists to avoid errors
        $css_version = file_exists($css_file_path) ? filemtime($css_file_path) : time();

        wp_register_style( 'milesweb-maintenance-mode', esc_url($css_file_url), [], $css_version, 'all' );
        wp_enqueue_style('milesweb-maintenance-mode');
    }
}

/**
 * Displays the maintenance mode page if enabled.
 */
function milesweb_maintenance_mode() {
    // Check if maintenance mode is enabled
    if (get_option('maintenance_mode_enabled', false)) {
        // Allow administrators to access the site
        if (current_user_can('manage_options')) {
            return;
        }

        // Retrieve image URLs from WordPress Media Library
        $logo_id = get_option('milesweb_logo_image_id');
        $coming_soon_id = get_option('milesweb_coming_soon_image_id');

        $logo_img = $logo_id ? wp_get_attachment_image($logo_id, 'medium', false, array('alt' => esc_attr__('MilesWeb Logo', 'milesweb-tools'))) : '<img src="' . esc_url(MILESWEB_PLUGIN_ASSETS_URL . 'images/logo.svg') . '" height="48" alt="' . esc_attr__('MilesWeb Logo', 'milesweb-tools') . '" />';
        $coming_soon_img = $coming_soon_id ? wp_get_attachment_image($coming_soon_id, 'large', false, array('alt' => esc_attr__('Coming Soon', 'milesweb-tools'))) : '<img src="' . esc_url(MILESWEB_PLUGIN_ASSETS_URL . 'images/coming-soon.png') . '" width="400" alt="' . esc_attr__('Coming Soon', 'milesweb-tools') . '" />';

        // Maintenance mode page output
        ?>
        <!DOCTYPE html>
        <html lang="<?php echo esc_attr(get_bloginfo('language')); ?>">
        <head>
            <meta charset="<?php echo esc_attr(get_bloginfo('charset')); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo esc_html__('Coming Soon', 'milesweb-tools'); ?></title>
            <?php wp_head(); ?>
        </head>
        <body>
            <div class="coming-soon">
                <?php echo wp_kses_post($logo_img); ?>
                <div><?php echo wp_kses_post($coming_soon_img); ?></div>
                <h1><?php echo esc_html__('Coming Soon', 'milesweb-tools'); ?></h1>
                <p><?php echo esc_html__('New WordPress website is being built and will be published soon', 'milesweb-tools'); ?></p>
                <a href="mailto:<?php echo esc_attr(get_bloginfo('admin_email')); ?>"><?php echo esc_html__('Contact Us', 'milesweb-tools'); ?></a>
            </div>
            <?php wp_footer(); ?>
        </body>
        </html>
        <?php

        status_header(503);
        header('Retry-After: 3600');
        exit;
    }
}
