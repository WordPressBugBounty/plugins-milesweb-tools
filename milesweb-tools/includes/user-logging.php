<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
// Save user login timestamp
add_action('wp_login', 'milesweb_log_user_login', 10, 2);
function milesweb_log_user_login($user_login, $user) {
    update_user_meta($user->ID, 'last_login', time());
}
