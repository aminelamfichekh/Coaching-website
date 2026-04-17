<?php
/**
 * The Classic template to display the content
 *
 * Used for index/archive/search.
 *
 * @package GRIT
 * @since GRIT 1.0
 */

$grit_template_args = get_query_var( 'grit_template_args' );

if ( is_array( $grit_template_args ) ) {
	$grit_columns    = empty( $grit_template_args['columns'] ) ? 2 : max( 1, $grit_template_args['columns'] );
	$grit_blog_style = array( $grit_template_args['type'], $grit_columns );
    $grit_columns_class = grit_get_column_class( 1, $grit_columns, ! empty( $grit_template_args['columns_tablet']) ? $grit_template_args['columns_tablet'] : '', ! empty($grit_template_args['columns_mobile']) ? $grit_template_args['columns_mobile'] : '' );
} else {
	$grit_template_args = array();
	$grit_blog_style = explode( '_', grit_get_theme_option( 'blog_style' ) );
	$grit_columns    = empty( $grit_blog_style[1] ) ? 2 : max( 1, $grit_blog_style[1] );
    $grit_columns_class = grit_get_column_class( 1, $grit_columns );
}
$grit_expanded   = ! grit_sidebar_present() && grit_get_theme_option( 'expand_content' ) == 'expand';

$grit_post_format = get_post_format();
$grit_post_format = empty( $grit_post_format ) ? 'standard' : str_replace( 'post-format-', '', $grit_post_format );

?><div class="<?php
	if ( ! empty( $grit_template_args['slider'] ) ) {
		echo ' slider-slide swiper-slide';
	} else {
		echo ( grit_is_blog_style_use_masonry( $grit_blog_style[0] ) ? 'masonry_item masonry_item-1_' . esc_attr( $grit_columns ) : esc_attr( $grit_columns_class ) );
	}
?>"><article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class(
		'post_item post_item_container post_format_' . esc_attr( $grit_post_format )
				. ' post_layout_classic post_layout_classic_' . esc_attr( $grit_columns )
				. ' post_layout_' . esc_attr( $grit_blog_style[0] )
				. ' post_layout_' . esc_attr( $grit_blog_style[0] ) . '_' . esc_attr( $grit_columns )
	);
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
								: explode( ',', $grit_template_args['meta_parts'] )
								)
							: grit_array_get_keys_by_value( grit_get_theme_option( 'meta_parts' ) );

	grit_show_post_featured( apply_filters( 'grit_filter_args_featured',
		array(
			'thumb_size' => ! empty( $grit_template_args['thumb_size'] )
				? $grit_template_args['thumb_size']
				: grit_get_thumb_size(
				'classic' == $grit_blog_style[0]
						? ( strpos( grit_get_theme_option( 'body_style' ), 'full' ) !== false
								? ( $grit_columns > 2 ? 'big' : 'huge' )
								: ( $grit_columns > 2
									? ( $grit_expanded ? 'square' : 'square' )
									: ($grit_columns > 1 ? 'square' : ( $grit_expanded ? 'huge' : 'big' ))
									)
							)
						: ( strpos( grit_get_theme_option( 'body_style' ), 'full' ) !== false
								? ( $grit_columns > 2 ? 'masonry-big' : 'full' )
								: ($grit_columns === 1 ? ( $grit_expanded ? 'huge' : 'big' ) : ( $grit_columns <= 2 && $grit_expanded ? 'masonry-big' : 'masonry' ))
							)
			),
			'hover'      => $grit_hover,
			'meta_parts' => $grit_components,
			'no_links'   => ! empty( $grit_template_args['no_links'] ),
        ),
        'content-classic',
        $grit_template_args
    ) );

	// Title and post meta
	$grit_show_title = get_the_title() != '';
	$grit_show_meta  = count( $grit_components ) > 0 && ! in_array( $grit_hover, array( 'border', 'pull', 'slide', 'fade', 'info' ) );

	if ( $grit_show_title ) {
		?>
		<div class="post_header entry-header">
			<?php

			// Post meta
			if ( apply_filters( 'grit_filter_show_blog_meta', $grit_show_meta, $grit_components, 'classic' ) ) {
				if ( count( $grit_components ) > 0 ) {
					do_action( 'grit_action_before_post_meta' );
					grit_show_post_meta(
						apply_filters(
							'grit_filter_post_meta_args', array(
							'components' => join( ',', $grit_components ),
							'seo'        => false,
							'echo'       => true,
						), $grit_blog_style[0], $grit_columns
						)
					);
					do_action( 'grit_action_after_post_meta' );
				}
			}

			// Post title
			if ( apply_filters( 'grit_filter_show_blog_title', true, 'classic' ) ) {
				do_action( 'grit_action_before_post_title' );
				if ( empty( $grit_template_args['no_links'] ) ) {
					the_title( sprintf( '<h4 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h4>' );
				} else {
					the_title( '<h4 class="post_title entry-title">', '</h4>' );
				}
				do_action( 'grit_action_after_post_title' );
			}

			if( !in_array( $grit_post_format, array( 'quote', 'aside', 'link', 'status' ) ) ) {
				// More button
				if ( apply_filters( 'grit_filter_show_blog_readmore', ! $grit_show_title || ! empty( $grit_template_args['more_button'] ), 'classic' ) ) {
					if ( empty( $grit_template_args['no_links'] ) ) {
						do_action( 'grit_action_before_post_readmore' );
						grit_show_post_more_link( $grit_template_args, '<div class="more-wrap">', '</div>' );
						do_action( 'grit_action_after_post_readmore' );
					}
				}
			}
			?>
		</div><!-- .entry-header -->
		<?php
	}

	// Post content
	if( in_array( $grit_post_format, array( 'quote', 'aside', 'link', 'status' ) ) ) {
		ob_start();
		if (apply_filters('grit_filter_show_blog_excerpt', empty($grit_template_args['hide_excerpt']) && grit_get_theme_option('excerpt_length') > 0, 'classic')) {
			grit_show_post_content($grit_template_args, '<div class="post_content_inner">', '</div>');
		}
		// More button
		if(! empty( $grit_template_args['more_button'] )) {
			if ( empty( $grit_template_args['no_links'] ) ) {
				do_action( 'grit_action_before_post_readmore' );
				grit_show_post_more_link( $grit_template_args, '<div class="more-wrap">', '</div>' );
				do_action( 'grit_action_after_post_readmore' );
			}
		}
		$grit_content = ob_get_contents();
		ob_end_clean();
		grit_show_layout($grit_content, '<div class="post_content entry-content">', '</div><!-- .entry-content -->');
	}
	?>

</article></div><?php
// Need opening PHP-tag above, because <div> is a inline-block element (used as column)!
