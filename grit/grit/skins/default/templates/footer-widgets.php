<?php
/**
 * The template to display the widgets area in the footer
 *
 * @package GRIT
 * @since GRIT 1.0.10
 */

// Footer sidebar
$grit_footer_name    = grit_get_theme_option( 'footer_widgets' );
$grit_footer_present = ! grit_is_off( $grit_footer_name ) && is_active_sidebar( $grit_footer_name );
if ( $grit_footer_present ) {
	grit_storage_set( 'current_sidebar', 'footer' );
	$grit_footer_wide = grit_get_theme_option( 'footer_wide' );
	ob_start();
	if ( is_active_sidebar( $grit_footer_name ) ) {
		dynamic_sidebar( $grit_footer_name );
	}
	$grit_out = trim( ob_get_contents() );
	ob_end_clean();
	if ( ! empty( $grit_out ) ) {
		$grit_out          = preg_replace( "/<\\/aside>[\r\n\s]*<aside/", '</aside><aside', $grit_out );
		$grit_need_columns = true;   //or check: strpos($grit_out, 'columns_wrap')===false;
		if ( $grit_need_columns ) {
			$grit_columns = max( 0, (int) grit_get_theme_option( 'footer_columns' ) );			
			if ( 0 == $grit_columns ) {
				$grit_columns = min( 4, max( 1, grit_tags_count( $grit_out, 'aside' ) ) );
			}
			if ( $grit_columns > 1 ) {
				$grit_out = preg_replace( '/<aside([^>]*)class="widget/', '<aside$1class="column-1_' . esc_attr( $grit_columns ) . ' widget', $grit_out );
			} else {
				$grit_need_columns = false;
			}
		}
		?>
		<div class="footer_widgets_wrap widget_area<?php echo ! empty( $grit_footer_wide ) ? ' footer_fullwidth' : ''; ?> sc_layouts_row sc_layouts_row_type_normal">
			<?php do_action( 'grit_action_before_sidebar_wrap', 'footer' ); ?>
			<div class="footer_widgets_inner widget_area_inner">
				<?php
				if ( ! $grit_footer_wide ) {
					?>
					<div class="content_wrap">
					<?php
				}
				if ( $grit_need_columns ) {
					?>
					<div class="columns_wrap">
					<?php
				}
				do_action( 'grit_action_before_sidebar', 'footer' );
				grit_show_layout( $grit_out );
				do_action( 'grit_action_after_sidebar', 'footer' );
				if ( $grit_need_columns ) {
					?>
					</div><!-- /.columns_wrap -->
					<?php
				}
				if ( ! $grit_footer_wide ) {
					?>
					</div><!-- /.content_wrap -->
					<?php
				}
				?>
			</div><!-- /.footer_widgets_inner -->
			<?php do_action( 'grit_action_after_sidebar_wrap', 'footer' ); ?>
		</div><!-- /.footer_widgets_wrap -->
		<?php
	}
}
