<?php

defined('ABSPATH') or die('No script kiddies please!!');
/*
 * Plugin Name: Badges For WooCommerce
 * Plugin URI: https://example.com/wordpress-plugins/badges-woocommerce/
 * Description: A plugin to show badges in your WooCommerce store.
 * Version: 	1.0.0
 * Author:     Saloni
 * Author URI:  https://example.com/
 * Domain Path: /languages
 * Text Domain: badges-for-woocommerce
 * Requires at least: 6.6 or higher
 * Requires PHP:      7.2 or higher
 * WC requires at least: 5.0
 * WC tested up to: 9.7.1
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 */

register_activation_hook(__FILE__, 'bgfw_activation_settings');

function bgfw_activation_settings()
{
    /**
     * Check if WooCommerce is activated
     */
    if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(__('Sorry, but this plugin requires WooCommerce in order to work.So please ensure that WooCommerce is both installed and activated.', 'http://wordpress.org/extend/plugins/woocommerce/'), 'Plugin dependency check', array('back_link' => true));
    }
}
