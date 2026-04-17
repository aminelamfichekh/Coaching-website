<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package GRIT
 * @since GRIT 1.0
 */

if ( grit_sidebar_present() ) {
	
	$grit_sidebar_type = grit_get_theme_option( 'sidebar_type' );
	if ( 'custom' == $grit_sidebar_type && ! grit_is_layouts_available() ) {
		$grit_sidebar_type = 'default';
	}
	
	// Catch output to the buffer
	ob_start();
	if ( 'default' == $grit_sidebar_type ) {
		// Default sidebar with widgets
		$grit_sidebar_name = grit_get_theme_option( 'sidebar_widgets' );
		grit_storage_set( 'current_sidebar', 'sidebar' );
		if ( is_active_sidebar( $grit_sidebar_name ) ) {
			dynamic_sidebar( $grit_sidebar_name );
		}
	} else {
		// Custom sidebar from Layouts Builder
		$grit_sidebar_id = grit_get_custom_sidebar_id();
		do_action( 'grit_action_show_layout', $grit_sidebar_id );
	}
	$grit_out = trim( ob_get_contents() );
	ob_end_clean();
	
	// If any html is present - display it
	if ( ! empty( $grit_out ) ) {
		$grit_sidebar_position    = grit_get_theme_option( 'sidebar_position' );
		$grit_sidebar_position_ss = grit_get_theme_option( 'sidebar_position_ss' );
		?>
		<div class="sidebar widget_area
			<?php
			echo ' ' . esc_attr( $grit_sidebar_position );
			echo ' sidebar_' . esc_attr( $grit_sidebar_position_ss );
			echo ' sidebar_' . esc_attr( $grit_sidebar_type );

			$grit_sidebar_scheme = apply_filters( 'grit_filter_sidebar_scheme', grit_get_theme_option( 'sidebar_scheme' ) );
			if ( ! empty( $grit_sidebar_scheme ) && ! grit_is_inherit( $grit_sidebar_scheme ) && 'custom' != $grit_sidebar_type ) {
				echo ' scheme_' . esc_attr( $grit_sidebar_scheme );
			}
			?>
		" role="complementary">
			<?php

			// Skip link anchor to fast access to the sidebar from keyboard
			?>
			<a id="sidebar_skip_link_anchor" class="grit_skip_link_anchor" href="#"></a>
			<?php

			do_action( 'grit_action_before_sidebar_wrap', 'sidebar' );

			// Button to show/hide sidebar on mobile
			if ( in_array( $grit_sidebar_position_ss, array( 'above', 'float' ) ) ) {
				$grit_title = apply_filters( 'grit_filter_sidebar_control_title', 'float' == $grit_sidebar_position_ss ? esc_html__( 'Show Sidebar', 'grit' ) : '' );
				$grit_text  = apply_filters( 'grit_filter_sidebar_control_text', 'above' == $grit_sidebar_position_ss ? esc_html__( 'Show Sidebar', 'grit' ) : '' );
				?>
				<a href="#" class="sidebar_control" title="<?php echo esc_attr( $grit_title ); ?>"><?php echo esc_html( $grit_text ); ?></a>
				<?php
			}
			?>
			<div class="sidebar_inner">
				<?php
				do_action( 'grit_action_before_sidebar', 'sidebar' );
				grit_show_layout( preg_replace( "/<\/aside>[\r\n\s]*<aside/", '</aside><aside', $grit_out ) );
				do_action( 'grit_action_after_sidebar', 'sidebar' );
				?>
			</div>
			<?php

			do_action( 'grit_action_after_sidebar_wrap', 'sidebar' );

			?>
		</div>
		<div class="clearfix"></div>
		<?php
	}
}
