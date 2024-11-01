<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

class WP_Custom_Emails_Core_Defaults {

    
     // types of modes
    public static function settings_modes() {

        $modes = array(
            'bypass' => __('Bypass', WTBP_CE_DOMAIN),
            'custom' => __('Custom', WTBP_CE_DOMAIN),
            'disabled' => __('Disabled', WTBP_CE_DOMAIN)
        );

        return $modes;
    }
    
    // show details texts
    public static function settings_show_details() {

        $html = '<span class="wtbp-show-details wtbp-details button button-secondary">' . __('Show details', WTBP_CE_DOMAIN) . '</span>';
        $html .= '<span class="wtbp-hide-details wtbp-details button button-secondary">' . __('Hide details', WTBP_CE_DOMAIN) . '</span>';
        
        return $html;
    }
    
    // default password reset title
    public static function default_user_ret_pass_title() {

        $title = __('Password Reset');

        return $title;
    }

    // default password reset message
    public static function default_user_ret_pass_message() {

        $message = __('Someone requested that the password be reset for the following account:', WTBP_CE_DOMAIN) . "\r\n\r\n";
        $message .= '{site_url}' . "\r\n\r\n";
        $message .= __('Username:') . ' {user_login}' . "\r\n\r\n";
        $message .= __('If this was a mistake, just ignore this email and nothing will happen.', WTBP_CE_DOMAIN) . "\r\n\r\n";
        $message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
        $message .= '{reset_link}' . "\r\n";

        return $message;
    }

    // default new user title (admin)
    public static function default_admin_new_user_title() {

        $title = __('New User Registration');

        return $title;
    }

    // default new user message (admin)
    public static function default_admin_new_user_message() {

        $message = __('New user registration on your site:') . "\r\n\r\n";
        $message .= '{site_url}' . "\r\n\r\n";
        $message .= __('Username:') . ' {user_login}' . "\r\n";
        $message .= __('Email:') . ' {user_email}' . "\r\n";

        return $message;
    }

    // WP new user title (admin)
    public static function wp_admin_new_user_title() {

        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

        return sprintf(__('[%s] New User Registration'), $blogname);
    }

    // WP new user message (admin)
    public static function wp_admin_new_user_message( $user ) {

        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

        $message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
        $message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
        $message .= sprintf(__('E-mail: %s'), $user->user_email) . "\r\n";

        return $message;
    }

    // WP new user title (user)
    public static function wp_user_new_user_title() {

        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

        return sprintf(__('[%s] Your username and password'), $blogname);
    }

    // WP new user message (user)
    public static function wp_user_new_user_message( $user, $plaintext_pass ) {

        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

        $message  = sprintf(__('Username: %s'), $user->user_login) . "\r\n";
        $message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n";
        $message .= wp_login_url() . "\r\n";

        return $message;
    }

    // default new user title (user)
    public static function default_user_new_user_title() {

        $title = __('Your username and password');

        return $title;
    }

    // default new user message (user)
    public static function default_user_new_user_message() {
        global $wp_version;

        if ( $wp_version >= 4.3 ) {
            $message = __('Username:') . ' {user_login}' . "\r\n";
            $message .= __('To set your password, visit the following address:') . "\r\n";
            $message .= '{password_url}' . "\r\n";
            $message .= '{login_url}' . "\r\n\r\n";
        }
        else {
            $message = __('Username:') . ' {user_login}' . "\r\n";
            $message .= __('Password:') . ' {user_password}' . "\r\n";
            $message .= '{login_url}' . "\r\n\r\n";
        }

        return $message;
    }

    // default password change (admin)
    public static function default_admin_password_change_title() {

        $title = __('Password Changed');

        return $title;
    }

    // default passord change (admin)
    public static function default_admin_password_change_message() {

        $message = __('Password Lost and Changed for user:') . "\r\n";
        $message .= __('Username:') . ' {user_login}' . "\r\n";

        return $message;
    }

    // default new comment (moderator)
    public static function default_moderator_new_comment_title() {

        $title = __('Please moderate new comment', WTBP_CE_DOMAIN);

        return $title;
    }

    // default new comment (moderator)
    public static function default_moderator_new_comment_message() {

        $message  = __('A new comment on the post "{post_title}" is waiting for your approval', WTBP_CE_DOMAIN) . "\r\n";
        $message .= '{post_url}' . "\r\n\r\n";
        $message .= __( 'Author: {author_name} (IP: {author_ip}, {author_domain})', WTBP_CE_DOMAIN ) . "\r\n";
        $message .= __( 'E-mail: {author_email}', WTBP_CE_DOMAIN ) . "\r\n";
        $message .= __( 'URL: {author_url}', WTBP_CE_DOMAIN ) . "\r\n";
        $message .= __( 'Whois: {author_whois}', WTBP_CE_DOMAIN ) . "\r\n";
        $message .= __( "Comment:\r\n{comment_content}", WTBP_CE_DOMAIN ) . "\r\n\r\n";

        $message .= __('Approve it: {approve_url}', WTBP_CE_DOMAIN ) . "\r\n";
        if ( EMPTY_TRASH_DAYS )
            $message .= __('Trash it: {trash_url}', WTBP_CE_DOMAIN ) . "\r\n";
        else
            $message .= __('Delete it: {delete_url}', WTBP_CE_DOMAIN ) . "\r\n";
        $message .= __('Spam it: {spam_url}', WTBP_CE_DOMAIN ) . "\r\n";
        $message .= __('Waiting for approval: {comment_count}. Please visit the moderation panel:') . "\r\n";
        $message .= '{moderate_url}' . "\r\n";

        return $message;
    }

    // default new trackback (moderator)
    public static function default_moderator_new_trackback_title() {

        $title = __('Please moderate new trackback', WTBP_CE_DOMAIN);

        return $title;
    }

    // default new trackback (moderator)
    public static function default_moderator_new_trackback_message() {

        $message  = __('A new trackback on the post "{post_title}" is waiting for your approval', WTBP_CE_DOMAIN) . "\r\n";
        $message .= '{post_url}' . "\r\n\r\n";
        $message .= __( 'Website: {website_name} (IP: {website_ip}, {website_hostname})', WTBP_CE_DOMAIN ) . "\r\n";
        $message .= __( 'URL: {website_url}', WTBP_CE_DOMAIN ) . "\r\n";
        $message .= __( "Trackback excerpt:\r\n{trackback_excerpt}", WTBP_CE_DOMAIN ) . "\r\n\r\n";

        $message .= __('Approve it: {approve_url}', WTBP_CE_DOMAIN ) . "\r\n";
        if ( EMPTY_TRASH_DAYS )
            $message .= __('Trash it: {trash_url}', WTBP_CE_DOMAIN ) . "\r\n";
        else
            $message .= __('Delete it: {delete_url}', WTBP_CE_DOMAIN ) . "\r\n";
        $message .= __('Spam it: {spam_url}', WTBP_CE_DOMAIN ) . "\r\n";
        $message .= __('Waiting for approval: {comment_count}. Please visit the moderation panel:') . "\r\n";
        $message .= '{moderate_url}' . "\r\n";

        return $message;
    }

    // default new pingback (moderator)
    public static function default_moderator_new_pingback_title() {

        $title = __('Please moderate new pingback', WTBP_CE_DOMAIN);

        return $title;
    }

    // default new pingback (moderator)
    public static function default_moderator_new_pingback_message() {

        $message  = __('A new pingback on the post "{post_title}" is waiting for your approval', WTBP_CE_DOMAIN) . "\r\n";
        $message .= '{post_url}' . "\r\n\r\n";
        $message .= __( 'Website: {website_name} (IP: {website_ip}, {website_hostname})', WTBP_CE_DOMAIN ) . "\r\n";
        $message .= __( 'URL: {website_url}', WTBP_CE_DOMAIN ) . "\r\n";
        $message .= __( "Pingback excerpt:\r\n{pingback_excerpt}", WTBP_CE_DOMAIN ) . "\r\n\r\n";

        $message .= __('Approve it: {approve_url}', WTBP_CE_DOMAIN ) . "\r\n";
        if ( EMPTY_TRASH_DAYS )
            $message .= __('Trash it: {trash_url}', WTBP_CE_DOMAIN ) . "\r\n";
        else
            $message .= __('Delete it: {delete_url}', WTBP_CE_DOMAIN ) . "\r\n";
        $message .= __('Spam it: {spam_url}', WTBP_CE_DOMAIN ) . "\r\n";
        $message .= __('Waiting for approval: {comment_count}. Please visit the moderation panel:') . "\r\n";
        $message .= '{moderate_url}' . "\r\n";

        return $message;
    }

    // default new comment (postauthor)
    public static function default_postauthor_new_comment_title() {

        $title = __('New comment', WTBP_CE_DOMAIN);

        return $title;
    }

    // default new comment (postauthor)
    public static function default_postauthor_new_comment_message() {

        $message  = __('New comment on your post "{post_title}"', WTBP_CE_DOMAIN) . "\r\n";
        $message .= '{post_url}' . "\r\n\r\n";
        $message .= __( 'Author: {author_name} (IP: {author_ip}, {author_domain})', WTBP_CE_DOMAIN ) . "\r\n";
        $message .= __( 'E-mail: {author_email}', WTBP_CE_DOMAIN ) . "\r\n";
        $message .= __( 'URL: {author_url}', WTBP_CE_DOMAIN ) . "\r\n";
        $message .= __( 'Whois: {author_whois}', WTBP_CE_DOMAIN ) . "\r\n";
        $message .= __( "Comment:\r\n{comment_content}", WTBP_CE_DOMAIN ) . "\r\n\r\n";
        $message .= __( 'You can see all comments on this post here:' ) . "\r\n";
        $message .= '{comments_url}' . "\r\n\r\n";
        $message .= __( 'Permalink: {permalink}', WTBP_CE_DOMAIN ) . "\r\n";

        if ( EMPTY_TRASH_DAYS )
            $message .= __('Trash it: {trash_url}', WTBP_CE_DOMAIN ) . "\r\n";
        else
            $message .= __('Delete it: {delete_url}', WTBP_CE_DOMAIN ) . "\r\n";
        $message .= __('Spam it: {spam_url}', WTBP_CE_DOMAIN ) . "\r\n";

        return $message;
    }

    // default new trackback (postauthor)
    public static function default_postauthor_new_trackback_title() {

        $title = __('New trackback', WTBP_CE_DOMAIN);

        return $title;
    }

    // default new trackback (postauthor)
    public static function default_postauthor_new_trackback_message() {

        $message  = __('New trackback on your post "{post_title}"', WTBP_CE_DOMAIN) . "\r\n";
        $message .= '{post_url}' . "\r\n\r\n";
        $message .= __( 'Website: {website_name} (IP: {website_ip}, {website_hostname})', WTBP_CE_DOMAIN ) . "\r\n";
        $message .= __( 'URL: {website_url}', WTBP_CE_DOMAIN ) . "\r\n";
        $message .= __( "Comment:\r\n{comment_content}", WTBP_CE_DOMAIN ) . "\r\n\r\n";
        $message .= __( 'You can see all trackbacks on this post here:' ) . "\r\n";
        $message .= '{trackbacks_url}' . "\r\n\r\n";
        $message .= __( 'Permalink: {permalink}', WTBP_CE_DOMAIN ) . "\r\n";

        if ( EMPTY_TRASH_DAYS )
            $message .= __('Trash it: {trash_url}', WTBP_CE_DOMAIN ) . "\r\n";
        else
            $message .= __('Delete it: {delete_url}', WTBP_CE_DOMAIN ) . "\r\n";
        $message .= __('Spam it: {spam_url}', WTBP_CE_DOMAIN ) . "\r\n";

        return $message;
    }

    // default new pingback (postauthor)
    public static function default_postauthor_new_pingback_title() {

        $title = __('New pingback', WTBP_CE_DOMAIN);

        return $title;
    }

    // default new pingback (postauthor)
    public static function default_postauthor_new_pingback_message() {

        $message  = __('New pingback on your post "{post_title}"', WTBP_CE_DOMAIN) . "\r\n";
        $message .= '{post_url}' . "\r\n\r\n";
        $message .= __( 'Website: {website_name} (IP: {website_ip}, {website_hostname})', WTBP_CE_DOMAIN ) . "\r\n";
        $message .= __( 'URL: {website_url}', WTBP_CE_DOMAIN ) . "\r\n";
        $message .= __( "Comment:\r\n{comment_content}", WTBP_CE_DOMAIN ) . "\r\n\r\n";
        $message .= __( 'You can see all pingbacks on this post here:' ) . "\r\n";
        $message .= '{pingbacks_url}' . "\r\n\r\n";
        $message .= __( 'Permalink: {permalink}', WTBP_CE_DOMAIN ) . "\r\n";

        if ( EMPTY_TRASH_DAYS )
            $message .= __('Trash it: {trash_url}', WTBP_CE_DOMAIN ) . "\r\n";
        else
            $message .= __('Delete it: {delete_url}', WTBP_CE_DOMAIN ) . "\r\n";
        $message .= __('Spam it: {spam_url}', WTBP_CE_DOMAIN ) . "\r\n";

        return $message;
    }

}
