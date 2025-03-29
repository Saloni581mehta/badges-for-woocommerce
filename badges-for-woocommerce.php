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
   
/**
   * Load Text Domain
*/
add_action( 'plugins_loaded', 'bgfw_load_textdomain' );
  
function bgfw_load_textdomain() {
      load_plugin_textdomain( 'badges-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
} 


add_filter('woocommerce_sale_flash', 'bgfw_discount_text', 10, 2);

function bgfw_discount_text($html, $flash)
{
    global $product;
    ob_start();
    if ($product->is_on_sale() && $product->is_in_stock()) {
        ?>
        <div class="bgfw-sales-badges">
            <?php
            echo '-';
            echo bgfw_sale_price_items($product);
            ?>
        </div>
        <?php
    }
    $data = ob_get_contents();
    $html = $html . $data;
    ob_end_clean();
    return $html;
}
function bgfw_sale_price_items($product)
{
    if ($product->is_type('variable')) {
        // Get all variations
        $variations = $product->get_available_variations();
        $max_percentage = null;

        foreach ($variations as $variation) {
            $variation_product = wc_get_product($variation['variation_id']);

            $regular_price = (float) $variation_product->get_regular_price();
            $sale_price = (float) $variation_product->get_sale_price();

            if ($sale_price && $regular_price > 0) {
                $percentage = round(100 - ($sale_price / $regular_price * 100));
                if ($max_percentage === null || $percentage > $max_percentage) {
                    $max_percentage = $percentage;
                }
            }
        }

        return $max_percentage ? $max_percentage . '%' : '';
    } elseif ($product->is_type('grouped')) {
        // Handle grouped products by checking all child products
        $child_ids = $product->get_children();
        $max_percentage = null;

        foreach ($child_ids as $child_id) {
            $child_product = wc_get_product($child_id);
            $percentage = bgfw_calculate_discount_percentage($child_product);
            if ($percentage !== null && ($max_percentage === null || $percentage > $max_percentage)) {
                $max_percentage = $percentage;
            }
        }

        return $max_percentage !== null ? $max_percentage . '%' : '';
    } else {
        return bgfw_calculate_discount_percentage($product) . '%';

    }
}
function bgfw_calculate_discount_percentage($product)
{
    $regular_price = (float) $product->get_regular_price();
    $sale_price = (float) $product->get_sale_price();

    if ($sale_price && $regular_price > 0) {
        return round(100 - ($sale_price / $regular_price * 100));
    }

    return null;
}

add_action('woocommerce_shop_loop_item_title', 'bgfw_display_discount_badge', 10);

function bgfw_display_discount_badge()
{
    global $product;

    if ($product->is_on_sale() && $product->is_in_stock()) {
        $off_text = __('OFF', 'badges-for-woocommerce');
        echo '<div class="bgfw-sales-badges">-' . bgfw_sale_price_items($product) . ' ' . $off_text . '</div>';
    } else {
        $off_text = __('Out of stock', 'badges-for-woocommerce');
        echo '<div class="bgfw-sales-badges">' . $off_text . '</div>';
    }

}

add_action('woocommerce_single_product_summary', 'bgfw_display_discount_badge', 5);


