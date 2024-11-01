<?php

/**
 * Multisite notifications module
 *
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

class WP_Custom_Emails_WPMS extends WP_Custom_Emails_Core {

	public function __construct() {

		$this->setup_globals( 'wpms' );

		$this->includes();
		$this->setup_hooks();
	}

	/**
	 * Include required files
	 */
	private function includes() {

		// add default e-mails titles and messages
		require_once $this->dir . 'defaults.php';

		// add notifications settings
		require_once $this->dir . 'settings.php';

		// overrides the Wordpress Core functions, where there is no hooks.
		require_once $this->dir . 'overrides.php';
	}

	/**
	 * Setup the core hooks, actions and filters
	 *
	 */
	private function setup_hooks() {
		global $wtbp_ce_settings;
		$o = $wtbp_ce_settings;
		
	
		/** Admin changes email  ********************************************************** */
		if ( isset( $o[ 'ms_admin_changes_email_mode' ] ) && 'bypass' != $o[ 'ms_admin_changes_email_mode' ] ) {

			require_once(ABSPATH . 'wp-admin/includes/ms.php');

			remove_action( 'update_option_new_admin_email', 'update_option_new_admin_email', 10 );
			remove_action( 'add_option_new_admin_email', 'update_option_new_admin_email', 10 );
			add_action( 'update_option_new_admin_email', array( $this, 'update_option_new_admin_email' ), 10, 2 );
			add_action( 'add_option_new_admin_email', array( $this, 'update_option_new_admin_email' ), 10, 2 );
		}


		/** User changes emails  ********************************************************** */
		if ( isset($o[ 'ms_user_changes_emails_mode' ]) && 'bypass' != $o[ 'ms_user_changes_emails_mode' ] ) {

			require_once(ABSPATH . 'wp-admin/includes/ms.php');

			remove_action( 'personal_options_update', 'send_confirmation_on_profile_email' );
			add_action( 'personal_options_update', array( $this, 'send_confirmation_on_profile_email' ) );
		}
	}

	// sends an email when a site administrator email address is changed.
	function update_option_new_admin_email( $old_value, $value ) {
		global $wtbp_ce_settings;
		$o = $wtbp_ce_settings;

		$current_user = wp_get_current_user();

		if ( 'disabled' == $o[ 'ms_admin_changes_email_mode' ] ) {
			return null;
		}

		if ( $value == get_option( 'admin_email' ) || !is_email( $value ) )
			return;

		$hash			 = md5( $value . time() . mt_rand() );
		$new_admin_email = array(
			'hash'		 => $hash,
			'newemail'	 => $value
		);
		update_option( 'adminhash', $new_admin_email );

		// Default title
		$default_title = WP_Custom_Emails_WPMS_Defaults::default_ms_admin_changes_email_title();

		$title = isset( $o[ 'ms_admin_changes_email_title' ] ) && !empty( $o[ 'ms_admin_changes_email_title' ] ) ? esc_html( $o[ 'ms_admin_changes_email_title' ] ) : $default_title;

		// Prepare the title
		$title = str_replace( '{blog_name}', wp_specialchars_decode( get_option( 'blogname' ) ), $title );


		// Default message
		$default_message = WP_Custom_Emails_Core_Defaults::default_ms_admin_changes_email_message();

		$message = isset( $o[ 'ms_admin_changes_email_mes' ] ) && !empty( $o[ 'ms_admin_changes_email_mes' ] ) ? wp_kses_post( $o[ 'ms_admin_changes_email_mes' ] ) : $default_message;

		$message = apply_filters( 'new_admin_email_content', $message, $new_admin_email );

		// Prepare the final text
		$message = str_replace( '{user_name}', $current_user->user_login, $message );
		$message = str_replace( '{admin_url}', esc_url( admin_url( 'options.php?adminhash=' . $hash ) ), $message );
		$message = str_replace( '{admin_email}', $value, $message );
		$message = str_replace( '{site_name}', get_site_option( 'site_name' ), $message );
		$message = str_replace( '{site_url}', network_home_url(), $message );


		wp_mail( $value, $title, $message );
	}

	// sends an email when an email address change is requested.
	function send_confirmation_on_profile_email() {
		global $errors, $wpdb, $wtbp_ce_settings;
		;
		$o = $wtbp_ce_settings;

		if ( 'disabled' == $o[ 'ms_user_changes_email_mode' ] ) {
			return null;
		}

		$current_user	 = wp_get_current_user();
		if ( !is_object( $errors ) )
			$errors			 = new WP_Error();

		if ( $current_user->ID != $_POST[ 'user_id' ] )
			return false;

		if ( $current_user->user_email != $_POST[ 'email' ] ) {
			if ( !is_email( $_POST[ 'email' ] ) ) {
				$errors->add( 'user_email', __( "<strong>ERROR</strong>: The email address isn&#8217;t correct." ), array( 'form-field' => 'email' ) );
				return;
			}

			if ( $wpdb->get_var( $wpdb->prepare( "SELECT user_email FROM {$wpdb->users} WHERE user_email=%s", $_POST[ 'email' ] ) ) ) {
				$errors->add( 'user_email', __( "<strong>ERROR</strong>: The email address is already used." ), array( 'form-field' => 'email' ) );
				delete_option( $current_user->ID . '_new_email' );
				return;
			}

			$hash			 = md5( $_POST[ 'email' ] . time() . mt_rand() );
			$new_user_email	 = array(
				'hash'		 => $hash,
				'newemail'	 => $_POST[ 'email' ]
			);
			update_option( $current_user->ID . '_new_email', $new_user_email );


			// Default title
			$default_title = WP_Custom_Emails_WPMS_Defaults::default_ms_user_changes_email_title();

			$title = isset( $o[ 'ms_user_changes_email_title' ] ) && !empty( $o[ 'ms_user_changes_email_title' ] ) ? esc_html( $o[ 'ms_admin_changes_email_title' ] ) : $default_title;

			// Prepare the title
			$title = str_replace( '{blog_name}', wp_specialchars_decode( get_option( 'blogname' ) ), $title );


			// Default message
			$default_message = WP_Custom_Emails_Core_Defaults::default_ms_admin_changes_email_message();

			$message = isset( $o[ 'ms_user_changes_email_mes' ] ) && !empty( $o[ 'ms_user_changes_email_mes' ] ) ? esc_html( $o[ 'ms_user_changes_email_mes' ] ) : $default_message;

			$message = apply_filters( 'new_admin_email_content', $message, $new_admin_email );

			// Prepare the final text
			$message = str_replace( '{user_name}', $current_user->user_login, $message );
			$message = str_replace( '{admin_url}', esc_url( admin_url( 'profile.php?newuseremail=' . $hash ) ), $message );
			$message = str_replace( '{new_email}', $_POST[ 'email' ], $message );
			$message = str_replace( '{site_name}', get_site_option( 'site_name' ), $message );
			$message = str_replace( '{site_url}', network_home_url(), $message );

			$message = apply_filters( 'new_user_email_content', $message, $new_user_email );


			wp_mail( $_POST[ 'email' ], $title, $message );
			$_POST[ 'email' ] = $current_user->user_email;
		}
	}

}
