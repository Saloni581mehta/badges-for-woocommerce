<?php

defined('ABSPATH') or die('No script kiddies please!!');
/*
 * Plugin Name: Badges For WooCommerce
 * Plugin URI: https://example.com/wordpress-plugins/badges-woocommerce/
 * Description: A plugin to show badges in your WooCommerce store.
 * Version: 	1.0.1
 * Author:     Saloni
 * Author URI:  https://example.com/
 * Domain Path: /languages
 * Text Domain: badges-for-woocommerce
 * Requires at least: 3 or higher
 * Requires PHP:      7.2 or higher
 * WC requires at least: 5.0
 * WC tested up to: 9.7.1
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 */


 /*
*  checking high performance order storage
 */

add_action( 'before_woocommerce_init', 'bgfw_check_hpos_compatible');
 
function bgfw_check_hpos_compatible() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
}

/*
* check plugin dependencies
*/
register_activation_hook( __FILE__, 'bgfw_check_wooactivation' );

function bgfw_check_wooactivation() {
    if ( !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
        update_option( 'bgfw_needs_wc', true );
    }
}

add_action( 'admin_init', 'bgfw_self_deactivate_if_needed' );

function bgfw_self_deactivate_if_needed() {
    if ( get_option( 'bgfw_needs_wc' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );

        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
        delete_option( 'bgfw_needs_wc' );

        add_action( 'admin_notices', 'bgfw_missing_wc_notice' );
    }
}

function bgfw_missing_wc_notice() {
    echo '<div class="notice notice-error is-dismissible">';
    echo '<p>' . esc_html__( 'This plugin requires WooCommerce to be installed and activated first. Please activate WooCommerce.', 'badges-for-woocommerce' ) . '</p>';
    echo '</div>';
}

/*
 * Let's change the sale discount
 */
add_filter('woocommerce_sale_flash', 'bgfw_discount_text', 10, 2);

function bgfw_discount_text($html, $flash) {
    $html = $html . 'off';
    return $html;
}