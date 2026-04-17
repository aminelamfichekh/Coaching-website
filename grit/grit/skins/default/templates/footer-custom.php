<?php
/**
 * The template to display default site footer
 *
 * @package GRIT
 * @since GRIT 1.0.10
 */

$grit_footer_id = grit_get_custom_footer_id();
$grit_footer_meta = get_post_meta( $grit_footer_id, 'trx_addons_options', true );
if ( ! empty( $grit_footer_meta['margin'] ) ) {
	grit_add_inline_css( sprintf( '.page_content_wrap{padding-bottom:%s}', esc_attr( grit_prepare_css_value( $grit_footer_meta['margin'] ) ) ) );
}
?>
<footer class="footer_wrap footer_custom footer_custom_<?php echo esc_attr( $grit_footer_id ); ?> footer_custom_<?php echo esc_attr( sanitize_title( get_the_title( $grit_footer_id ) ) ); ?>
						<?php
						$grit_footer_scheme = grit_get_theme_option( 'footer_scheme' );
						if ( ! empty( $grit_footer_scheme ) && ! grit_is_inherit( $grit_footer_scheme  ) ) {
							echo ' scheme_' . esc_attr( $grit_footer_scheme );
						}
						?>
						">
	<?php
	// Custom footer's layout
	do_action( 'grit_action_show_layout', $grit_footer_id );
	?>
</footer><!-- /.footer_wrap -->
