<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

class WP_Custom_Emails_Core_Settings {

    public function __construct() {
        $this->setup_hooks();
    }

    /**
     * Setup the filters
     */
    protected function setup_hooks() {

        /** Settings  ********************************************************** */
        add_filter('wtbp_ce_settings', array($this, 'add_settings')); // Add settings
        add_filter('wtbp_ce_settings_tabs', array($this, 'add_settings_tab')); // Add settings tab
    }

    /*
     * Add settings
     */

    public function add_settings($settings) {

        $new_settings = array('notifications' => apply_filters('wtbp_ce_settings_notifications', array(
                // password reset
                'user_ret_pass_head' => array(
                    'id' => 'user_ret_pass_head',
                    'class' => 'wtbp-header',
                    'name' => '<h3>' . __('Retrieve Password', WTBP_CE_DOMAIN) . '</h3>',
                    'type' => 'header',
                ),
                'user_ret_pass_mode' => array(
                    'id' => 'user_ret_pass_mode',
                    'name' => '<strong>' . __('Mode', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => 'select',
                    'options' => WP_Custom_Emails_Core_Defaults::settings_modes(),
                    'desc' => WP_Custom_Emails_Core_Defaults::settings_show_details(),
                    'std' => 'bypass'
                ),
                'user_ret_pass_title' => array(
                    'id' => 'user_ret_pass_title',
                    'class' => 'wtbp-ce-hidden-field',
                    'name' => '<strong>' . __('Title', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => 'text',
                    'std' => WP_Custom_Emails_Core_Defaults::default_user_ret_pass_title()
                ),
                'user_ret_pass_mes' => array(
                    'id' => 'user_ret_pass_mes',
                    'class' => 'wtbp-ce-hidden-field',
                    'name' => '<strong>' . __('Message', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => wtbp_ce_set_type(),
                    'desc' => __('You can use the following placeholders', WTBP_CE_DOMAIN) . ': <code>{site_url}</code>, <code>{user_login}</code>, <code>{reset_link}</code>',
                    'std' => WP_Custom_Emails_Core_Defaults::default_user_ret_pass_message(),
                ),
                // new user
                'user_new_user_head' => array(
                    'id' => 'user_new_user_head',
                    'class' => 'wtbp-header',
                    'name' => '<h3>' . __('New User (user)', WTBP_CE_DOMAIN) . '</h3>',
                    'type' => 'header',
                ),
                'user_new_user_mode' => array(
                    'id' => 'user_new_user_mode',
                    'name' => '<strong>' . __('Mode', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => 'select',
                    'options' => WP_Custom_Emails_Core_Defaults::settings_modes(),
                    'desc' => WP_Custom_Emails_Core_Defaults::settings_show_details(),
                    'std' => 'bypass'
                ),
                'user_new_user_title' => array(
                    'id' => 'user_new_user_title',
                    'class' => 'wtbp-ce-hidden-field',
                    'name' => '<strong>' . __('Title', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => 'text',
                    'std' => WP_Custom_Emails_Core_Defaults::default_user_new_user_title()
                ),
                'user_new_user_mes' => array(
                    'id' => 'user_new_user_mes',
                    'class' => 'wtbp-ce-hidden-field',
                    'name' => '<strong>' . __('Message', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => wtbp_ce_set_type(),
                    'desc' => __('You can use the following placeholders', WTBP_CE_DOMAIN) . ': <code>{user_login}</code>, <code>{login_url}</code>, <code>{password_url}</code> (for WP >= 4.3), <code>{user_password}</code> (for WP < 4.3)',
                    'std' => WP_Custom_Emails_Core_Defaults::default_user_new_user_message()
                ),
                // admin new user
                'admin_new_user_head' => array(
                    'id' => 'admin_new_user_head',
                    'class' => 'wtbp-header',
                    'name' => '<h3>' . __('New User (admin)', WTBP_CE_DOMAIN) . '</h3>',
                    'type' => 'header',
                ),
                'admin_new_user_mode' => array(
                    'id' => 'admin_new_user_mode',
                    'name' => '<strong>' . __('Mode', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => 'select',
                    'options' => WP_Custom_Emails_Core_Defaults::settings_modes(),
                    'desc' => WP_Custom_Emails_Core_Defaults::settings_show_details(),
                    'std' => 'bypass'
                ),
                'admin_new_user_title' => array(
                    'id' => 'admin_new_user_title',
                    'class' => 'wtbp-ce-hidden-field',
                    'name' => '<strong>' . __('Title', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => 'text',
                    'std' => WP_Custom_Emails_Core_Defaults::default_admin_new_user_title()
                ),
                'admin_new_user_mes' => array(
                    'id' => 'admin_new_user_mes',
                    'class' => 'wtbp-ce-hidden-field',
                    'name' => '<strong>' . __('Message', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => wtbp_ce_set_type(),
                    'desc' => __('You can use the following placeholders', WTBP_CE_DOMAIN) . ': <code>{site_url}</code>, <code>{user_login}</code>, <code>{user_email}</code>',
                    'std' => WP_Custom_Emails_Core_Defaults::default_admin_new_user_message()
                ),
                // admin password change
                'admin_password_change_head' => array(
                    'id' => 'admin_password_change_head',
                    'class' => 'wtbp-header',
                    'name' => '<h3>' . __('Password Change', WTBP_CE_DOMAIN) . '</h3>',
                    'type' => 'header',
                ),
                'admin_password_change_mode' => array(
                    'id' => 'admin_password_change_mode',
                    'name' => '<strong>' . __('Mode', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => 'select',
                    'options' => WP_Custom_Emails_Core_Defaults::settings_modes(),
                    'desc' => WP_Custom_Emails_Core_Defaults::settings_show_details(),
                    'std' => 'bypass'
                ),
                'admin_password_change_title' => array(
                    'id' => 'admin_password_change_title',
                    'class' => 'wtbp-ce-hidden-field',
                    'name' => '<strong>' . __('Title', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => 'text',
                    'std' => WP_Custom_Emails_Core_Defaults::default_admin_password_change_title()
                ),
                'admin_password_change_mes' => array(
                    'id' => 'admin_password_change_mes',
                    'class' => 'wtbp-ce-hidden-field',
                    'name' => '<strong>' . __('Message', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => wtbp_ce_set_type(),
                    'desc' => __('You can use the following placeholders', WTBP_CE_DOMAIN) . ': <code>{user_login}</code>',
                    'std' => WP_Custom_Emails_Core_Defaults::default_admin_password_change_message()
                ),

                // moderator new comment
                'moderator_new_comment_head' => array(
                    'id' => 'moderator_new_comment_head',
                    'class' => 'wtbp-header',
                    'name' => '<h3>' . __('New comment (moderator)', WTBP_CE_DOMAIN) . '</h3>',
                    'type' => 'header',
                ),
                'moderator_new_comment_mode' => array(
                    'id' => 'moderator_new_comment_mode',
                    'name' => '<strong>' . __('Mode', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => 'select',
                    'options' => WP_Custom_Emails_Core_Defaults::settings_modes(),
                    'desc' => WP_Custom_Emails_Core_Defaults::settings_show_details(),
                    'std' => 'bypass'
                ),
                'moderator_new_comment_title' => array(
                    'id' => 'moderator_new_comment_title',
                    'class' => 'wtbp-ce-hidden-field',
                    'name' => '<strong>' . __('Title', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => 'text',
                    'std' => WP_Custom_Emails_Core_Defaults::default_moderator_new_comment_title()
                ),
                'moderator_new_comment_mes' => array(
                    'id' => 'moderator_new_comment_mes',
                    'class' => 'wtbp-ce-hidden-field',
                    'name' => '<strong>' . __('Message', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => wtbp_ce_set_type(),
                    'desc' => __('You can use the following placeholders', WTBP_CE_DOMAIN) . ': <code>{post_title}</code>,
                        <code>{post_url}</code>, <code>{author_name}</code>, <code>{author_ip}</code>, <code>{author_domain}</code>,
                        <code>{author_email}</code>, <code>{author_url}</code>, <code>{author_whois}</code>, <code>{comment_content}</code>,
                        <code>{approve_url}</code>, <code>{trash_url}</code>, <code>{delete_url}</code>, <code>{spam_url}</code>,
                        <code>{comment_count}</code>, <code>{moderate_url}</code>',
                    'std' => WP_Custom_Emails_Core_Defaults::default_moderator_new_comment_message()
                ),

                // moderator new trackback
                'moderator_new_trackback_head' => array(
                    'id' => 'moderator_new_trackback_head',
                    'class' => 'wtbp-header',
                    'name' => '<h3>' . __('New trackback (moderator)', WTBP_CE_DOMAIN) . '</h3>',
                    'type' => 'header',
                ),
                'moderator_new_trackback_mode' => array(
                    'id' => 'moderator_new_trackback_mode',
                    'name' => '<strong>' . __('Mode', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => 'select',
                    'options' => WP_Custom_Emails_Core_Defaults::settings_modes(),
                    'desc' => WP_Custom_Emails_Core_Defaults::settings_show_details(),
                    'std' => 'bypass'
                ),
                'moderator_new_trackback_title' => array(
                    'id' => 'moderator_new_trackback_title',
                    'class' => 'wtbp-ce-hidden-field',
                    'name' => '<strong>' . __('Title', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => 'text',
                    'std' => WP_Custom_Emails_Core_Defaults::default_moderator_new_trackback_title()
                ),
                'moderator_new_trackback_mes' => array(
                    'id' => 'moderator_new_trackback_mes',
                    'class' => 'wtbp-ce-hidden-field',
                    'name' => '<strong>' . __('Message', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => wtbp_ce_set_type(),
                    'desc' => __('You can use the following placeholders', WTBP_CE_DOMAIN) . ': <code>{post_title}</code>,
                        <code>{post_url}</code>, <code>{website_name}</code>, <code>{website_ip}</code>, <code>{website_hostname}</code>,
                        <code>{website_url}</code>, <code>{trackback_excerpt}</code>, <code>{approve_url}</code>, <code>{trash_url}</code>,
                        <code>{delete_url}</code>, <code>{spam_url}</code>, <code>{comment_count}</code>, <code>{moderate_url}</code>',
                    'std' => WP_Custom_Emails_Core_Defaults::default_moderator_new_trackback_message()
                ),

                // moderator new pingback
                'moderator_new_pingback_head' => array(
                    'id' => 'moderator_new_pingback_head',
                    'class' => 'wtbp-header',
                    'name' => '<h3>' . __('New pingback (moderator)', WTBP_CE_DOMAIN) . '</h3>',
                    'type' => 'header',
                ),
                'moderator_new_pingback_mode' => array(
                    'id' => 'moderator_new_pingback_mode',
                    'name' => '<strong>' . __('Mode', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => 'select',
                    'options' => WP_Custom_Emails_Core_Defaults::settings_modes(),
                    'desc' => WP_Custom_Emails_Core_Defaults::settings_show_details(),
                    'std' => 'bypass'
                ),
                'moderator_new_pingback_title' => array(
                    'id' => 'moderator_new_pingback_title',
                    'class' => 'wtbp-ce-hidden-field',
                    'name' => '<strong>' . __('Title', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => 'text',
                    'std' => WP_Custom_Emails_Core_Defaults::default_moderator_new_pingback_title()
                ),
                'moderator_new_pingback_mes' => array(
                    'id' => 'moderator_new_pingback_mes',
                    'class' => 'wtbp-ce-hidden-field',
                    'name' => '<strong>' . __('Message', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => wtbp_ce_set_type(),
                    'desc' => __('You can use the following placeholders', WTBP_CE_DOMAIN) . ': <code>{post_title}</code>,
                        <code>{post_url}</code>, <code>{website_name}</code>, <code>{website_ip}</code>, <code>{website_hostname}</code>,
                        <code>{website_url}</code>, <code>{pingback_excerpt}</code>, <code>{approve_url}</code>, <code>{trash_url}</code>,
                        <code>{delete_url}</code>, <code>{spam_url}</code>, <code>{comment_count}</code>, <code>{moderate_url}</code>',
                    'std' => WP_Custom_Emails_Core_Defaults::default_moderator_new_pingback_message()
                ),

                // postauthor new comment
                'postauthor_new_comment_head' => array(
                    'id' => 'postauthor_new_comment_head',
                    'class' => 'wtbp-header',
                    'name' => '<h3>' . __('New comment (postauthor)', WTBP_CE_DOMAIN) . '</h3>',
                    'type' => 'header',
                ),
                'postauthor_new_comment_mode' => array(
                    'id' => 'postauthor_new_comment_mode',
                    'name' => '<strong>' . __('Mode', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => 'select',
                    'options' => WP_Custom_Emails_Core_Defaults::settings_modes(),
                    'desc' => WP_Custom_Emails_Core_Defaults::settings_show_details(),
                    'std' => 'bypass'
                ),
                'postauthor_new_comment_title' => array(
                    'id' => 'postauthor_new_comment_title',
                    'class' => 'wtbp-ce-hidden-field',
                    'name' => '<strong>' . __('Title', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => 'text',
                    'std' => WP_Custom_Emails_Core_Defaults::default_postauthor_new_comment_title()
                ),
                'postauthor_new_comment_mes' => array(
                    'id' => 'postauthor_new_comment_mes',
                    'class' => 'wtbp-ce-hidden-field',
                    'name' => '<strong>' . __('Message', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => wtbp_ce_set_type(),
                    'desc' => __('You can use the following placeholders', WTBP_CE_DOMAIN) . ': <code>{post_title}</code>,
                        <code>{post_url}</code>, <code>{author_name}</code>, <code>{author_ip}</code>, <code>{author_domain}</code>,
                        <code>{author_email}</code>, <code>{author_url}</code>, <code>{author_whois}</code>, <code>{comment_content}</code>,
                        <code>{approve_url}</code>, <code>{trash_url}</code>, <code>{delete_url}</code>, <code>{spam_url}</code>,
                        <code>{comment_count}</code>, <code>{moderate_url}</code>',
                    'std' => WP_Custom_Emails_Core_Defaults::default_postauthor_new_comment_message()
                ),

                // postauthor new trackback
                'postauthor_new_trackback_head' => array(
                    'id' => 'postauthor_new_trackback_head',
                    'class' => 'wtbp-header',
                    'name' => '<h3>' . __('New trackback (postauthor)', WTBP_CE_DOMAIN) . '</h3>',
                    'type' => 'header',
                ),
                'postauthor_new_trackback_mode' => array(
                    'id' => 'postauthor_new_trackback_mode',
                    'name' => '<strong>' . __('Mode', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => 'select',
                    'options' => WP_Custom_Emails_Core_Defaults::settings_modes(),
                    'desc' => WP_Custom_Emails_Core_Defaults::settings_show_details(),
                    'std' => 'bypass'
                ),
                'postauthor_new_trackback_title' => array(
                    'id' => 'postauthor_new_trackback_title',
                    'class' => 'wtbp-ce-hidden-field',
                    'name' => '<strong>' . __('Title', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => 'text',
                    'std' => WP_Custom_Emails_Core_Defaults::default_postauthor_new_trackback_title()
                ),
                'postauthor_new_trackback_mes' => array(
                    'id' => 'postauthor_new_trackback_mes',
                    'class' => 'wtbp-ce-hidden-field',
                    'name' => '<strong>' . __('Message', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => wtbp_ce_set_type(),
                    'desc' => __('You can use the following placeholders', WTBP_CE_DOMAIN) . ': <code>{post_title}</code>,
                        <code>{post_url}</code>, <code>{website_name}</code>, <code>{website_ip}</code>, <code>{website_hostname}</code>,
                        <code>{website_url}</code>, <code>{trackback_excerpt}</code>, <code>{approve_url}</code>, <code>{trash_url}</code>,
                        <code>{delete_url}</code>, <code>{spam_url}</code>, <code>{comment_count}</code>, <code>{moderate_url}</code>',
                    'std' => WP_Custom_Emails_Core_Defaults::default_postauthor_new_trackback_message()
                ),

                // postauthor new pingback
                'postauthor_new_pingback_head' => array(
                    'id' => 'postauthor_new_pingback_head',
                    'class' => 'wtbp-header',
                    'name' => '<h3>' . __('New pingback (postauthor)', WTBP_CE_DOMAIN) . '</h3>',
                    'type' => 'header',
                ),
                'postauthor_new_pingback_mode' => array(
                    'id' => 'postauthor_new_pingback_mode',
                    'name' => '<strong>' . __('Mode', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => 'select',
                    'options' => WP_Custom_Emails_Core_Defaults::settings_modes(),
                    'desc' => WP_Custom_Emails_Core_Defaults::settings_show_details(),
                    'std' => 'bypass'
                ),
                'postauthor_new_pingback_title' => array(
                    'id' => 'postauthor_new_pingback_title',
                    'class' => 'wtbp-ce-hidden-field',
                    'name' => '<strong>' . __('Title', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => 'text',
                    'std' => WP_Custom_Emails_Core_Defaults::default_postauthor_new_pingback_title()
                ),
                'postauthor_new_pingback_mes' => array(
                    'id' => 'postauthor_new_pingback_mes',
                    'class' => 'wtbp-ce-hidden-field',
                    'name' => '<strong>' . __('Message', WTBP_CE_DOMAIN) . '</strong>',
                    'type' => wtbp_ce_set_type(),
                    'desc' => __('You can use the following placeholders', WTBP_CE_DOMAIN) . ': <code>{post_title}</code>,
                        <code>{post_url}</code>, <code>{website_name}</code>, <code>{website_ip}</code>, <code>{website_hostname}</code>,
                        <code>{website_url}</code>, <code>{pingback_excerpt}</code>, <code>{approve_url}</code>, <code>{trash_url}</code>,
                        <code>{delete_url}</code>, <code>{spam_url}</code>, <code>{comment_count}</code>, <code>{moderate_url}</code>',
                    'std' => WP_Custom_Emails_Core_Defaults::default_postauthor_new_pingback_message()
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

        if (!isset($tabs['notifications'])) {
            $tabs['notifications'] = __('Manage notifications', WTBP_CE_DOMAIN);
        }

        return $tabs;
    }

}

new WP_Custom_Emails_Core_Settings();
?>