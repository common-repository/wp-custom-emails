<?php

/*
 * Rejestracja ustawień z wykorzystaniem WordPress Settings API
 */


// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( !defined( 'ABSPATH' ) )
	exit;

/**
 * Dodaje sekcje i pola dla ustawień
 */
function wtbp_ce_register_settings() {

	if ( false == get_option( 'wtbp_ce_settings' ) ) {
		add_option( 'wtbp_ce_settings' );
	}

	foreach ( wtbp_ce_get_registered_settings() as $section => $settings ) {

		add_settings_section(
		'wtbp_ce_settings_' . $section, __return_null(), '__return_false', 'wtbp_ce_settings_' . $section
		);

		foreach ( $settings as $option ) {

			$name = isset( $option[ 'name' ] ) ? $option[ 'name' ] : '';

			add_settings_field(
			'wtbp_ce_settings[' . $option[ 'id' ] . ']', $name, function_exists( 'wtbp_ce_' . $option[ 'type' ] . '_callback' ) ? 'wtbp_ce_' . $option[ 'type' ] . '_callback' : 'wtbp_ce_missing_callback', 'wtbp_ce_settings_' . $section, 'wtbp_ce_settings_' . $section, array(
				'id'		 => isset( $option[ 'id' ] ) ? $option[ 'id' ] : null,
				'desc'		 => !empty( $option[ 'desc' ] ) ? $option[ 'desc' ] : '',
				'name'		 => isset( $option[ 'name' ] ) ? $option[ 'name' ] : null,
				'section'	 => $section,
				'size'		 => isset( $option[ 'size' ] ) ? $option[ 'size' ] : null,
				'class'		 => isset( $option[ 'class' ] ) ? $option[ 'class' ] : null, // CSS class
				'options'	 => isset( $option[ 'options' ] ) ? $option[ 'options' ] : '',
				'std'		 => isset( $option[ 'std' ] ) ? $option[ 'std' ] : ''
			)
			);
		}
	}

	// Creates our settings in the options table
	register_setting( 'wtbp_ce_settings', 'wtbp_ce_settings', 'wtbp_ce_settings_validation' );
}

add_action( 'admin_init', 'wtbp_ce_register_settings' );


/*
 * Domyślne opcje wtyczki.
 * Filtry umożliwiają dodanie nowych ustawień inną wtyczką.
 */

function wtbp_ce_get_registered_settings() {

	$wtbp_ce_settings = array(
		//  General TAB
		'general' => apply_filters( 'wtbp_ce_settings_general', array(
			'sender_head'	 => array(
				'id'	 => 'sender_head',
				'name'	 => '<h3>' . __( 'Custom sender', WTBP_CE_DOMAIN ) . '</h3>',
				'type'	 => 'header',
			),
			'sender_name'	 => array(
				'id'	 => 'sender_name',
				'name'	 => '<strong>' . __( 'Sender name', WTBP_CE_DOMAIN ) . '</strong>',
				'type'	 => 'text',
			),
			'sender_email'	 => array(
				'id'	 => 'sender_email',
				'name'	 => '<strong>' . __( 'Sender e-mail address', WTBP_CE_DOMAIN ) . '</strong>',
				'type'	 => 'text',
			),
			'other'			 => array(
				'id'	 => 'other',
				'name'	 => '<h3>' . __( 'Other', WTBP_CE_DOMAIN ) . '</h3>',
				'type'	 => 'header',
			),
			'content_type'	 => array(
				'id'		 => 'content_type',
				'name'		 => '<strong>' . __( 'E-mail content type', WTBP_CE_DOMAIN ) . '</strong>',
				'type'		 => 'select',
				'options'	 => array(
					'plain'	 => __( 'Plain text', WTBP_CE_DOMAIN ),
					'html'	 => __( 'HTML', WTBP_CE_DOMAIN )
				),
				'std'		 => 'plain'
			)
		)
		),
	);


	return apply_filters( 'wtbp_ce_settings', $wtbp_ce_settings );
}

/*
 * Walidacja pól formularza z ustawieniami
 */

function wtbp_ce_settings_validation( $input = array() ) {
	global $wtbp_ce_settings;

	if ( empty( $_POST[ '_wp_http_referer' ] ) ) {
		return $input;
	}

	// Get the referer
	parse_str( $_POST[ '_wp_http_referer' ], $referrer );

	// Current tab
	$tab = isset( $referrer[ 'tab' ] ) ? $referrer[ 'tab' ] : 'general';

	// All settings
	$settings = wtbp_ce_get_registered_settings();

	// If there is no change, set the empty array
	$input = $input ? $input : array();


	// Pętla po wszytskich opcjach wtyczki. 
	if ( !empty( $settings[ $tab ] ) ) {
		foreach ( $settings[ $tab ] as $key => $value ) {


			if ( empty( $input[ $key ] ) ) {
				if ( isset( $wtbp_ce_settings[ $key ] ) ) {
					unset( $wtbp_ce_settings[ $key ] );
				}
			} else {

				// The rules
				switch ( $value[ 'type' ] ) {
					case 'text':
						$input[ $key ]	 = trim( sanitize_text_field( $input[ $key ] ) );
						break;
					case 'textarea':
						$input[ $key ]	 = wp_slash( wp_kses_post( $input[ $key ] ) );
						//$input[ $key ]	 = trim( implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $input[ $key ] ) ) ) );
						break;
					case 'email_editor':
						$input[ $key ]	 = wp_slash( wp_kses_post( $input[ $key ] ) );
						break;
					case 'select':
						$input[ $key ]	 = sanitize_text_field( $input[ $key ] );
						break;
					default:
						$input[ $key ]	 = '';
				}
			}
		}
	}

	// settings after validation
	do_action( 'wtbp_ce_settings_after_validation', $input );

	// merge the options
	$output = array_merge( $wtbp_ce_settings, $input );


	return $output;
}

/**
 * Create the tabs
 */
function wtbp_ce_get_settings_tabs() {

	$settings = wtbp_ce_get_registered_settings();

	$tabs				 = array();
	$tabs[ 'general' ]	 = __( 'General', WTBP_CE_DOMAIN );


	return apply_filters( 'wtbp_ce_settings_tabs', $tabs );
}

/**
 * Input type text - Callback
 */
function wtbp_ce_text_callback( $args ) {
	global $wtbp_ce_settings;

	if ( isset( $wtbp_ce_settings[ $args[ 'id' ] ] ) )
		$value	 = $wtbp_ce_settings[ $args[ 'id' ] ];
	else
		$value	 = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';

	// Restore default title
	$default_text = '';
	if (
	isset( $args[ 'class' ] ) && $args[ 'class' ] === 'wtbp-ce-hidden-field' &&
	isset( $args[ 'std' ] ) && !empty( $args[ 'std' ] )
	) {
		$default_text = '<input type="hidden" class="wtbp-ce-default-title" value="' . $args[ 'std' ] . '" />';
	}


	$size	 = ( isset( $args[ 'size' ] ) && !is_null( $args[ 'size' ] ) ) ? $args[ 'size' ] : 'regular';
	$html	 = '<input type="text" class="' . $size . '-text" id="wtbp_ce_settings[' . $args[ 'id' ] . ']" name="wtbp_ce_settings[' . $args[ 'id' ] . ']" value="' . esc_attr( wp_unslash( $value ) ) . '"/>';
	$html .= $default_text;
	$html .= '<label for="wtbp_ce_settings[' . $args[ 'id' ] . ']"> ' . $args[ 'desc' ] . '</label>';


	echo $html;
}

/**
 * Input type checkbox - Callback
 */
function wtbp_ce_checkbox_callback( $args ) {
	global $wtbp_ce_settings;

	$checked = isset( $wtbp_ce_settings[ $args[ 'id' ] ] ) ? checked( 1, $wtbp_ce_settings[ $args[ 'id' ] ], false ) : '';
	$html	 = '<input type="checkbox" id="wtbp_ce_settings[' . $args[ 'id' ] . ']" name="wtbp_ce_settings[' . $args[ 'id' ] . ']" value="1" ' . $checked . '/>';
	$html .= '<label for="wtbp_ce_settings[' . $args[ 'id' ] . ']"> ' . $args[ 'desc' ] . '</label>';
	echo $html;
}

/**
 * Input type textarea - Callback
 */
function wtbp_ce_textarea_callback( $args ) {
	global $wtbp_ce_settings;
	$o = $wtbp_ce_settings;

	if ( isset( $o[ $args[ 'id' ] ] ) && !empty( $o[ $args[ 'id' ] ] ) ) {
		$value = $o[ $args[ 'id' ] ];
	} else {
		$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
	}


	// Restore default message
	$extra_info		 = '';
	$default_text	 = '';
	if (
	isset( $args[ 'class' ] ) && $args[ 'class' ] === 'wtbp-ce-hidden-field' &&
	isset( $args[ 'std' ] ) && !empty( $args[ 'std' ] )
	) {

		$extra_info = '<span class="wtbp-ce-default-btn button button-secondary">' . __( 'Restore default title and message', WTBP_CE_DOMAIN ) . '</span>';

		$default_text = '<textarea class="wtbp-ce-default-text">' . $args[ 'std' ] . '</textarea>';
	}




	$size	 = ( isset( $args[ 'size' ] ) && !is_null( $args[ 'size' ] ) ) ? $args[ 'size' ] : 'regular';
	$html	 = '<p><label for="wtbp_ce_settings[' . $args[ 'id' ] . ']"> ' . $args[ 'desc' ] . '</label></p><br /><div class="wtbp-ce-extra-info wtbp-ce-row">' . $extra_info . '</div>';
	$html .= '<textarea class="large-text wtbp_ce_message_textarea" cols="50" rows="13" id="wtbp_ce_settings[' . $args[ 'id' ] . ']" name="wtbp_ce_settings[' . $args[ 'id' ] . ']">' . esc_textarea( wp_unslash( $value ) ) . '</textarea>';
	$html .= $default_text;

	echo $html;
}

/**
 * WP Editor ( email_editor ) - Callback
 */
function wtbp_ce_email_editor_callback( $args ) {
	global $wtbp_ce_settings;
	$o = $wtbp_ce_settings;

	if ( isset( $o[ $args[ 'id' ] ] ) && !empty( $o[ $args[ 'id' ] ] ) ) {
		$value = $o[ $args[ 'id' ] ];
	} else {
		$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
	}

	// Restore default message
	$extra_info		 = '';
	$default_text	 = '';
	if (
	isset( $args[ 'class' ] ) && $args[ 'class' ] === 'wtbp-ce-hidden-field' &&
	isset( $args[ 'std' ] ) && !empty( $args[ 'std' ] )
	) {

		$extra_info = '<span class="wtbp-ce-default-btn button button-secondary">' . __( 'Restore default title and message', WTBP_CE_DOMAIN ) . '</span>';

		$default_text = '<textarea class="wtbp-ce-default-text">' . $args[ 'std' ] . '</textarea>';
	}

	$settings = array(
		'media_buttons'	 => false,
		'editor_height'	 => 300,
		'quicktags'		 => true,
		'editor_class'	 => 'wtbp_ce_message_textarea',
		'textarea_name'	 => 'wtbp_ce_settings[' . $args[ 'id' ] . ']',
	);

	echo '<p><label for="wtbp_ce_settings[' . $args[ 'id' ] . ']"> ' . $args[ 'desc' ] . '</label></p><br /><div class="wtbp-ce-extra-info wtbp-ce-row">' . $extra_info . '</div>';

	wp_editor(  wpautop( wp_unslash(  wp_kses_post( $value ) ) ), $args[ 'id' ], $settings );
	echo $default_text;
}

/**
 * Select - Callback
 */
function wtbp_ce_select_callback( $args ) {
	global $wtbp_ce_settings;

	if ( isset( $wtbp_ce_settings[ $args[ 'id' ] ] ) )
		$value	 = $wtbp_ce_settings[ $args[ 'id' ] ];
	else
		$value	 = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';

	$html = '<select id="wtbp_ce_settings[' . $args[ 'id' ] . ']" name="wtbp_ce_settings[' . $args[ 'id' ] . ']"/>';

	foreach ( $args[ 'options' ] as $option => $name ) :
		$selected = selected( $option, $value, false );
		$html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
	endforeach;

	$html .= '</select>';
	$html .= '<label for="wtbp_ce_settings[' . $args[ 'id' ] . ']"> ' . $args[ 'desc' ] . '</label>';

	echo $html;
}

/**
 * Header - Callback
 */
function wtbp_ce_header_callback( $args ) {
	echo '<hr/>';
}

/**
 * No callback
 */
function wtbp_ce_missing_callback( $args ) {
	printf( __( 'Missing callback function for the option <strong>%s</strong>', WTBP_CE_DOMAIN ), $args[ 'id' ] );
}

/*
 * Set editor or textareta
 */

function wtbp_ce_set_type() {

	if ( WTBP_WP_CE()->core->is_html ) {
		return 'email_editor';
	} else {
		return 'textarea';
	}
}
