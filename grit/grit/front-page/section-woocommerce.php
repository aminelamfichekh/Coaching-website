<?php
$grit_woocommerce_sc = grit_get_theme_option( 'front_page_woocommerce_products' );
if ( ! empty( $grit_woocommerce_sc ) ) {
	?><div class="front_page_section front_page_section_woocommerce<?php
		$grit_scheme = grit_get_theme_option( 'front_page_woocommerce_scheme' );
		if ( ! empty( $grit_scheme ) && ! grit_is_inherit( $grit_scheme ) ) {
			echo ' scheme_' . esc_attr( $grit_scheme );
		}
		echo ' front_page_section_paddings_' . esc_attr( grit_get_theme_option( 'front_page_woocommerce_paddings' ) );
		if ( grit_get_theme_option( 'front_page_woocommerce_stack' ) ) {
			echo ' sc_stack_section_on';
		}
	?>"
			<?php
			$grit_css      = '';
			$grit_bg_image = grit_get_theme_option( 'front_page_woocommerce_bg_image' );
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
		$grit_anchor_icon = grit_get_theme_option( 'front_page_woocommerce_anchor_icon' );
		$grit_anchor_text = grit_get_theme_option( 'front_page_woocommerce_anchor_text' );
		if ( ( ! empty( $grit_anchor_icon ) || ! empty( $grit_anchor_text ) ) && shortcode_exists( 'trx_sc_anchor' ) ) {
			echo do_shortcode(
				'[trx_sc_anchor id="front_page_section_woocommerce"'
											. ( ! empty( $grit_anchor_icon ) ? ' icon="' . esc_attr( $grit_anchor_icon ) . '"' : '' )
											. ( ! empty( $grit_anchor_text ) ? ' title="' . esc_attr( $grit_anchor_text ) . '"' : '' )
											. ']'
			);
		}
	?>
		<div class="front_page_section_inner front_page_section_woocommerce_inner
			<?php
			if ( grit_get_theme_option( 'front_page_woocommerce_fullheight' ) ) {
				echo ' grit-full-height sc_layouts_flex sc_layouts_columns_middle';
			}
			?>
				"
				<?php
				$grit_css      = '';
				$grit_bg_mask  = grit_get_theme_option( 'front_page_woocommerce_bg_mask' );
				$grit_bg_color_type = grit_get_theme_option( 'front_page_woocommerce_bg_color_type' );
				if ( 'custom' == $grit_bg_color_type ) {
					$grit_bg_color = grit_get_theme_option( 'front_page_woocommerce_bg_color' );
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
			<div class="front_page_section_content_wrap front_page_section_woocommerce_content_wrap content_wrap woocommerce">
				<?php
				// Content wrap with title and description
				$grit_caption     = grit_get_theme_option( 'front_page_woocommerce_caption' );
				$grit_description = grit_get_theme_option( 'front_page_woocommerce_description' );
				if ( ! empty( $grit_caption ) || ! empty( $grit_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
					// Caption
					if ( ! empty( $grit_caption ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
						?>
						<h2 class="front_page_section_caption front_page_section_woocommerce_caption front_page_block_<?php echo ! empty( $grit_caption ) ? 'filled' : 'empty'; ?>">
						<?php
							echo wp_kses( $grit_caption, 'grit_kses_content' );
						?>
						</h2>
						<?php
					}

					// Description (text)
					if ( ! empty( $grit_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
						?>
						<div class="front_page_section_description front_page_section_woocommerce_description front_page_block_<?php echo ! empty( $grit_description ) ? 'filled' : 'empty'; ?>">
						<?php
							echo wp_kses( wpautop( $grit_description ), 'grit_kses_content' );
						?>
						</div>
						<?php
					}
				}

				// Content (widgets)
				?>
				<div class="front_page_section_output front_page_section_woocommerce_output list_products shop_mode_thumbs">
					<?php
					if ( 'products' == $grit_woocommerce_sc ) {
						$grit_woocommerce_sc_ids      = grit_get_theme_option( 'front_page_woocommerce_products_per_page' );
						$grit_woocommerce_sc_per_page = count( explode( ',', $grit_woocommerce_sc_ids ) );
					} else {
						$grit_woocommerce_sc_per_page = max( 1, (int) grit_get_theme_option( 'front_page_woocommerce_products_per_page' ) );
					}
					$grit_woocommerce_sc_columns = max( 1, min( $grit_woocommerce_sc_per_page, (int) grit_get_theme_option( 'front_page_woocommerce_products_columns' ) ) );
					echo do_shortcode(
						"[{$grit_woocommerce_sc}"
										. ( 'products' == $grit_woocommerce_sc
												? ' ids="' . esc_attr( $grit_woocommerce_sc_ids ) . '"'
												: '' )
										. ( 'product_category' == $grit_woocommerce_sc
												? ' category="' . esc_attr( grit_get_theme_option( 'front_page_woocommerce_products_categories' ) ) . '"'
												: '' )
										. ( 'best_selling_products' != $grit_woocommerce_sc
												? ' orderby="' . esc_attr( grit_get_theme_option( 'front_page_woocommerce_products_orderby' ) ) . '"'
													. ' order="' . esc_attr( grit_get_theme_option( 'front_page_woocommerce_products_order' ) ) . '"'
												: '' )
										. ' per_page="' . esc_attr( $grit_woocommerce_sc_per_page ) . '"'
										. ' columns="' . esc_attr( $grit_woocommerce_sc_columns ) . '"'
						. ']'
					);
					?>
				</div>
			</div>
		</div>
	</div>
	<?php
}
