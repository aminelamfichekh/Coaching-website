<?php
/**
 * The template to display default site footer
 *
 * @package GRIT
 * @since GRIT 1.0.10
 */

?>
<footer class="footer_wrap footer_default
<?php
$grit_footer_scheme = grit_get_theme_option( 'footer_scheme' );
if ( ! empty( $grit_footer_scheme ) && ! grit_is_inherit( $grit_footer_scheme  ) ) {
	echo ' scheme_' . esc_attr( $grit_footer_scheme );
}
?>
				">
	<?php

	// Footer widgets area
	get_template_part( apply_filters( 'grit_filter_get_template_part', 'templates/footer-widgets' ) );

	// Logo
	get_template_part( apply_filters( 'grit_filter_get_template_part', 'templates/footer-logo' ) );

	// Socials
	get_template_part( apply_filters( 'grit_filter_get_template_part', 'templates/footer-socials' ) );

	// Copyright area
	get_template_part( apply_filters( 'grit_filter_get_template_part', 'templates/footer-copyright' ) );

	?>
</footer><!-- /.footer_wrap -->
