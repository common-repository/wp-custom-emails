<?php

#-----------------------------------------------------------------#
# WORDPRESS CORE NOTIFICATION FUNCTIONS OVERRIDES
#-----------------------------------------------------------------#

global $wtbp_ce_settings;
$o = $wtbp_ce_settings;

/*
 * NEW USER
 */
if ( !function_exists( 'wp_new_user_notification' ) ) :

	/**
	 * Email login credentials to a newly-registered user.
	 *
	 * A new user registration notification is also sent to admin email.
	 *
	 * @since 0.5
	 *
	 * @param int    $user_id        User ID.
	 * @param string $plaintext_pass Optional. The user's plaintext password. Default empty.
	 */
	function wp_new_user_notification( $user_id, $plaintext_pass = '', $notify = '' ) {
		global $wp_version;
		global $wpdb;
		global $wp_hasher;
		global $wtbp_ce_settings;
		$o = $wtbp_ce_settings;

		$user = get_userdata( $user_id );

		$v43 = false;
		if ( $wp_version >= 4.3 ) {
			$v43 = true;
		}

		//
		// Admin notification
		//

        switch ( $o[ 'admin_new_user_mode' ] ) {
			case 'custom':
				$default_title	 = WP_Custom_Emails_Core_Defaults::default_admin_new_user_title();
				$default_message = WP_Custom_Emails_Core_Defaults::default_admin_new_user_message();

				$title	 = isset( $o[ 'admin_new_user_title' ] ) && !empty( $o[ 'admin_new_user_title' ] ) ? wp_kses_post( $o[ 'admin_new_user_title' ] ) : $default_title;
				$message = isset( $o[ 'admin_new_user_mes' ] ) && !empty( $o[ 'admin_new_user_mes' ] ) ? wp_kses_post( $o[ 'admin_new_user_mes' ] ) : $default_message;

				// Prepare the final text
				$message = str_replace( '{site_url}', network_home_url( '/' ), $message );
				$message = str_replace( '{user_login}', $user->user_login, $message );
				$message = str_replace( '{user_email}', $user->user_email, $message );

				break;

			case 'bypass':

				$title	 = WP_Custom_Emails_Core_Defaults::wp_admin_new_user_title();
				$message = WP_Custom_Emails_Core_Defaults::wp_admin_new_user_message( $user );

				break;

			default:
				$message = null;
		}

		// Output formatting
		$message = WTBP_WP_CE()->core->output_formatting( $message );

		if ( $message )
			@wp_mail( get_option( 'admin_email' ), $title, $message );

		//
		// User notification
		//
        
	if ( !$v43 && empty( $plaintext_pass ) )
			return;

		switch ( $o[ 'user_new_user_mode' ] ) {
			case 'custom':
				$default_title	 = WP_Custom_Emails_Core_Defaults::default_user_new_user_title();
				$default_message = WP_Custom_Emails_Core_Defaults::default_user_new_user_message();

				$title	 = isset( $o[ 'user_new_user_title' ] ) && !empty( $o[ 'user_new_user_title' ] ) ? esc_html( $o[ 'user_new_user_title' ] ) : $default_title;
				$message = isset( $o[ 'user_new_user_mes' ] ) && !empty( $o[ 'user_new_user_mes' ] ) ? wp_kses_post( $o[ 'user_new_user_mes' ] ) : $default_message;

				// Prepare the final text
				$message = str_replace( '{user_login}', $user->user_login, $message );
				$message = str_replace( '{user_password}', $plaintext_pass, $message );
				$message = str_replace( '{login_url}', wp_login_url(), $message );

				if ( $v43 ) {
					$key = wp_generate_password( 20, false );

					/** This action is documented in wp-login.php */
					do_action( 'retrieve_password_key', $user->user_login, $key );

					// Now insert the key, hashed, into the DB.
					if ( empty( $wp_hasher ) ) {
						require_once ABSPATH . WPINC . '/class-phpass.php';
						$wp_hasher = new PasswordHash( 8, true );
					}
					$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
					$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );

					$message = str_replace( '{password_url}', network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user->user_login ), 'login' ), $message );
				}

				break;

			case 'bypass':

				$title	 = WP_Custom_Emails_Core_Defaults::wp_user_new_user_title();
				$message = WP_Custom_Emails_Core_Defaults::wp_user_new_user_message( $user, $plaintext_pass );

				break;

			default:
				$message = null;
		}

		// Output formatting
		$message = WTBP_WP_CE()->core->output_formatting( $message );

		if ( $message )
			wp_mail( $user->user_email, $title, $message );
	}

endif;


/*
 * PASSWORD CHANGE
 */
if ( !function_exists( 'wp_password_change_notification' ) ) :

	/**
	 * Notify the blog admin of a user changing password, normally via email.
	 *
	 * @since 0.5
	 *
	 * @param object $user User Object
	 */
	function wp_password_change_notification( $user ) {
		global $wtbp_ce_settings;
		$o = $wtbp_ce_settings;

		// Admin notification
		if ( 'disabled' == $o[ 'admin_password_change_mode' ] )
			return;

		if ( 0 !== strcasecmp( $user->user_email, get_option( 'admin_email' ) ) ) {


			switch ( $o[ 'admin_password_change_mode' ] ) {
				case 'custom':
					
					$default_title	 = WP_Custom_Emails_Core_Defaults::default_admin_password_change_title();
					$default_message = WP_Custom_Emails_Core_Defaults::default_admin_password_change_message();

					$title	 = isset( $o[ 'admin_password_change_title' ] ) && !empty( $o[ 'admin_password_change_title' ] ) ? esc_html( $o[ 'admin_password_change_title' ] ) : $default_title;
					$message = isset( $o[ 'admin_password_change_mes' ] ) && !empty( $o[ 'admin_password_change_mes' ] ) ? wp_kses_post( $o[ 'admin_password_change_mes' ] ) : $default_message;

					// Prepare the final text
					$message = str_replace( '{user_login}', $user->user_login, $message );

					break;

				case 'bypass':

					$title	 = WP_Custom_Emails_Core_Defaults::default_admin_password_change_title();
					$message = WP_Custom_Emails_Core_Defaults::default_admin_password_change_message();

					break;

				default:
					$message = null;
			}

			// Output formatting
			$message = WTBP_WP_CE()->core->output_formatting( $message );

			wp_mail( get_option( 'admin_email' ), $title, $message );
		}
	}


endif;