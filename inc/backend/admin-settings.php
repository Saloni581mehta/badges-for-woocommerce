<?php

add_filter('woocommerce_settings_tabs_array', 'bgfw_add_settings_tab', 50);
function bgfw_add_settings_tab($settings_tabs)
{
    $settings_tabs['bgfw'] = __('Badges', 'badges-for-woocommerce');
    return $settings_tabs;
}
add_action('woocommerce_settings_tabs_bgfw', 'bgfw_display_settings');
function bgfw_display_settings()
{
    woocommerce_admin_fields(bgfw_get_settings());
}
add_action('woocommerce_update_options_bgfw', 'bgfw_save_settings');
function bgfw_save_settings()
{
    woocommerce_update_options(bgfw_get_settings());
}

function bgfw_get_settings()
{
    return array(
        array(
            'title' => __('Badge settings', 'badges-for-woocommerce'),
            'type' => 'title',
            'desc' => __('Configure badges for products.', 'badges-for-woocommerce'),
            'id' => 'bgfw_settings'
        ),
        array(
            'title' => __('Enable discount badges', 'badges-for-woocommerce'),
            'id' => 'bgfw_enable_discount_badges',
            'type' => 'checkbox',
            'default' => 'no',
        ),
        array(
            'title' => __('Discount badge label', 'badges-for-woocommerce'),
            'id' => 'bgfw_discount_badge_label',
            'type' => 'text',
            'default' => 'Sale!',
        ),
        array(
            'title' => __('Discount badges position in archive', 'badges-for-woocommerce'),
            'id' => 'bgfw_discount_archive_position',
            'type' => 'select',
            'options' => array(
                'default' => __('On default', 'badges-for-woocommerce'),
                'above_title' => __('Above title', 'badges-for-woocommerce'),
                'below_title' => __('Below title', 'badges-for-woocommerce'),
                'above_image' => __('Above image', 'badges-for-woocommerce'),
                'below_image' => __('Below image', 'badges-for-woocommerce'),
            ),
            'default' => 'default',
            'desc' => __('Select where the badge should appear on the archive product page.', 'badges-for-woocommerce'),
            'desc_tip' => true,

        ),
        array(
            'title' => __('Discount badge in Product Page', 'badges-for-woocommerce'),
            'id' => 'bgfw_product_page_position',
            'type' => 'select',
            'options' => array(
                'default' => __('On default', 'badges-for-woocommerce'),
                'below_image' => __('Below image', 'badges-for-woocommerce'),
                'before_title' => __('Before title', 'badges-for-woocommerce'),
                'top' => __('Top', 'badges-for-woocommerce'),
            ),
            'default' => 'default',
            'desc' => __('Select where the badge should appear on the single product page.', 'badges-for-woocommerce'),
            'desc_tip' => true,

        ),
        array(
            'title' => __('Enable new badges', 'badges-for-woocommerce'),
            'id' => 'bgfw_enable_new_badges',
            'type' => 'checkbox',
            'default' => 'no',
            'desc' => __('Enable this to show new badge for latest products', 'badges-for-woocommerce'),

        ),
        array(
            'title' => __('Enable out of stock badges', 'badges-for-woocommerce'),
            'id' => 'bgfw_enable_outstock_badges',
            'type' => 'checkbox',
            'default' => 'no',
            'desc' => __('Enable this to show out of stock badge for out of stock products', 'badges-for-woocommerce'),

        ),
        array(
            'type' => 'sectionend',
            'id' => 'bgfw_settings'
        ),
    );
}

