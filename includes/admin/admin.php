<?php

/**
 * Main WP Custom Emails Admin Class
 *
 */
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

class WP_Custom_Emails_Admin {

    // @var string URL to the admin javascript directory
    public $js_url = '';

    // @var string URL to the admin css directory
    public $css_url = '';

    // the main admin loader
    public function __construct() {
        $this->setup_globals();
        $this->setup_hooks();
    }

    
    // admin globals
    private function setup_globals() {

        $this->js_url = WTBP_CE_URL . 'assets/js/'; // Admin js URL

        $this->css_url = WTBP_CE_URL . 'assets/css/'; // Admin css URL
    }

    
    // setup the admin hooks, actions and filters
    private function setup_hooks() {

        /** admin submenu ********************************************************** */
        add_action('admin_menu', array($this, 'admin_menu'), 10);

        /** enqueue scripts ********************************************************** */
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts')); // Enqueue admin js and css

        /** register WPML single string ********************************************************** */
        add_action('wtbp_ce_settings_after_validation', array($this, 'wpml_register_string'));
    }

    // Register admin submenu for the general options
    public function admin_menu() {

        add_submenu_page('options-general.php', __('WP Custom Emails', WTBP_CE_DOMAIN), __('WP Custom Emails', WTBP_CE_DOMAIN), 'create_users', WTBP_CE_DOMAIN, 'wtbp_ce_settings_page');
    }

    // add admin js script
    public function admin_scripts($hook) {


        if ($hook === 'settings_page_wp-custom-emails') {

            // Enqueue JS
            wp_enqueue_script(WTBP_CE_DOMAIN . '-admin', $this->js_url . 'admin-script.js');

            // Enqueue CSS
            wp_enqueue_style(WTBP_CE_DOMAIN . '-admin', $this->css_url . 'admin-style.css');
        }
    }
    
    
     // register WPML single string
    public function wpml_register_string($input) {

        if (class_exists('WPML_String_Translation')) {

            foreach (wtbp_ce_get_registered_settings() as $section => $settings) {
                foreach ($settings as $option) {
                    if(array_key_exists($option['id'], $input)){
                        if (isset($option['class']) && $option['class'] === 'wtbp-ce-hidden-field') {
                            if ($option['type'] === 'text') {
                                icl_register_string(WTBP_CE_NAME, $option['id'], $input[$option['id']]);
                            }

                            if ($option['type'] === 'textarea') {
                                icl_register_string(WTBP_CE_NAME, $option['id'], $input[$option['id']]);
                            }
                        }
                    }
                }
            }
        }
    }

}

//Init admin class
new WP_Custom_Emails_Admin();

