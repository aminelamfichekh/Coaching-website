<?php
/**
 * The template to display the logo or the site name and the slogan in the Header
 *
 * @package GRIT
 * @since GRIT 1.0
 */

$grit_args = get_query_var( 'grit_logo_args' );

// Site logo
$grit_logo_type   = isset( $grit_args['type'] ) ? $grit_args['type'] : '';
$grit_logo_image  = grit_get_logo_image( $grit_logo_type );
$grit_logo_text   = grit_is_on( grit_get_theme_option( 'logo_text' ) ) ? get_bloginfo( 'name' ) : '';
$grit_logo_slogan = get_bloginfo( 'description', 'display' );
if ( ! empty( $grit_logo_image['logo'] ) || ! empty( $grit_logo_text ) ) {
	?><a class="sc_layouts_logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<?php
		if ( ! empty( $grit_logo_image['logo'] ) ) {
			if ( empty( $grit_logo_type ) && function_exists( 'the_custom_logo' ) && is_numeric($grit_logo_image['logo']) && (int) $grit_logo_image['logo'] > 0 ) {
				the_custom_logo();
			} else {
				$grit_attr = grit_getimagesize( $grit_logo_image['logo'] );
				echo '<img src="' . esc_url( $grit_logo_image['logo'] ) . '"'
						. ( ! empty( $grit_logo_image['logo_retina'] ) ? ' srcset="' . esc_url( $grit_logo_image['logo_retina'] ) . ' 2x"' : '' )
						. ' alt="' . esc_attr( $grit_logo_text ) . '"'
						. ( ! empty( $grit_attr[3] ) ? ' ' . wp_kses_data( $grit_attr[3] ) : '' )
						. '>';
			}
		} else {
			grit_show_layout( grit_prepare_macros( $grit_logo_text ), '<span class="logo_text">', '</span>' );
			grit_show_layout( grit_prepare_macros( $grit_logo_slogan ), '<span class="logo_slogan">', '</span>' );
		}
		?>
	</a>
	<?php
}
