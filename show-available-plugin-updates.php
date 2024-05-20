<?php
/**
 * Plugin Name:     Show Available Plugin Updates
 * Plugin URI:      https://mpress.cc
 * Description:     Create a simple API endpoint to display available plugin updates.
 * Author:          Mateusz Zadorożny
 * Author URI:      https://mpress.cc
 * Text Domain:     show-available-plugin-updates
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Show_Available_Plugin_Updates
 */

// Register the custom endpoint
add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/plugin-updates', array (
        'methods' => 'GET',
        'callback' => 'get_plugins_with_updates',
        'permission_callback' => '__return_true', // Ensure proper permission handling
    )
    );
});

// Callback function to get plugins with available updates
function get_plugins_with_updates()
{
    // Check for updates
    include_once (ABSPATH . 'wp-admin/includes/update.php');
    wp_update_plugins();

    // Get the list of plugins
    $all_plugins = get_plugins();
    $update_plugins = get_site_transient('update_plugins');

    $plugins_with_updates = array();

    // Check for available updates
    if (!empty($update_plugins->response)) {
        foreach ($update_plugins->response as $plugin_file => $plugin_data) {
            if (isset($all_plugins[$plugin_file])) {
                $plugins_with_updates[$plugin_file] = $all_plugins[$plugin_file];
                $plugins_with_updates[$plugin_file]['new_version'] = $plugin_data->new_version;
            }
        }
    }

    return $plugins_with_updates;
}

