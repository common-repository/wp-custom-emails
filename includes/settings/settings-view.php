<?php
/**
 * Strona z ustawieniami
*/

// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Funkcja tworząca stronę z ustawieniami
 */
function wtbp_ce_settings_page() {
	global $wtbp_ce_options;
	$o = $wtbp_ce_options;
        
	$active_tab = isset( $_GET[ 'tab' ] ) && array_key_exists( $_GET['tab'], wtbp_ce_get_settings_tabs() ) ? $_GET[ 'tab' ] : 'general';

	ob_start();
	?>
	<div id="wtbp-settings" class="wrap wtbp-settings">
		<h2 class="nav-tab-wrapper">
			<?php
			foreach( wtbp_ce_get_settings_tabs() as $id => $name ) {
				$tab_url = add_query_arg( array(
					'settings-updated' => false,
					'tab' => $id
				) );

				$active = $active_tab == $id ? ' nav-tab-active' : '';

				echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $name ) . '" class="nav-tab' . $active . '">';
					echo esc_html( $name );
				echo '</a>';
			}
			?>
		</h2>
		<div>
                   
			<form class="wtbp-emails-form" method="post" action="options.php">
				<table class="form-table">
				<?php
				settings_fields( 'wtbp_ce_settings' );
                                
				do_settings_fields( 'wtbp_ce_settings_' . $active_tab, 'wtbp_ce_settings_' . $active_tab );
				?>
				</table>
				<?php submit_button(); ?>
			</form>
		</div><!-- #tab_container-->
	</div><!-- .wrap -->
	<?php
	echo ob_get_clean();
}
