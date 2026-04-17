<?php
/**
 * The Header: Logo and main menu
 *
 * @package GRIT
 * @since GRIT 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js<?php
	// Class scheme_xxx need in the <html> as context for the <body>!
	echo ' scheme_' . esc_attr( grit_get_theme_option( 'color_scheme' ) );
?>">

<head>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<?php
	if ( function_exists( 'wp_body_open' ) ) {
		wp_body_open();
	} else {
		do_action( 'wp_body_open' );
	}
	do_action( 'grit_action_before_body' );
	?>

	<div class="<?php echo esc_attr( apply_filters( 'grit_filter_body_wrap_class', 'body_wrap' ) ); ?>" <?php do_action('grit_action_body_wrap_attributes'); ?>>

		<?php do_action( 'grit_action_before_page_wrap' ); ?>

		<div class="<?php echo esc_attr( apply_filters( 'grit_filter_page_wrap_class', 'page_wrap' ) ); ?>" <?php do_action('grit_action_page_wrap_attributes'); ?>>

			<?php do_action( 'grit_action_page_wrap_start' ); ?>

			<?php
			$grit_full_post_loading = ( grit_is_singular( 'post' ) || grit_is_singular( 'attachment' ) ) && grit_get_value_gp( 'action' ) == 'full_post_loading';
			$grit_prev_post_loading = ( grit_is_singular( 'post' ) || grit_is_singular( 'attachment' ) ) && grit_get_value_gp( 'action' ) == 'prev_post_loading';

			// Don't display the header elements while actions 'full_post_loading' and 'prev_post_loading'
			if ( ! $grit_full_post_loading && ! $grit_prev_post_loading ) {

				// Short links to fast access to the content, sidebar and footer from the keyboard
				?>
				<a class="grit_skip_link skip_to_content_link" href="#content_skip_link_anchor" tabindex="<?php echo esc_attr( apply_filters( 'grit_filter_skip_links_tabindex', 1 ) ); ?>"><?php esc_html_e( "Skip to content", 'grit' ); ?></a>
				<?php if ( grit_sidebar_present() ) { ?>
				<a class="grit_skip_link skip_to_sidebar_link" href="#sidebar_skip_link_anchor" tabindex="<?php echo esc_attr( apply_filters( 'grit_filter_skip_links_tabindex', 1 ) ); ?>"><?php esc_html_e( "Skip to sidebar", 'grit' ); ?></a>
				<?php } ?>
				<a class="grit_skip_link skip_to_footer_link" href="#footer_skip_link_anchor" tabindex="<?php echo esc_attr( apply_filters( 'grit_filter_skip_links_tabindex', 1 ) ); ?>"><?php esc_html_e( "Skip to footer", 'grit' ); ?></a>

				<?php
				do_action( 'grit_action_before_header' );

				// Header
				$grit_header_type = grit_get_theme_option( 'header_type' );
				if ( 'custom' == $grit_header_type && ! grit_is_layouts_available() ) {
					$grit_header_type = 'default';
				}
				get_template_part( apply_filters( 'grit_filter_get_template_part', "templates/header-" . sanitize_file_name( $grit_header_type ) ) );

				// Side menu
				if ( in_array( grit_get_theme_option( 'menu_side' ), array( 'left', 'right' ) ) ) {
					get_template_part( apply_filters( 'grit_filter_get_template_part', 'templates/header-navi-side' ) );
				}

				// Mobile menu
				get_template_part( apply_filters( 'grit_filter_get_template_part', 'templates/header-navi-mobile' ) );

				do_action( 'grit_action_after_header' );

			}
			?>

			<?php do_action( 'grit_action_before_page_content_wrap' ); ?>

			<div class="page_content_wrap<?php
				if ( grit_is_off( grit_get_theme_option( 'remove_margins' ) ) ) {
					if ( empty( $grit_header_type ) ) {
						$grit_header_type = grit_get_theme_option( 'header_type' );
					}
					if ( 'custom' == $grit_header_type && grit_is_layouts_available() ) {
						$grit_header_id = grit_get_custom_header_id();
						if ( $grit_header_id > 0 ) {
							$grit_header_meta = grit_get_custom_layout_meta( $grit_header_id );
							if ( ! empty( $grit_header_meta['margin'] ) ) {
								?> page_content_wrap_custom_header_margin<?php
							}
						}
					}
					$grit_footer_type = grit_get_theme_option( 'footer_type' );
					if ( 'custom' == $grit_footer_type && grit_is_layouts_available() ) {
						$grit_footer_id = grit_get_custom_footer_id();
						if ( $grit_footer_id ) {
							$grit_footer_meta = grit_get_custom_layout_meta( $grit_footer_id );
							if ( ! empty( $grit_footer_meta['margin'] ) ) {
								?> page_content_wrap_custom_footer_margin<?php
							}
						}
					}
				}
				do_action( 'grit_action_page_content_wrap_class', $grit_prev_post_loading );
				?>"<?php
				if ( apply_filters( 'grit_filter_is_prev_post_loading', $grit_prev_post_loading ) ) {
					?> data-single-style="<?php echo esc_attr( grit_get_theme_option( 'single_style' ) ); ?>"<?php
				}
				do_action( 'grit_action_page_content_wrap_data', $grit_prev_post_loading );
			?>>
				<?php
				do_action( 'grit_action_page_content_wrap', $grit_full_post_loading || $grit_prev_post_loading );

				// Single posts banner
				if ( apply_filters( 'grit_filter_single_post_header', grit_is_singular( 'post' ) || grit_is_singular( 'attachment' ) ) ) {
					if ( $grit_prev_post_loading ) {
						if ( grit_get_theme_option( 'posts_navigation_scroll_which_block' ) != 'article' ) {
							do_action( 'grit_action_between_posts' );
						}
					}
					// Single post thumbnail and title
					$grit_path = apply_filters( 'grit_filter_get_template_part', 'templates/single-styles/' . grit_get_theme_option( 'single_style' ) );
					if ( grit_get_file_dir( $grit_path . '.php' ) != '' ) {
						get_template_part( $grit_path );
					}
				}

				// Widgets area above page
				$grit_body_style   = grit_get_theme_option( 'body_style' );
				$grit_widgets_name = grit_get_theme_option( 'widgets_above_page' );
				$grit_show_widgets = ! grit_is_off( $grit_widgets_name ) && is_active_sidebar( $grit_widgets_name );
				if ( $grit_show_widgets ) {
					if ( 'fullscreen' != $grit_body_style ) {
						?>
						<div class="content_wrap">
							<?php
					}
					grit_create_widgets_area( 'widgets_above_page' );
					if ( 'fullscreen' != $grit_body_style ) {
						?>
						</div>
						<?php
					}
				}

				// Content area
				do_action( 'grit_action_before_content_wrap' );
				?>
				<div class="content_wrap<?php echo 'fullscreen' == $grit_body_style ? '_fullscreen' : ''; ?>">

					<?php do_action( 'grit_action_content_wrap_start' ); ?>

					<div class="content">
						<?php
						do_action( 'grit_action_page_content_start' );

						// Skip link anchor to fast access to the content from keyboard
						?>
						<a id="content_skip_link_anchor" class="grit_skip_link_anchor" href="#"></a>
						<?php
						// Single posts banner between prev/next posts
						if ( ( grit_is_singular( 'post' ) || grit_is_singular( 'attachment' ) )
							&& $grit_prev_post_loading 
							&& grit_get_theme_option( 'posts_navigation_scroll_which_block' ) == 'article'
						) {
							do_action( 'grit_action_between_posts' );
						}

						// Widgets area above content
						grit_create_widgets_area( 'widgets_above_content' );

						do_action( 'grit_action_page_content_start_text' );
