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
        // $off_text = __('OFF', 'badges-for-woocommerce');
        // echo '<div class="bgfw-sales-badges">-' . bgfw_sale_price_items($product) . ' ' . $off_text . '</div>';
        // Check if the product was published within the last 7 days
        $publish_date = $product->get_date_created();
        $current_date = current_time('timestamp');
        $days_since_publish = ($current_date - $publish_date->getTimestamp()) / (60 * 60 * 24);

        if ($days_since_publish <= 7) {
            echo '<div class="bgfw-new-badge">' . __('New', 'badges-for-woocommerce') . '</div>';
        }
    } else {
        $off_text = __('Out of stock', 'badges-for-woocommerce');
        echo '<div class="bgfw-out-stock-badge">' . $off_text . '</div>';
    }

}

add_action('woocommerce_single_product_summary', 'bgfw_display_discount_badge', 5);

add_action('wp_enqueue_scripts', 'bgfw_register_frontend_assets');

function bgfw_register_frontend_assets()
{
    wp_enqueue_style('bgfw-frontend-style', plugin_dir_url(__FILE__) . 'assets/css/frontend-style.css', false, '1.0.0');
}

