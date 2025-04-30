<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit; // Exit if accessed directly.
}
// Delete plugin options
delete_option('maintenance_mode_enabled');
delete_option('force_https_redirect');
delete_option('file_editing_disabled');
delete_option('disable_xmlrpc');
// Delete user meta data for login tracking
// global $wpdb;
// $wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key = 'last_login'");
