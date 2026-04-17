<?php
/**
 * The template to display the socials in the footer
 *
 * @package GRIT
 * @since GRIT 1.0.10
 */


// Socials
if ( grit_is_on( grit_get_theme_option( 'socials_in_footer' ) ) ) {
	$grit_output = grit_get_socials_links();
	if ( '' != $grit_output ) {
		?>
		<div class="footer_socials_wrap socials_wrap">
			<div class="footer_socials_inner">
				<?php grit_show_layout( $grit_output ); ?>
			</div>
		</div>
		<?php
	}
}
