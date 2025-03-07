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
    $regular_price = (float) $product->get_regular_price();
    $sale_price = (float) $product->get_sale_price();
    if ($sale_price != 0 || !empty($sale_price)) {
        $percentage = round(100 - ($sale_price / $regular_price * 100)) . '%';
    }
    return $percentage;
}