<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
?>
<div class="wrap">
    <h1>MilesWeb Settings</h1>
    <div class="mw-row mw-justify-content-space-between">
        <!-- Maintenance Mode -->
        <div class="mw-row mw-col-12 mw-col-xl-3 mw-col-sm-6">
            <div class="mw-row mw-card mw-justify-content-space-between mw-align-items-center">
                <div class="mw-row mw-col-xl-8 mw-col-10">
                    <h2 class="av-setting-h3">Maintenance Mode</h2>
                    <p>Enable or disable maintenance mode for your site.</p>
                </div>
                <label class="milesweb-toggle">
                    <input type="checkbox" id="maintenance_mode_enabled"
                        <?php checked(get_option('maintenance_mode_enabled', false), true); ?> />
                    <span class="milesweb-slider"></span>
                </label>
            </div>
        </div>
        <!-- Force HTTPS Redirect -->
        <div class="mw-row mw-col-12 mw-col-xl-3 mw-col-sm-6">
            <div class="mw-row mw-card mw-justify-content-space-between mw-align-items-center">
                <div class="mw-row mw-col-xl-8 mw-col-10">
                    <h2 class="av-setting-h3">Force HTTPS Redirect</h2>
                    <p>Force all HTTP traffic to redirect to HTTPS.</p>
                </div>
                <label class="milesweb-toggle">
                    <input type="checkbox" id="force_https_redirect"
                        <?php checked(get_option('force_https_redirect', false), true); ?> />
                    <span class="milesweb-slider"></span>
                </label>
            </div>
        </div>
        <!-- Disable File Editing -->
        <div class="mw-row mw-col-12 mw-col-xl-3 mw-col-sm-6">
            <div class="mw-row mw-card mw-justify-content-space-between mw-align-items-center">
                <div class="mw-row mw-col-xl-8 mw-col-10">
                    <h2 class="av-setting-h3">Disable File Editing</h2>
                    <p>Prevent file editing via the WordPress admin dashboard.</p>
                </div>
                <label class="milesweb-toggle">
                    <input type="checkbox" id="file_editing_disabled"
                        <?php checked(get_option('file_editing_disabled', false), true); ?> />
                    <span class="milesweb-slider"></span>
                </label>
            </div>
        </div>
        <!-- Disable XML-RPC -->
        <div class="mw-row mw-col-12 mw-col-xl-3 mw-col-sm-6">
            <div class="mw-row mw-card mw-justify-content-space-between mw-align-items-center">
                <div class="mw-row mw-col-xl-8 mw-col-10">
                    <h2 class="av-setting-h3">Disable XML-RPC</h2>
                    <p>Disable XML-RPC functionality.</p>
                </div>
                <label class="milesweb-toggle">
                    <input type="checkbox" id="disable_xmlrpc"
                        <?php checked(get_option('disable_xmlrpc', false), true); ?> />
                    <span class="milesweb-slider"></span>
                </label>
            </div>
        </div>
       <!-- Force Footer Option -->
        <div class="mw-row mw-col-12 mw-col-xl-3 mw-col-sm-6">
            <div class="mw-row mw-card mw-justify-content-space-between mw-align-items-center">
                <div class="mw-row mw-col-xl-8 mw-col-10">
                    <h2 class="av-setting-h3">Complete Web Protection</h2>
                    <p>Integrated real-time scanning with protection against various threats.</p>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=milesweb-security')); ?>">View Malware Security Reports</a>
                </div>

                <label class="milesweb-toggle">
                    <input type="checkbox" id="mw_force_footer_enabled" <?php checked(get_option('mw_force_footer_enabled', true), true); ?> />
                    <span class="milesweb-slider"></span>
                </label>
            </div>
        </div>
        <!-- Active Theme -->
        <?php $theme_info = milesweb_get_active_theme_info(); ?>
        <div class="mw-col-12 mw-col-xl-5 d-flex">
            <div class="mw-card flex-1 mw-overflow-md">
                <div class="mw-row">
                    <div class="mw-col-md-6">
                        <h2 class="mw-h3">Active Theme</h2>
                        <p class=""><strong>Name:</strong> <?php echo esc_html($theme_info['name']); ?></p>
                        <p><strong>Version:</strong> <?php echo esc_html($theme_info['version']); ?></p>
                        <p><strong>Author:</strong> <?php echo esc_html($theme_info['author']); ?></p>
                        <p><strong>Theme URI:</strong> <a class="av-link-overflow" href="<?php echo esc_url($theme_info['theme_uri']); ?>" target="_blank"><?php echo esc_html($theme_info['theme_uri']); ?></a></p>
                    </div>
                    <div class="mw-col-md-6 d-flex mw-justify-content-center">
                        <p class="m-0 pl-10"><strong class="av-scr-h4">Screenshot:</strong><br>
                            <?php if (!empty($theme_info['screenshot'])) : ?>
                                <img class="img-fluid av-screenshot" src="<?php echo esc_url($theme_info['screenshot']); ?>" width="240" alt="Theme Screenshot">
                            <?php else : ?>
                                No screenshot available.
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Third Column: Last 5 User Logins -->
        <div class="mw-col-12 mw-col-xl-4 d-flex">
            <!-- User Activity Section -->
            <div class="mw-card flex-1">
                <h3 class="mw-h3">Last 5 User Logins</h3>
                    <?php
                global $wpdb; // Global wpdb object
                // Define cache key and group
                $cache_key = 'last_login_users_top_5';
                $cache_group = 'user_login_data';
                // Try to get cached results
                $results = wp_cache_get($cache_key, $cache_group);
                if (false === $results) {
                    // Secure database query with prepare() to prevent SQL injection
                    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is implemented with wp_cache_get and wp_cache_set
                    $results = $wpdb->get_results(
                        $wpdb->prepare(
                            "SELECT um.user_id, CAST(um.meta_value AS UNSIGNED) AS last_login, u.user_login
                            FROM {$wpdb->usermeta} um
                            INNER JOIN {$wpdb->users} u ON um.user_id = u.ID
                            WHERE um.meta_key = %s
                            ORDER BY last_login DESC
                            LIMIT %d",
                            'last_login',
                            5
                        )
                    );
                    // Store result in cache (cache for 12 hours)
                    wp_cache_set($cache_key, $results, $cache_group, 12 * HOUR_IN_SECONDS);
                }
                    if (!empty($results)) {
                        echo '<div class="mw-flex-flow-column">';
                        echo '<div class="mw-flex"><div class="mw-table-cell"><strong>' . esc_html__('User', 'milesweb-tools') . '</strong></div>';
                        echo '<div class="mw-table-cell"><strong>' . esc_html__('Last Login', 'milesweb-tools') . '</strong></div></div>';
                        foreach ($results as $row) {
                            $user_login = esc_html($row->user_login); // 🔹 Escape output
                            $last_login = esc_html(gmdate('Y-m-d H:i:s', $row->last_login)); // 🔹 Escape output
                            echo '<div class="mw-flex">';
                            echo '<div class="mw-table-cell">' .esc_html($user_login) . '</div>';
                            echo '<div class="mw-table-cell">' . esc_html($last_login) . '</div>';
                            echo '</div>';
                        }
                        echo '</div>';
                    } else {
                        echo '<p>' . esc_html__('No login data available.', 'milesweb-tools') . '</p>';
                    }
                    ?>
            </div>
        </div>
    </div>
    <div class="mw-row mw-justify-content-space-between">
        <!-- First Column WordPress Version -->
        <div class="mw-col-12 mw-col-xl-4 d-flex">
            <div class="mw-card flex-1 d-flex mw-flex-wrap av-version-box">
                <div class="mw-col-12">
                    <h3><img style="vertical-align: middle;padding-right: 4px;" src="<?php echo esc_url(MILESWEB_PLUGIN_ASSETS_URL) ?>images/icon-wp.svg" alt="WordPress Version | MilesWeb"> WordPress Version</h3>
                    <p><strong class="mw-version"><?php echo esc_html(get_bloginfo('version')); ?> </strong></p>
                    <p><b><?php echo wp_kses_post(milesweb_check_wordpress_version()); ?></b></p>
                </div>
                <div class="pt-10 mw-col-12 av-version-php">
                    <h3><img style="vertical-align: middle;padding-right: 4px;" src="<?php echo esc_url(MILESWEB_PLUGIN_ASSETS_URL . 'images/icon-php.svg'); ?>" alt="PHP Version | MilesWeb"> PHP Version</h3>
                    <p><strong class="mw-version"><?php echo esc_html(phpversion()); ?></strong></p>
                </div>
            </div>
        </div>
        <div class="mw-row mw-col-12 mw-col-xl-8 d-flex">
            <div class="mw-card mw-col mw-row flex-1 mw-align-items-center">
                <?php
                // Fetch categorized storage info dynamically
                $storage_info = milesweb_get_storage_info();
                if ($storage_info): ?>
                    <div class="mw-row mw-col-md-6">
                        <div class="mw-col-12">
                            <h3 class="mw-h3">Storage Usage Information</h3>
                            <div class="mw-card-body">
                                <div class="mw-flex-flow-column">
                                    <div class="mw-flex">
                                        <div class="mw-table-cell"><strong>Category</strong></div>
                                        <div class="mw-table-cell"><strong>Storage Used (MB)</strong></div>
                                    </div>
                                    <?php foreach ($storage_info as $category => $size): ?>
                                        <?php if ($category !== 'Total'): // Exclude Total from table
                                        ?>
                                            <div class="mw-flex">
                                                <div class="mw-table-cell"><?php echo esc_html($category); ?></div>
                                                <div class="mw-table-cell"><?php echo esc_html($size); ?> MB</div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <div class="mw-flex mw-table-total">
                                        <div class="mw-table-cell"><strong>Total</strong></div>
                                        <div class="mw-table-cell"><?php echo esc_html($storage_info['Total']); ?> MB</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <p>No storage information available.</p>
                    <?php endif; ?>
                    </div>
                    <div class="mw-col-md-6 pt-md-24">
                        <canvas id="myChart" width="300" height="300"></canvas>
                    </div>
                </div>
            </div>


        <?php $plugins = milesweb_get_plugins_info(); ?>
        <div class="mw-row mw-col-12 mw-col-xl-6 d-flex">
            <div class="mw-row mw-card mw-col-12 flex-1 mw-overflow-md">
                <div class="mw-row mw-col-12">
                    <h2 class="mw-h3">Plugins</h2>
                    <div class="mw-col-12">
                        <div class="mw-card-body">
                            <div class="mw-flex-flow-column">
                                <div class="mw-flex">
                                    <div class="mw-table-cell"><strong>Name</strong></div>
                                    <div class="mw-table-cell"><strong>Version</strong></div>
                                    <div class="mw-table-cell"><strong>Author</strong></div>
                                    <div class="mw-table-cell"><strong>Status</strong></div>
                                    <div class="mw-table-cell"><strong>Size (MB)</strong></div>
                                </div>
                                <?php foreach ($plugins as $plugin) : ?>
                                    <div class="mw-flex">
                                        <div class="mw-table-cell"><?php echo esc_html($plugin['name']); ?></div>
                                        <div class="mw-table-cell"><?php echo esc_html($plugin['version']); ?></div>
                                        <div class="mw-table-cell"><?php echo esc_html($plugin['author']); ?></div>
                                        <div class="mw-table-cell"><?php echo esc_html($plugin['is_active']); ?></div>
                                        <div class="mw-table-cell"><?php echo esc_html($plugin['size']); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php $inactive_themes = milesweb_get_inactive_themes_info(); ?>
        <div class="mw-col-12 mw-col-xl-6 d-flex">
            <div class="mw-card flex-1">
                <h2 class="mw-h3">Inactive Themes</h2>
                <?php if (!empty($inactive_themes)) : ?>
                    <div class="mw-card-body">
                        <div class="mw-flex-flow-column">
                            <div class="mw-flex">
                                <div class="mw-table-cell"><strong>Name</strong></div>
                                <div class="mw-table-cell"><strong>Version</strong></div>
                                <div class="mw-table-cell"><strong>Size (MB)</strong></div>
                            </div>
                            <?php foreach ($inactive_themes as $theme) : ?>
                                <div class="mw-flex">
                                    <div class="mw-table-cell"><?php echo esc_html($theme['name']); ?></div>
                                    <div class="mw-table-cell"><?php echo esc_html($theme['version']); ?></div>
                                    <div class="mw-table-cell"><?php echo esc_html($theme['size']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else : ?>
                    <p>No inactive themes found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>