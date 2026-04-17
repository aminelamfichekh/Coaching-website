<?php
/**
 * The default template to display the content
 *
 * Used for index/archive/search.
 *
 * @package GRIT
 * @since GRIT 1.0
 */

$grit_template_args = get_query_var( 'grit_template_args' );
$grit_columns = 1;
if ( is_array( $grit_template_args ) ) {
	$grit_columns    = empty( $grit_template_args['columns'] ) ? 1 : max( 1, $grit_template_args['columns'] );
	$grit_blog_style = array( $grit_template_args['type'], $grit_columns );
	if ( ! empty( $grit_template_args['slider'] ) ) {
		?><div class="slider-slide swiper-slide">
		<?php
	} elseif ( $grit_columns > 1 ) {
	    $grit_columns_class = grit_get_column_class( 1, $grit_columns, ! empty( $grit_template_args['columns_tablet']) ? $grit_template_args['columns_tablet'] : '', ! empty($grit_template_args['columns_mobile']) ? $grit_template_args['columns_mobile'] : '' );
		?>
		<div class="<?php echo esc_attr( $grit_columns_class ); ?>">
		<?php
	}
} else {
	$grit_template_args = array();
}
$grit_expanded    = ! grit_sidebar_present() && grit_get_theme_option( 'expand_content' ) == 'expand';
$grit_post_format = get_post_format();
$grit_post_format = empty( $grit_post_format ) ? 'standard' : str_replace( 'post-format-', '', $grit_post_format );
?>
<article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class( 'post_item post_item_container post_layout_excerpt post_format_' . esc_attr( $grit_post_format ) );
	grit_add_blog_animation( $grit_template_args );
	?>
>
	<?php

	// Sticky label
	if ( is_sticky() && ! is_paged() ) {
		?>
		<span class="post_label label_sticky"></span>
		<?php
	}

	// Featured image
	$grit_hover      = ! empty( $grit_template_args['hover'] ) && ! grit_is_inherit( $grit_template_args['hover'] )
							? $grit_template_args['hover']
							: grit_get_theme_option( 'image_hover' );
	$grit_components = ! empty( $grit_template_args['meta_parts'] )
							? ( is_array( $grit_template_args['meta_parts'] )
								? $grit_template_args['meta_parts']
								: array_map( 'trim', explode( ',', $grit_template_args['meta_parts'] ) )
								)
							: grit_array_get_keys_by_value( grit_get_theme_option( 'meta_parts' ) );
	grit_show_post_featured( apply_filters( 'grit_filter_args_featured',
		array(
			'no_links'   => ! empty( $grit_template_args['no_links'] ),
			'hover'      => $grit_hover,
			'meta_parts' => $grit_components,
			'thumb_size' => ! empty( $grit_template_args['thumb_size'] )
							? $grit_template_args['thumb_size']
							: grit_get_thumb_size( strpos( grit_get_theme_option( 'body_style' ), 'full' ) !== false
								? 'full'
								: ( $grit_expanded 
									? 'huge' 
									: 'big' 
									)
								),
		),
		'content-excerpt',
		$grit_template_args
	) );

	// Title and post meta
	$grit_show_title = get_the_title() != '';
	$grit_show_meta  = count( $grit_components ) > 0 && ! in_array( $grit_hover, array( 'border', 'pull', 'slide', 'fade', 'info' ) );

	if ( $grit_show_title ) {
		?>
		<div class="post_header entry-header">
			<?php
			// Post title
			if ( apply_filters( 'grit_filter_show_blog_title', true, 'excerpt' ) ) {
				do_action( 'grit_action_before_post_title' );
				if ( empty( $grit_template_args['no_links'] ) ) {
					the_title( sprintf( '<h3 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' );
				} else {
					the_title( '<h3 class="post_title entry-title">', '</h3>' );
				}
				do_action( 'grit_action_after_post_title' );
			}
			?>
		</div><!-- .post_header -->
		<?php
	}

	// Post content
	if ( apply_filters( 'grit_filter_show_blog_excerpt', empty( $grit_template_args['hide_excerpt'] ) && grit_get_theme_option( 'excerpt_length' ) > 0, 'excerpt' ) ) {
		?>
		<div class="post_content entry-content">
			<?php

			// Post meta
			if ( apply_filters( 'grit_filter_show_blog_meta', $grit_show_meta, $grit_components, 'excerpt' ) ) {
				if ( count( $grit_components ) > 0 ) {
					do_action( 'grit_action_before_post_meta' );
					grit_show_post_meta(
						apply_filters(
							'grit_filter_post_meta_args', array(
								'components' => join( ',', $grit_components ),
								'seo'        => false,
								'echo'       => true,
							), 'excerpt', 1
						)
					);
					do_action( 'grit_action_after_post_meta' );
				}
			}

			if ( grit_get_theme_option( 'blog_content' ) == 'fullpost' ) {
				// Post content area
				?>
				<div class="post_content_inner">
					<?php
					do_action( 'grit_action_before_full_post_content' );
					the_content( '' );
					do_action( 'grit_action_after_full_post_content' );
					?>
				</div>
				<?php
				// Inner pages
				wp_link_pages(
					array(
						'before'      => '<div class="page_links"><span class="page_links_title">' . esc_html__( 'Pages:', 'grit' ) . '</span>',
						'after'       => '</div>',
						'link_before' => '<span>',
						'link_after'  => '</span>',
						'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'grit' ) . ' </span>%',
						'separator'   => '<span class="screen-reader-text">, </span>',
					)
				);
			} else {
				// Post content area
				grit_show_post_content( $grit_template_args, '<div class="post_content_inner">', '</div>' );
			}

			// More button
			if ( apply_filters( 'grit_filter_show_blog_readmore',  ! isset( $grit_template_args['more_button'] ) || ! empty( $grit_template_args['more_button'] ), 'excerpt' ) ) {
				if ( empty( $grit_template_args['no_links'] ) ) {
					do_action( 'grit_action_before_post_readmore' );
					if ( grit_get_theme_option( 'blog_content' ) != 'fullpost' ) {
						grit_show_post_more_link( $grit_template_args, '<p>', '</p>' );
					} else {
						grit_show_post_comments_link( $grit_template_args, '<p>', '</p>' );
					}
					do_action( 'grit_action_after_post_readmore' );
				}
			}

			?>
		</div><!-- .entry-content -->
		<?php
	}
	?>
</article>
<?php

if ( is_array( $grit_template_args ) ) {
	if ( ! empty( $grit_template_args['slider'] ) || $grit_columns > 1 ) {
		?>
		</div>
		<?php
	}
}
