<?php
/**
 * The template to display the widgets area in the header
 *
 * @package GRIT
 * @since GRIT 1.0
 */

// Header sidebar
$grit_header_name    = grit_get_theme_option( 'header_widgets' );
$grit_header_present = ! grit_is_off( $grit_header_name ) && is_active_sidebar( $grit_header_name );
if ( $grit_header_present ) {
	grit_storage_set( 'current_sidebar', 'header' );
	$grit_header_wide = grit_get_theme_option( 'header_wide' );
	ob_start();
	if ( is_active_sidebar( $grit_header_name ) ) {
		dynamic_sidebar( $grit_header_name );
	}
	$grit_widgets_output = ob_get_contents();
	ob_end_clean();
	if ( ! empty( $grit_widgets_output ) ) {
		$grit_widgets_output = preg_replace( "/<\/aside>[\r\n\s]*<aside/", '</aside><aside', $grit_widgets_output );
		$grit_need_columns   = strpos( $grit_widgets_output, 'columns_wrap' ) === false;
		if ( $grit_need_columns ) {
			$grit_columns = max( 0, (int) grit_get_theme_option( 'header_columns' ) );
			if ( 0 == $grit_columns ) {
				$grit_columns = min( 6, max( 1, grit_tags_count( $grit_widgets_output, 'aside' ) ) );
			}
			if ( $grit_columns > 1 ) {
				$grit_widgets_output = preg_replace( '/<aside([^>]*)class="widget/', '<aside$1class="column-1_' . esc_attr( $grit_columns ) . ' widget', $grit_widgets_output );
			} else {
				$grit_need_columns = false;
			}
		}
		?>
		<div class="header_widgets_wrap widget_area<?php echo ! empty( $grit_header_wide ) ? ' header_fullwidth' : ' header_boxed'; ?>">
			<?php do_action( 'grit_action_before_sidebar_wrap', 'header' ); ?>
			<div class="header_widgets_inner widget_area_inner">
				<?php
				if ( ! $grit_header_wide ) {
					?>
					<div class="content_wrap">
					<?php
				}
				if ( $grit_need_columns ) {
					?>
					<div class="columns_wrap">
					<?php
				}
				do_action( 'grit_action_before_sidebar', 'header' );
				grit_show_layout( $grit_widgets_output );
				do_action( 'grit_action_after_sidebar', 'header' );
				if ( $grit_need_columns ) {
					?>
					</div>	<!-- /.columns_wrap -->
					<?php
				}
				if ( ! $grit_header_wide ) {
					?>
					</div>	<!-- /.content_wrap -->
					<?php
				}
				?>
			</div>	<!-- /.header_widgets_inner -->
			<?php do_action( 'grit_action_after_sidebar_wrap', 'header' ); ?>
		</div>	<!-- /.header_widgets_wrap -->
		<?php
	}
}
