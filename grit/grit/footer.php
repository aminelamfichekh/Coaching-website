<?php
/**
 * The Footer: widgets area, logo, footer menu and socials
 *
 * @package GRIT
 * @since GRIT 1.0
 */

							do_action( 'grit_action_page_content_end_text' );
							
							// Widgets area below the content
							grit_create_widgets_area( 'widgets_below_content' );
						
							do_action( 'grit_action_page_content_end' );
							?>
						</div>
						<?php
						
						do_action( 'grit_action_after_page_content' );

						// Show main sidebar
						get_sidebar();

						do_action( 'grit_action_content_wrap_end' );
						?>
					</div>
					<?php

					do_action( 'grit_action_after_content_wrap' );

					// Widgets area below the page and related posts below the page
					$grit_body_style = grit_get_theme_option( 'body_style' );
					$grit_widgets_name = grit_get_theme_option( 'widgets_below_page' );
					$grit_show_widgets = ! grit_is_off( $grit_widgets_name ) && is_active_sidebar( $grit_widgets_name );
					$grit_show_related = grit_is_single() && grit_get_theme_option( 'related_position' ) == 'below_page';
					if ( $grit_show_widgets || $grit_show_related ) {
						if ( 'fullscreen' != $grit_body_style ) {
							?>
							<div class="content_wrap">
							<?php
						}
						// Show related posts before footer
						if ( $grit_show_related ) {
							do_action( 'grit_action_related_posts' );
						}

						// Widgets area below page content
						if ( $grit_show_widgets ) {
							grit_create_widgets_area( 'widgets_below_page' );
						}
						if ( 'fullscreen' != $grit_body_style ) {
							?>
							</div>
							<?php
						}
					}
					do_action( 'grit_action_page_content_wrap_end' );
					?>
			</div>
			<?php
			do_action( 'grit_action_after_page_content_wrap' );

			// Don't display the footer elements while actions 'full_post_loading' and 'prev_post_loading'
			if ( ( ! grit_is_singular( 'post' ) && ! grit_is_singular( 'attachment' ) ) || ! in_array ( grit_get_value_gp( 'action' ), array( 'full_post_loading', 'prev_post_loading' ) ) ) {
				
				// Skip link anchor to fast access to the footer from keyboard
				?>
				<a id="footer_skip_link_anchor" class="grit_skip_link_anchor" href="#"></a>
				<?php

				do_action( 'grit_action_before_footer' );

				// Footer
				$grit_footer_type = grit_get_theme_option( 'footer_type' );
				if ( 'custom' == $grit_footer_type && ! grit_is_layouts_available() ) {
					$grit_footer_type = 'default';
				}
				get_template_part( apply_filters( 'grit_filter_get_template_part', "templates/footer-" . sanitize_file_name( $grit_footer_type ) ) );

				do_action( 'grit_action_after_footer' );

			}
			?>

			<?php do_action( 'grit_action_page_wrap_end' ); ?>

		</div>

		<?php do_action( 'grit_action_after_page_wrap' ); ?>

	</div>

	<?php do_action( 'grit_action_after_body' ); ?>

	<?php wp_footer(); ?>

</body>
</html>