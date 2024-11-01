<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

class WP_Custom_Emails_WPMS_Defaults extends WP_Custom_Emails_Core_Defaults {
    
    
    // admin changes email title ( admin )
    public static function default_ms_admin_changes_email_title() {
        
        $title = '{blog_name} ' . __( 'New Admin Email Address', WTBP_CE_DOMAIN);
        
        return $title;
        
    }
    
    // admin changes email message ( admin )
    public static function default_ms_admin_changes_email_message() {
        
        $message = __( 'Howdy {user_name},', WTBP_CE_DOMAIN) . "\r\n\r\n";
        $message .= __( 'You recently requested to have the administration email address on your site changed.', WTBP_CE_DOMAIN) . "\r\n\r\n";
        $message .= __( 'If this is correct, please click on the following link to change it:', WTBP_CE_DOMAIN) . "\r\n";
        $message .= '{admin_url}' . "\r\n\r\n";
        $message .= __( 'You can safely ignore and delete this email if you do not want to take this action.', WTBP_CE_DOMAIN) . "\r\n\r\n";
        $message .= __( 'This email has been sent to', WTBP_CE_DOMAIN) . ' {new_email}' . "\r\n\r\n";
        $message .= __( 'Regards,', WTBP_CE_DOMAIN) . "\r\n";
        $message .= __( 'All at,', WTBP_CE_DOMAIN) . ' {site_name}' . "\r\n";
        $message .= '{site_url}' . "\r\n";;

        return $message;
    }
    
    
    // change user email title ( user )
    public static function default_ms_user_changes_email_title() {
        
        $title = '{blog_name} ' . __( 'New Email Address', WTBP_CE_DOMAIN);
        
        return $title;
        
    }
    
    
    
    
     
}


new WP_Custom_Emails_WPMS_Defaults();
