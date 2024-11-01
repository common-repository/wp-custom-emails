<?php

/*
  Plugin Name: WP Custom Emails
  Description: Easily customize WordPress notification emails.
  Version: 1.2.2
  Author: Michał Jaworski, Damian Góra
  Author URI: https://wordpress.org/plugins/wp-custom-emails/
  License:
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Let's start debug.
//ini_set('display_errors',1);
//ini_set('display_startup_errors',1);
//error_reporting(-1);

if ( !class_exists( 'WP_Custom_Emails' ) ) {



	/**
	 * The main class
	 */
	class WP_Custom_Emails {

		private static $instance;

		/**
		 * WP_Custom_Emails_Core Object
		 *
		 * @var object
		 */
		public $core;

		// Instance
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance			 = new self();
				self::$instance->constants();
				self::$instance->includes();
				self::$instance->load_textdomain();
				self::$instance->core	 = new WP_Custom_Emails_Core();
				
				if ( is_multisite() ) {
					self::$instance->multisite = new WP_Custom_Emails_WPMS();
				}
			}
			return self::$instance;
		}

		// Constants
		private function constants() {

			// Current version
			define( 'WTBP_CE_VERSION', '1.2.2' );

			// Plugin name
			define( 'WTBP_CE_NAME', 'WP Custom Emails' );

			// Root plugin path
			define( 'WTBP_CE_DIR', plugin_dir_path( __FILE__ ) );

			// Root plugin URL
			define( 'WTBP_CE_URL', plugin_dir_url( __FILE__ ) );

			// General plugin FILE
			define( 'WTBP_CE_FILE', __FILE__ );

			// Text Domain
			define( 'WTBP_CE_DOMAIN', 'wp-custom-emails' );
		}

		// Include the necessary files
		private function includes() {
			global $wtbp_ce_settings;

			require_once WTBP_CE_DIR . 'includes/settings/settings.php'; // Settings functions
			$wtbp_ce_settings = get_option( 'wtbp_ce_settings' );
			if ( empty( $wtbp_ce_settings ) ) {
				$wtbp_ce_settings = array();
			}

			require_once WTBP_CE_DIR . 'includes/settings/settings-view.php'; // Settings view

			require_once(WTBP_CE_DIR . 'includes/install.php'); // Install class
			require_once WTBP_CE_DIR . 'includes/admin/admin.php'; // main admin class

			require_once(WTBP_CE_DIR . 'includes/core/core.php'); // Core class
			// Multisite notifications extension
			if ( is_multisite() ) {
				require_once WTBP_CE_DIR . 'includes/wpms/wpms.php'; // Multisite module init
			}
		}

		// Register text domain
		private function load_textdomain() {
			$lang_dir = dirname( plugin_basename( WTBP_CE_FILE ) ) . '/languages/';
			load_plugin_textdomain( WTBP_CE_DOMAIN, false, $lang_dir );
		}

	}

	function WTBP_WP_CE() {
		return WP_Custom_Emails::instance();
	}

	// Init everything
	WTBP_WP_CE();
}
?>