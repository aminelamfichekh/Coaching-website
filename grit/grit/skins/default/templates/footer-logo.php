<?php
/**
 * The template to display the site logo in the footer
 *
 * @package GRIT
 * @since GRIT 1.0.10
 */

// Logo
if ( grit_is_on( grit_get_theme_option( 'logo_in_footer' ) ) ) {
	$grit_logo_image = grit_get_logo_image( 'footer' );
	$grit_logo_text  = get_bloginfo( 'name' );
	if ( ! empty( $grit_logo_image['logo'] ) || ! empty( $grit_logo_text ) ) {
		?>
		<div class="footer_logo_wrap">
			<div class="footer_logo_inner">
				<?php
				if ( ! empty( $grit_logo_image['logo'] ) ) {
					$grit_attr = grit_getimagesize( $grit_logo_image['logo'] );
					echo '<a href="' . esc_url( home_url( '/' ) ) . '">'
							. '<img src="' . esc_url( $grit_logo_image['logo'] ) . '"'
								. ( ! empty( $grit_logo_image['logo_retina'] ) ? ' srcset="' . esc_url( $grit_logo_image['logo_retina'] ) . ' 2x"' : '' )
								. ' class="logo_footer_image"'
								. ' alt="' . esc_attr__( 'Site logo', 'grit' ) . '"'
								. ( ! empty( $grit_attr[3] ) ? ' ' . wp_kses_data( $grit_attr[3] ) : '' )
							. '>'
						. '</a>';
				} elseif ( ! empty( $grit_logo_text ) ) {
					echo '<h1 class="logo_footer_text">'
							. '<a href="' . esc_url( home_url( '/' ) ) . '">'
								. esc_html( $grit_logo_text )
							. '</a>'
						. '</h1>';
				}
				?>
			</div>
		</div>
		<?php
	}
}
