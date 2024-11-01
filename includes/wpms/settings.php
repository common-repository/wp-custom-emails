<?php

// Exit if accessed directly
defined('ABSPATH') || exit;


class WP_Custom_Emails_WPMS_Settings extends WP_Custom_Emails_Core_Settings {
    
    
    public function __construct() {
        $this->setup_hooks();
    }

    

    /*
     * Add settings
     */

    public function add_settings($settings) {
        
        $new_settings = array('wpms' => apply_filters('wtbp_ce_settings_wpms', array(
            
                // change administrator email
                'ms_admin_changes_email_head' => array(
                    'id' => 'ms_admin_changes_email_head',
                    'class' => 'wtbp-header',
                    'name' => '<h3>' . __('Admin changes email', WTBP_CE_DOMAIN) . '</h3>',
                    'type' => 'header',
                ),
                'ms_admin_changes_email_mode' => array(
                    'id' => 'ms_admin_changes_email_mode',
                    'name' => '<strong>' . __('Mode', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => 'select',
                    'options' => WP_Custom_Emails_WPMS_Defaults::settings_modes(),
                    'desc' => WP_Custom_Emails_WPMS_Defaults::settings_show_details(),
                    'std' => 'bypass'
                ),
                'ms_admin_changes_email_title' => array(
                    'id' => 'ms_admin_changes_email_title',
                    'class' => 'wtbp-ce-hidden-field',
                    'name' => '<strong>' . __('Title', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => 'text',
                    'desc' => __('You can use the following placeholders', WTBP_CE_DOMAIN) . ': <code>{blog_name}</code>',
                    'std' => WP_Custom_Emails_WPMS_Defaults::default_ms_admin_changes_email_title()
                ),
                'ms_admin_changes_email_mes' => array(
                    'id' => 'ms_admin_changes_email_mes',
                    'class' => 'wtbp-ce-hidden-field',
                    'name' => '<strong>' . __('Message', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => wtbp_ce_set_type(),
                    'desc' => __('You can use the following placeholders', WTBP_CE_DOMAIN) . ': <code>{user_name}</code>, <code>{admin_url}</code>, <code>{new_email}</code>, <code>{site_name}</code>, <code>{site_url}</code>',
                    'std' => WP_Custom_Emails_WPMS_Defaults::default_ms_admin_changes_email_message(),
                ),
            
            
                // user changes emails
                'ms_user_changes_emails_head' => array(
                    'id' => 'ms_user_changes_emails_head',
                    'class' => 'wtbp-header',
                    'name' => '<h3>' . __('User changes email', WTBP_CE_DOMAIN) . '</h3>',
                    'type' => 'header',
                ),
                'ms_user_changes_emails_mode' => array(
                    'id' => 'ms_user_changes_emails_mode',
                    'name' => '<strong>' . __('Mode', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => 'select',
                    'options' => WP_Custom_Emails_WPMS_Defaults::settings_modes(),
                    'desc' => WP_Custom_Emails_WPMS_Defaults::settings_show_details(),
                    'std' => 'bypass'
                ),
                'ms_user_changes_emails_title' => array(
                    'id' => 'ms_user_changes_emails_title',
                    'class' => 'wtbp-ce-hidden-field',
                    'name' => '<strong>' . __('Title', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => 'text',
                    'desc' => __('You can use the following placeholders', WTBP_CE_DOMAIN) . ': <code>{blog_name}</code>',
                    'std' => WP_Custom_Emails_WPMS_Defaults::default_ms_admin_changes_email_title()
                ),
                'ms_user_changes_emails_mes' => array(
                    'id' => 'ms_user_changes_emails_mes',
                    'class' => 'wtbp-ce-hidden-field',
                    'name' => '<strong>' . __('Message', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => wtbp_ce_set_type(),
                    'desc' => __('You can use the following placeholders', WTBP_CE_DOMAIN) . ': <code>{user_name}</code>, <code>{admin_url}</code>, <code>{new_email}</code>, <code>{site_name}</code>, <code>{site_url}</code>',
                    'std' => WP_Custom_Emails_WPMS_Defaults::default_ms_admin_changes_email_message(),
                ),
                    )
            )
        );


        $output = array_merge($new_settings, $settings);

        return $output;
    }
    
    /*
     * Add settings tab
     */
    public function add_settings_tab($tabs) {

        if (!isset($tabs['wpms'])) {
            $tabs['wpms'] = __('Multisite', WTBP_CE_DOMAIN);
        }

        return $tabs;
    }
    
    
}

new WP_Custom_Emails_WPMS_Settings();

?>