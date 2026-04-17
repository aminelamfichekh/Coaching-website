<?php
/**
 * The template to display custom header from the ThemeREX Addons Layouts
 *
 * @package GRIT
 * @since GRIT 1.0.06
 */

$grit_header_css   = '';
$grit_header_image = get_header_image();
$grit_header_video = grit_get_header_video();
if ( ! empty( $grit_header_image ) && grit_trx_addons_featured_image_override( is_singular() || grit_storage_isset( 'blog_archive' ) || is_category() ) ) {
	$grit_header_image = grit_get_current_mode_image( $grit_header_image );
}

$grit_header_id = grit_get_custom_header_id();
$grit_header_meta = get_post_meta( $grit_header_id, 'trx_addons_options', true );
if ( ! empty( $grit_header_meta['margin'] ) ) {
	grit_add_inline_css( sprintf( '.page_content_wrap{padding-top:%s}', esc_attr( grit_prepare_css_value( $grit_header_meta['margin'] ) ) ) );
}

?><header class="top_panel top_panel_custom top_panel_custom_<?php echo esc_attr( $grit_header_id ); ?> top_panel_custom_<?php echo esc_attr( sanitize_title( get_the_title( $grit_header_id ) ) ); ?>
				<?php
				echo ! empty( $grit_header_image ) || ! empty( $grit_header_video )
					? ' with_bg_image'
					: ' without_bg_image';
				if ( '' != $grit_header_video ) {
					echo ' with_bg_video';
				}
				if ( '' != $grit_header_image ) {
					echo ' ' . esc_attr( grit_add_inline_css_class( 'background-image: url(' . esc_url( $grit_header_image ) . ');' ) );
				}
				if ( is_single() && has_post_thumbnail() ) {
					echo ' with_featured_image';
				}
				if ( grit_is_on( grit_get_theme_option( 'header_fullheight' ) ) ) {
					echo ' header_fullheight grit-full-height';
				}
				$grit_header_scheme = grit_get_theme_option( 'header_scheme' );
				if ( ! empty( $grit_header_scheme ) && ! grit_is_inherit( $grit_header_scheme  ) ) {
					echo ' scheme_' . esc_attr( $grit_header_scheme );
				}
				?>
">
	<?php

	// Background video
	if ( ! empty( $grit_header_video ) ) {
		get_template_part( apply_filters( 'grit_filter_get_template_part', 'templates/header-video' ) );
	}

	// Custom header's layout
	do_action( 'grit_action_show_layout', $grit_header_id );

	// Header widgets area
	get_template_part( apply_filters( 'grit_filter_get_template_part', 'templates/header-widgets' ) );

	?>
</header>
