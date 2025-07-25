<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Only run on frontend (avoid admin and login pages)
if (!is_admin() && !is_login_page()) {
    add_action('wp_footer', 'mw_force_footer_output', 100);
}
function get_cpanel_username_from_path() {
    $path = ABSPATH; // or use __DIR__ if inside plugin
    $parts = explode('/', trim($path, '/'));

    // Find "home" and return the next part
    $key = array_search('home', $parts);
    if ($key !== false && isset($parts[$key + 1])) {
        return $parts[$key + 1];
    }
    return 'unknown';
}
function mw_force_footer_output() {
    $enabled = filter_var(get_option('mw_force_footer_enabled', true), FILTER_VALIDATE_BOOLEAN);
    if (!$enabled) return;
    ?>
    <div id="force-footer" style="text-align: right;padding: 13px;font-size: 12px;">
        <p style="margin:0;" title="This site is hosted and protected by MilesWeb to detect and block malware in real-time.">Website security powered by <a href="https://www.milesweb.in/" target="_blank">MilesWeb</a></p>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const fallbackBackground = "#f1f1f1";
        const fallbackTextColor = "#222";

        function isDark(color) {
            if (!color) return false;
            let rgb = color.match(/\d+/g);
            if (!rgb || rgb.length < 3) return false;
            const brightness = (parseInt(rgb[0]) * 299 + parseInt(rgb[1]) * 587 + parseInt(rgb[2]) * 114) / 1000;
            return brightness < 130;
        }

        const originalFooter = document.querySelector('footer, #colophon, [class*="footer"]');
        const customFooter = document.getElementById('force-footer');

        if (originalFooter && customFooter) {
            const styles = window.getComputedStyle(originalFooter);
            const bgColor = styles.backgroundColor;
            const textColor = styles.color;

            if (bgColor && !isDark(bgColor)) {
                customFooter.style.backgroundColor = bgColor;
            } else {
                customFooter.style.backgroundColor = fallbackBackground;
            }

            if (textColor && !isDark(textColor)) {
                customFooter.style.color = textColor;
                customFooter.querySelectorAll('*').forEach(el => {
                    el.style.color = textColor;
                });
            } else {
                customFooter.style.color = fallbackTextColor;
                customFooter.querySelectorAll('*').forEach(el => {
                    el.style.color = fallbackTextColor;
                });
            }
        } else if (customFooter) {
            customFooter.style.backgroundColor = fallbackBackground;
            customFooter.style.color = fallbackTextColor;
            customFooter.querySelectorAll('*').forEach(el => {
                el.style.color = fallbackTextColor;
            });
        }
    });
    </script>
    <?php
}

// Helper: Check login page
function is_login_page() {
    return in_array($GLOBALS['pagenow'], ['wp-login.php', 'wp-register.php']);
}
