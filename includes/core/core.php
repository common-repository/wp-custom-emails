<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

class WP_Custom_Emails_Core {

	/**
	 * @var path to the core class directory
	 */
	protected $dir = '';

	/*
	 * @var bool - check if text/html is content type
	 */
	public $is_html;

	/**
	 * Create class
	 */
	public function __construct() {
		global $wtbp_ce_settings;
		$o = $wtbp_ce_settings;

		$this->is_html = false;
		if ( isset( $o[ 'content_type' ] ) && 'html' === $o[ 'content_type' ] ) {
			$this->is_html = true;
		}

		$this->setup_globals( 'core' );
		$this->includes();
		$this->setup_hooks();
	}

	/**
	 * Globals
	 * @param string $slug 
	 */
	protected function setup_globals( $slug ) {

		$this->dir = WTBP_CE_DIR . "includes/$slug/"; // the instance root directory
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
	 */
	private function setup_hooks() {
		global $wtbp_ce_settings;
		$o = $wtbp_ce_settings;

		add_filter( 'wtbp_ce_message', array( $this, 'output_formatting' ), 10, 1 );
		add_filter( 'wtbp_ce_message_bypass', array( $this, 'output_formatting' ), 10, 1 );

		/** Sender  ********************************************************** */
		add_filter( 'wp_mail_from_name', array( $this, 'set_name' ) ); // Overrides the sender's name.
		add_filter( 'wp_mail_from', array( $this, 'set_email' ) ); // Overrides the sender's e-mail address.

		/** E-mail content type  ********************************************************** */
		if ( $this->is_html ) {

// Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
			remove_filter( 'wp_mail_content_type', 'set_html_content_type' );

			add_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ) );
		}

		/** Password Reset  ********************************************************** */
		if ( isset( $o[ 'user_ret_pass_mode' ] ) && 'bypass' != $o[ 'user_ret_pass_mode' ] ) {
			add_filter( 'retrieve_password_message', array( $this, 'retrieve_password_message' ), 10, 3 );
			add_filter( 'retrieve_password_title', array( $this, 'retrieve_password_title' ) );
		}

		/** New comment (moderator)  ********************************************************** */
		add_filter( 'comment_moderation_text', array( $this, 'moderator_new_comment_message' ), 10, 2 );
		add_filter( 'comment_moderation_subject', array( $this, 'moderator_new_comment_title' ), 10, 2 );

		/** New comment (postauthor)  ********************************************************** */
		add_filter( 'comment_notification_text', array( $this, 'postauthor_new_comment_message' ), 10, 2 );
		add_filter( 'comment_notification_subject', array( $this, 'postauthor_new_comment_title' ), 10, 2 );
	}

	/*
	 * Formating breaklines in HTML messages and shashes
	 */

	public function output_formatting( $message ) {
		
		$message = wp_unslash( $message );
		
		if ( $this->is_html && !empty( $message ) ) {
			$message = wpautop( $message );
		}

		return $message;
	}

	/*
	 * Overrides the sender's name.
	 */

	public function set_name( $from_name ) {
		global $wtbp_ce_settings;
		$o = $wtbp_ce_settings;

		if ( isset( $o[ 'sender_name' ] ) && !empty( $o[ 'sender_name' ] ) ) {

// New sender's name
			$from_name = $o[ 'sender_name' ];
		}

		return $from_name;
	}

	/*
	 * Overrides the sender's e-mail address.
	 */

	function set_email( $from_email ) {
		global $wtbp_ce_settings;
		$o = $wtbp_ce_settings;

		if ( isset( $o[ 'sender_email' ] ) && !empty( $o[ 'sender_email' ] ) && is_email( $o[ 'sender_email' ] ) ) {

// New e-mail address 
			$from_email = $o[ 'sender_email' ];
		}

		return $from_email;
	}

	/*
	 * Set HTML content type
	 */

	public function set_html_content_type() {
		return 'text/html';
	}

	/*
	 * Set custom retrieve password title
	 */

	function retrieve_password_title( $title ) {
		global $wtbp_ce_settings;
		$o = $wtbp_ce_settings;

		$default = WP_Custom_Emails_Core_Defaults::default_user_ret_pass_title();

		$new_title = isset( $o[ 'user_ret_pass_title' ] ) && !empty( $o[ 'user_ret_pass_title' ] ) ? esc_html( $o[ 'user_ret_pass_title' ] ) : $default;

		return $new_title;
	}

	/*
	 * Set custom retrieve password message
	 */

	function retrieve_password_message( $message, $key, $user_login ) {
		global $wtbp_ce_settings;
		$o = $wtbp_ce_settings;

		if ( 'disabled' == $o[ 'user_ret_pass_mode' ] ) {
			return null;
		}

// Defaults
		$default = WP_Custom_Emails_Core_Defaults::default_user_ret_pass_message();

		$message = isset( $o[ 'user_ret_pass_mes' ] ) && !empty( $o[ 'user_ret_pass_mes' ] ) ? wp_kses_post( $o[ 'user_ret_pass_mes' ] ) : $default;

// Prepare the final text
		$message = str_replace( '{site_url}', network_home_url( '/' ), $message );
		$message = str_replace( '{user_login}', $user_login, $message );
		$message = str_replace( '{reset_link}', network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ), $message );

		return apply_filters( 'wtbp_ce_message', $message );
	}

	/*
	 * Set custom new comment (pingback, trackback) message for moderator
	 */

	function moderator_new_comment_message( $notify_message, $comment_id ) {
		global $wpdb;
		global $wtbp_ce_settings;
		$o = $wtbp_ce_settings;

		$comment = get_comment( $comment_id );
		$post	 = get_post( $comment->comment_post_ID );
		$user	 = get_userdata( $post->post_author );

		$comment_author_domain	 = @gethostbyaddr( $comment->comment_author_IP );
		$comments_waiting		 = $wpdb->get_var( "SELECT count(comment_ID) FROM $wpdb->comments WHERE comment_approved = '0'" );

		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

		switch ( $comment->comment_type ) {
			case 'trackback':

				switch ( $o[ 'moderator_new_trackback_mode' ] ) {
					case 'custom':
						$default_message = WP_Custom_Emails_Core_Defaults::default_moderator_new_trackback_message();
						$message		 = isset( $o[ 'moderator_new_trackback_mes' ] ) && !empty( $o[ 'moderator_new_trackback_mes' ] ) ? wp_kses_post( $o[ 'moderator_new_trackback_mes' ] ) : $default_message;

// Prepare the final text
						$message = str_replace( '{post_title}', $post->post_title, $message );
						$message = str_replace( '{post_url}', get_permalink( $comment->comment_post_ID ), $message );
						$message = str_replace( '{website_name}', $comment->comment_author, $message );
						$message = str_replace( '{website_ip}', $comment->comment_author_IP, $message );
						$message = str_replace( '{website_hostname}', $comment_author_domain, $message );
						$message = str_replace( '{website_url}', $comment->comment_author_url, $message );
						$message = str_replace( '{trackback_excerpt}', $comment->comment_content, $message );

						break;

					case 'bypass':
						return apply_filters( 'wtbp_ce_message_bypass', $notify_message );

						break;

					default:
						$message = null;
				}

				break;

			case 'pingback':

				switch ( $o[ 'moderator_new_pingback_mode' ] ) {
					case 'custom':
						$default_message = WP_Custom_Emails_Core_Defaults::default_moderator_new_pingback_message();
						$message		 = isset( $o[ 'moderator_new_pingback_mes' ] ) && !empty( $o[ 'moderator_new_pingback_mes' ] ) ? wp_kses_post( $o[ 'moderator_new_pingback_mes' ] ) : $default_message;

// Prepare the final text
						$message = str_replace( '{post_title}', $post->post_title, $message );
						$message = str_replace( '{post_url}', get_permalink( $comment->comment_post_ID ), $message );
						$message = str_replace( '{website_name}', $comment->comment_author, $message );
						$message = str_replace( '{website_ip}', $comment->comment_author_IP, $message );
						$message = str_replace( '{website_hostname}', $comment_author_domain, $message );
						$message = str_replace( '{website_url}', $comment->comment_author_url, $message );
						$message = str_replace( '{pingback_excerpt}', $comment->comment_content, $message );

						break;

					case 'bypass':
						return apply_filters( 'wtbp_ce_message_bypass', $notify_message );

						break;

					default:
						$message = null;
				}

				break;

			default: // Comments

				switch ( $o[ 'moderator_new_comment_mode' ] ) {
					case 'custom':
						$default_message = WP_Custom_Emails_Core_Defaults::default_moderator_new_comment_message();

						$message = isset( $o[ 'moderator_new_comment_mes' ] ) && !empty( $o[ 'moderator_new_comment_mes' ] ) ? wp_kses_post( $o[ 'moderator_new_comment_mes' ] ) : $default_message;

// Prepare the final text
						$message = str_replace( '{post_title}', $post->post_title, $message );
						$message = str_replace( '{post_url}', get_permalink( $comment->comment_post_ID ), $message );
						$message = str_replace( '{author_name}', $comment->comment_author, $message );
						$message = str_replace( '{author_ip}', $comment->comment_author_IP, $message );
						$message = str_replace( '{author_domain}', $comment_author_domain, $message );
						$message = str_replace( '{author_email}', $comment->comment_author_email, $message );
						$message = str_replace( '{author_url}', $comment->comment_author_url, $message );
						$message = str_replace( '{author_whois}', "http://whois.arin.net/rest/ip/{$comment->comment_author_IP}", $message );
						$message = str_replace( '{comment_content}', $comment->comment_content, $message );

						break;

					case 'bypass':
						return apply_filters( 'wtbp_ce_message_bypass', $notify_message );

						break;

					default:
						$message = null;
				}

				break;
		}

		if ( $message ) {
			$message = str_replace( '{approve_url}', admin_url( "comment.php?action=approve&c=$comment_id" ), $message );
			$message = str_replace( '{trash_url}', admin_url( "comment.php?action=trash&c=$comment_id" ), $message );
			$message = str_replace( '{delete_url}', admin_url( "comment.php?action=delete&c=$comment_id" ), $message );
			$message = str_replace( '{spam_url}', admin_url( "comment.php?action=spam&c=$comment_id" ), $message );
			$message = str_replace( '{comment_count}', $comments_waiting, $message );
			$message = str_replace( '{moderate_url}', admin_url( "edit-comments.php?comment_status=moderated" ), $message );
		}

		$message = wp_specialchars_decode( $message, ENT_QUOTES );
		return apply_filters( 'wtbp_ce_message', $message );
	}

	/*
	 * Set custom new comment (pingback, trackback) title for moderator
	 */

	function moderator_new_comment_title( $subject, $comment_id ) {
		global $wpdb;
		global $wtbp_ce_settings;
		$o = $wtbp_ce_settings;

		$comment = get_comment( $comment_id );

		switch ( $comment->comment_type ) {
			case 'trackback':

				switch ( $o[ 'moderator_new_trackback_mode' ] ) {
					case 'custom':
						$default_title	 = WP_Custom_Emails_Core_Defaults::default_moderator_new_trackback_title();
						$title			 = isset( $o[ 'moderator_new_trackback_title' ] ) && !empty( $o[ 'moderator_new_trackback_title' ] ) ? esc_html( $o[ 'moderator_new_trackback_title' ] ) : $default_title;

						break;

					case 'bypass':
						return $subject;

						break;

					default:
						$title = null;
				}

				break;

			case 'pingback':

				switch ( $o[ 'moderator_new_pingback_mode' ] ) {
					case 'custom':
						$default_title	 = WP_Custom_Emails_Core_Defaults::default_moderator_new_pingback_title();
						$title			 = isset( $o[ 'moderator_new_pingback_title' ] ) && !empty( $o[ 'moderator_new_pingback_title' ] ) ? esc_html( $o[ 'moderator_new_pingback_title' ] ) : $default_title;

						break;

					case 'bypass':
						return $subject;

						break;

					default:
						$title = null;
				}

				break;

			default: // Comments

				switch ( $o[ 'moderator_new_comment_mode' ] ) {
					case 'custom':
						$default_title	 = WP_Custom_Emails_Core_Defaults::default_moderator_new_comment_title();
						$title			 = isset( $o[ 'moderator_new_comment_title' ] ) && !empty( $o[ 'moderator_new_comment_title' ] ) ? esc_html( $o[ 'moderator_new_comment_title' ] ) : $default_title;

						break;

					case 'bypass':
						return $subject;

						break;

					default:
						$title = null;
				}

				break;
		}

		return $title;
	}

	/*
	 * Set custom new comment (pingback, trackback) message for postauthor
	 */

	function postauthor_new_comment_message( $notify_message, $comment_id ) {
		global $wtbp_ce_settings;
		$o = $wtbp_ce_settings;

		$comment = get_comment( $comment_id );
		$post	 = get_post( $comment->comment_post_ID );

		$comment_author_domain = @gethostbyaddr( $comment->comment_author_IP );

		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

		switch ( $comment->comment_type ) {
			case 'trackback':

				switch ( $o[ 'postauthor_new_trackback_mode' ] ) {
					case 'custom':
						$default_message = WP_Custom_Emails_Core_Defaults::default_postauthor_new_trackback_message();
						$message		 = isset( $o[ 'postauthor_new_trackback_mes' ] ) && !empty( $o[ 'postauthor_new_trackback_mes' ] ) ? wp_kses_post( $o[ 'postauthor_new_trackback_mes' ] ) : $default_message;

// Prepare the final text
						$message = str_replace( '{post_title}', $post->post_title, $message );
						$message = str_replace( '{post_url}', get_permalink( $comment->comment_post_ID ), $message );
						$message = str_replace( '{website_name}', $comment->comment_author, $message );
						$message = str_replace( '{website_ip}', $comment->comment_author_IP, $message );
						$message = str_replace( '{website_hostname}', $comment_author_domain, $message );
						$message = str_replace( '{website_url}', $comment->comment_author_url, $message );
						$message = str_replace( '{comment_content}', $comment->comment_content, $message );
						$message = str_replace( '{trackbacks_url}', get_permalink( $comment->comment_post_ID ) . "#comments", $message );

						break;

					case 'bypass':
						return apply_filters( 'wtbp_ce_message_bypass', $notify_message );

						break;

					default:
						$message = null;
				}

				break;

			case 'pingback':

				switch ( $o[ 'postauthor_new_pingback_mode' ] ) {
					case 'custom':
						$default_message = WP_Custom_Emails_Core_Defaults::default_postauthor_new_pingback_message();
						$message		 = isset( $o[ 'postauthor_new_pingback_mes' ] ) && !empty( $o[ 'postauthor_new_pingback_mes' ] ) ? wp_kses_post( $o[ 'postauthor_new_pingback_mes' ] ) : $default_message;

// Prepare the final text
						$message = str_replace( '{post_title}', $post->post_title, $message );
						$message = str_replace( '{post_url}', get_permalink( $comment->comment_post_ID ), $message );
						$message = str_replace( '{website_name}', $comment->comment_author, $message );
						$message = str_replace( '{website_ip}', $comment->comment_author_IP, $message );
						$message = str_replace( '{website_hostname}', $comment_author_domain, $message );
						$message = str_replace( '{website_url}', $comment->comment_author_url, $message );
						$message = str_replace( '{comment_content}', $comment->comment_content, $message );
						$message = str_replace( '{pingbacks_url}', get_permalink( $comment->comment_post_ID ) . "#comments", $message );

						break;

					case 'bypass':
						return apply_filters( 'wtbp_ce_message_bypass', $notify_message );

						break;

					default:
						$message = null;
				}

				break;

			default: // Comments

				switch ( $o[ 'postauthor_new_comment_mode' ] ) {
					case 'custom':
						$default_message = WP_Custom_Emails_Core_Defaults::default_postauthor_new_comment_message();
						$message		 = isset( $o[ 'postauthor_new_comment_mes' ] ) && !empty( $o[ 'postauthor_new_comment_mes' ] ) ? wp_kses_post( $o[ 'postauthor_new_comment_mes' ] ) : $default_message;

// Prepare the final text
						$message = str_replace( '{post_title}', $post->post_title, $message );
						$message = str_replace( '{post_url}', get_permalink( $comment->comment_post_ID ), $message );
						$message = str_replace( '{author_name}', $comment->comment_author, $message );
						$message = str_replace( '{author_ip}', $comment->comment_author_IP, $message );
						$message = str_replace( '{author_domain}', $comment_author_domain, $message );
						$message = str_replace( '{author_email}', $comment->comment_author_email, $message );
						$message = str_replace( '{author_url}', $comment->comment_author_url, $message );
						$message = str_replace( '{author_whois}', "http://whois.arin.net/rest/ip/{$comment->comment_author_IP}", $message );
						$message = str_replace( '{comment_content}', $comment->comment_content, $message );
						$message = str_replace( '{comments_url}', get_permalink( $comment->comment_post_ID ) . "#comments", $message );

						break;

					case 'bypass':
						return apply_filters( 'wtbp_ce_message_bypass', $notify_message );

						break;

					default:
						$message = null;
				}

				break;
		}

		if ( $message ) {
			$message = str_replace( '{permalink}', get_comment_link( $comment_id ), $message );
			$message = str_replace( '{trash_url}', admin_url( "comment.php?action=trash&c=$comment_id" ), $message );
			$message = str_replace( '{delete_url}', admin_url( "comment.php?action=delete&c=$comment_id" ), $message );
			$message = str_replace( '{spam_url}', admin_url( "comment.php?action=spam&c=$comment_id" ), $message );
		}

		$message = wp_specialchars_decode( $message, ENT_QUOTES );
		return apply_filters( 'wtbp_ce_message', $message );
	}

	/*
	 * Set custom new comment (pingback, trackback) title for postauthor
	 */

	function postauthor_new_comment_title( $subject, $comment_id ) {
		global $wtbp_ce_settings;
		$o = $wtbp_ce_settings;

		$comment = get_comment( $comment_id );

		switch ( $comment->comment_type ) {
			case 'trackback':

				switch ( $o[ 'postauthor_new_trackback_mode' ] ) {
					case 'custom':
						$default_title	 = WP_Custom_Emails_Core_Defaults::default_postauthor_new_trackback_title();
						$title			 = isset( $o[ 'postauthor_new_trackback_title' ] ) && !empty( $o[ 'postauthor_new_trackback_title' ] ) ? esc_html( $o[ 'postauthor_new_trackback_title' ] ) : $default_title;

						break;

					case 'bypass':
						return $subject;

						break;

					default:
						$title = null;
				}

				break;

			case 'pingback':

				switch ( $o[ 'postauthor_new_pingback_mode' ] ) {
					case 'custom':
						$default_title	 = WP_Custom_Emails_Core_Defaults::default_postauthor_new_pingback_title();
						$title			 = isset( $o[ 'postauthor_new_pingback_title' ] ) && !empty( $o[ 'postauthor_new_pingback_title' ] ) ? esc_html( $o[ 'postauthor_new_pingback_title' ] ) : $default_title;

						break;

					case 'bypass':
						return $subject;

						break;

					default:
						$title = null;
				}

				break;

			default: // Comments

				switch ( $o[ 'postauthor_new_comment_mode' ] ) {
					case 'custom':
						$default_title	 = WP_Custom_Emails_Core_Defaults::default_postauthor_new_comment_title();
						$title			 = isset( $o[ 'postauthor_new_comment_title' ] ) && !empty( $o[ 'postauthor_new_comment_title' ] ) ? esc_html( $o[ 'postauthor_new_comment_title' ] ) : $default_title;

						break;

					case 'bypass':
						return $subject;

						break;

					default:
						$title = null;
				}

				break;
		}

		return $title;
	}

}
