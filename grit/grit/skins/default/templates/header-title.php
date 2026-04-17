<?php
/**
 * The template to display the page title and breadcrumbs
 *
 * @package GRIT
 * @since GRIT 1.0
 */

// Page (category, tag, archive, author) title

if ( grit_need_page_title() ) {
	grit_sc_layouts_showed( 'title', true );
	grit_sc_layouts_showed( 'postmeta', true );
	?>
	<div class="top_panel_title sc_layouts_row sc_layouts_row_type_normal">
		<div class="content_wrap">
			<div class="sc_layouts_column sc_layouts_column_align_center">
				<div class="sc_layouts_item">
					<div class="sc_layouts_title sc_align_center">
						<?php
						// Post meta on the single post
						if ( is_single() ) {
							?>
							<div class="sc_layouts_title_meta">
							<?php
								grit_show_post_meta(
									apply_filters(
										'grit_filter_post_meta_args', array(
											'components' => join( ',', grit_array_get_keys_by_value( grit_get_theme_option( 'meta_parts' ) ) ),
											'counters'   => join( ',', grit_array_get_keys_by_value( grit_get_theme_option( 'counters' ) ) ),
											'seo'        => grit_is_on( grit_get_theme_option( 'seo_snippets' ) ),
										), 'header', 1
									)
								);
							?>
							</div>
							<?php
						}

						// Blog/Post title
						?>
						<div class="sc_layouts_title_title">
							<?php
							$grit_blog_title           = grit_get_blog_title();
							$grit_blog_title_text      = '';
							$grit_blog_title_class     = '';
							$grit_blog_title_link      = '';
							$grit_blog_title_link_text = '';
							if ( is_array( $grit_blog_title ) ) {
								$grit_blog_title_text      = $grit_blog_title['text'];
								$grit_blog_title_class     = ! empty( $grit_blog_title['class'] ) ? ' ' . $grit_blog_title['class'] : '';
								$grit_blog_title_link      = ! empty( $grit_blog_title['link'] ) ? $grit_blog_title['link'] : '';
								$grit_blog_title_link_text = ! empty( $grit_blog_title['link_text'] ) ? $grit_blog_title['link_text'] : '';
							} else {
								$grit_blog_title_text = $grit_blog_title;
							}
							?>
							<h1 itemprop="headline" class="sc_layouts_title_caption<?php echo esc_attr( $grit_blog_title_class ); ?>">
								<?php
								$grit_top_icon = grit_get_term_image_small();
								if ( ! empty( $grit_top_icon ) ) {
									$grit_attr = grit_getimagesize( $grit_top_icon );
									?>
									<img src="<?php echo esc_url( $grit_top_icon ); ?>" alt="<?php esc_attr_e( 'Site icon', 'grit' ); ?>"
										<?php
										if ( ! empty( $grit_attr[3] ) ) {
											grit_show_layout( $grit_attr[3] );
										}
										?>
									>
									<?php
								}
								echo wp_kses_data( $grit_blog_title_text );
								?>
							</h1>
							<?php
							if ( ! empty( $grit_blog_title_link ) && ! empty( $grit_blog_title_link_text ) ) {
								?>
								<a href="<?php echo esc_url( $grit_blog_title_link ); ?>" class="theme_button theme_button_small sc_layouts_title_link"><?php echo esc_html( $grit_blog_title_link_text ); ?></a>
								<?php
							}

							// Category/Tag description
							if ( ! is_paged() && ( is_category() || is_tag() || is_tax() ) ) {
								the_archive_description( '<div class="sc_layouts_title_description">', '</div>' );
							}

							?>
						</div>
						<?php

						// Breadcrumbs
						ob_start();
						do_action( 'grit_action_breadcrumbs' );
						$grit_breadcrumbs = ob_get_contents();
						ob_end_clean();
						grit_show_layout( $grit_breadcrumbs, '<div class="sc_layouts_title_breadcrumbs">', '</div>' );
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
