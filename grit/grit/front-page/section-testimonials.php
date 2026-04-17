<div class="front_page_section front_page_section_testimonials<?php
	$grit_scheme = grit_get_theme_option( 'front_page_testimonials_scheme' );
	if ( ! empty( $grit_scheme ) && ! grit_is_inherit( $grit_scheme ) ) {
		echo ' scheme_' . esc_attr( $grit_scheme );
	}
	echo ' front_page_section_paddings_' . esc_attr( grit_get_theme_option( 'front_page_testimonials_paddings' ) );
	if ( grit_get_theme_option( 'front_page_testimonials_stack' ) ) {
		echo ' sc_stack_section_on';
	}
?>"
		<?php
		$grit_css      = '';
		$grit_bg_image = grit_get_theme_option( 'front_page_testimonials_bg_image' );
		if ( ! empty( $grit_bg_image ) ) {
			$grit_css .= 'background-image: url(' . esc_url( grit_get_attachment_url( $grit_bg_image ) ) . ');';
		}
		if ( ! empty( $grit_css ) ) {
			echo ' style="' . esc_attr( $grit_css ) . '"';
		}
		?>
>
<?php
	// Add anchor
	$grit_anchor_icon = grit_get_theme_option( 'front_page_testimonials_anchor_icon' );
	$grit_anchor_text = grit_get_theme_option( 'front_page_testimonials_anchor_text' );
if ( ( ! empty( $grit_anchor_icon ) || ! empty( $grit_anchor_text ) ) && shortcode_exists( 'trx_sc_anchor' ) ) {
	echo do_shortcode(
		'[trx_sc_anchor id="front_page_section_testimonials"'
									. ( ! empty( $grit_anchor_icon ) ? ' icon="' . esc_attr( $grit_anchor_icon ) . '"' : '' )
									. ( ! empty( $grit_anchor_text ) ? ' title="' . esc_attr( $grit_anchor_text ) . '"' : '' )
									. ']'
	);
}
?>
	<div class="front_page_section_inner front_page_section_testimonials_inner
	<?php
	if ( grit_get_theme_option( 'front_page_testimonials_fullheight' ) ) {
		echo ' grit-full-height sc_layouts_flex sc_layouts_columns_middle';
	}
	?>
			"
			<?php
			$grit_css      = '';
			$grit_bg_mask  = grit_get_theme_option( 'front_page_testimonials_bg_mask' );
			$grit_bg_color_type = grit_get_theme_option( 'front_page_testimonials_bg_color_type' );
			if ( 'custom' == $grit_bg_color_type ) {
				$grit_bg_color = grit_get_theme_option( 'front_page_testimonials_bg_color' );
			} elseif ( 'scheme_bg_color' == $grit_bg_color_type ) {
				$grit_bg_color = grit_get_scheme_color( 'bg_color', $grit_scheme );
			} else {
				$grit_bg_color = '';
			}
			if ( ! empty( $grit_bg_color ) && $grit_bg_mask > 0 ) {
				$grit_css .= 'background-color: ' . esc_attr(
					1 == $grit_bg_mask ? $grit_bg_color : grit_hex2rgba( $grit_bg_color, $grit_bg_mask )
				) . ';';
			}
			if ( ! empty( $grit_css ) ) {
				echo ' style="' . esc_attr( $grit_css ) . '"';
			}
			?>
	>
		<div class="front_page_section_content_wrap front_page_section_testimonials_content_wrap content_wrap">
			<?php
			// Caption
			$grit_caption = grit_get_theme_option( 'front_page_testimonials_caption' );
			if ( ! empty( $grit_caption ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<h2 class="front_page_section_caption front_page_section_testimonials_caption front_page_block_<?php echo ! empty( $grit_caption ) ? 'filled' : 'empty'; ?>"><?php echo wp_kses( $grit_caption, 'grit_kses_content' ); ?></h2>
				<?php
			}

			// Description (text)
			$grit_description = grit_get_theme_option( 'front_page_testimonials_description' );
			if ( ! empty( $grit_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<div class="front_page_section_description front_page_section_testimonials_description front_page_block_<?php echo ! empty( $grit_description ) ? 'filled' : 'empty'; ?>"><?php echo wp_kses( wpautop( $grit_description ), 'grit_kses_content' ); ?></div>
				<?php
			}

			// Content (widgets)
			?>
			<div class="front_page_section_output front_page_section_testimonials_output">
				<?php
				if ( is_active_sidebar( 'front_page_testimonials_widgets' ) ) {
					dynamic_sidebar( 'front_page_testimonials_widgets' );
				} elseif ( current_user_can( 'edit_theme_options' ) ) {
					if ( ! grit_exists_trx_addons() ) {
						grit_customizer_need_trx_addons_message();
					} else {
						grit_customizer_need_widgets_message( 'front_page_testimonials_caption', 'ThemeREX Addons - Testimonials' );
					}
				}
				?>
			</div>
		</div>
	</div>
</div>
