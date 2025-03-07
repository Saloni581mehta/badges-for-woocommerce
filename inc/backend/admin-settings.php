<?php

add_filter('woocommerce_settings_tabs_array', 'bgfw_add_settings_tab', 50);
function bgfw_add_settings_tab($settings_tabs)
{
    $settings_tabs['bgfw'] = __('Badges', 'badges-for-woocommerce');
    return $settings_tabs;
}

