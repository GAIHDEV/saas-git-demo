<?php
/**
 * Plugin Name: SaaS [DEMO GIT]
 * Plugin URI: https://aibusinessschool.com/
 * Description: A simple SaaS [DEMO GIT] plugin.
 * Version: 1.0.0
 * Author: Süleyman Ekici
 * Author URI: https://aibusinessschool.com/
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants.
define( 'MY_PLUGIN_NAME', 'saas-git-demo' );
define( 'MY_PLUGIN_FILE', __FILE__ );
define( 'MY_PLUGIN_DIR', plugin_dir_path( MY_PLUGIN_FILE ) );
define( 'MY_PLUGIN_API_ENDPOINT', 'https://github.com/GAIHDEV/saas-git-demo/releases/latest' );

// Register the plugin's shortcode.
add_shortcode( 'saas-git-demo', 'my_plugin_saas_git' );

function my_plugin_saas_git( $atts ) {
    // Set default attributes.
    $atts = shortcode_atts( array(
        'name' => 'World',
    ), $atts );

    // Return the hello world message.
    return 'Hello, ' . esc_html( $atts['name'] ) . '!';
}

// Add a filter to check for updates to the plugin.
add_filter( 'pre_set_site_transient_update_plugins', 'my_plugin_check_for_updates' );

function my_plugin_check_for_updates( $transient ) {
    // Check if the transient has already been checked.
    if ( isset( $transient->checked[ MY_PLUGIN_FILE ] ) ) {
        return $transient;
    }

    // Check if the GitHub API key is set.
    define( 'YOUR_GITHUB_API_TOKEN', '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' );
    define( 'MY_PLUGIN_API_ENDPOINT', 'https://github.com/GAIHDEV/saas-git-demo/releases/latest?access_token=' . YOUR_GITHUB_API_TOKEN );

    // Check for updates.
    $response = wp_remote_get( MY_PLUGIN_API_ENDPOINT, array(
        'headers' => array(
            'Authorization' => 'Token ' . YOUR_GITHUB_API_TOKEN,
        ),
    ) );

    if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body );

        // Check if the latest release is newer than the current version.
        if ( version_compare( MY_PLUGIN_VERSION, $data->tag_name, '<' ) ) {
            $plugin = array(
                'slug' => MY_PLUGIN_NAME,
                'new_version' => $data->tag_name,
                'url' => 'https://github.com/GAIHDEV/saas-git-demo/',
                'package' => $data->zipball_url,
            );

            $transient->response[ MY_PLUGIN_FILE ] = (object) $plugin;
        }
    }

    return $transient;
}
