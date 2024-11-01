<?php

/*
 * Install
 */

// Exit if accessed directly
defined('ABSPATH') || exit;



register_activation_hook(WTBP_CE_FILE, 'wtbp_ce_install');

function wtbp_ce_install() {
    global $wtbp_ce_settings;

    $options = array();

    // Save the default options
    foreach (wtbp_ce_get_registered_settings() as $settings) {

        foreach ($settings as $option) {
            if (
                    isset($option['std']) &&
                    !empty($option['std'])
            ) {
                $options[$option['id']] = $option['std'];
            }
        }
    }

    update_option('wtbp_ce_settings', array_merge($options, $wtbp_ce_settings));
}

?>