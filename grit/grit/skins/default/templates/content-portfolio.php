<?php
/**
 * The Portfolio template to display the content
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

$grit_post_format = get_post_format();
$grit_post_format = empty( $grit_post_format ) ? 'standard' : str_replace( 'post-format-', '', $grit_post_format );

?><div class="
<?php
if ( ! empty( $grit_template_args['slider'] ) ) {
	echo ' slider-slide swiper-slide';
} else {
	echo ( grit_is_blog_style_use_masonry( $grit_blog_style[0] ) ? 'masonry_item masonry_item-1_' . esc_attr( $grit_columns ) : esc_attr( $grit_columns_class ));
}
?>
"><article id="post-<?php the_ID(); ?>" 
	<?php
	post_class(
		'post_item post_item_container post_format_' . esc_attr( $grit_post_format )
		. ' post_layout_portfolio'
		. ' post_layout_portfolio_' . esc_attr( $grit_columns )
		. ( 'portfolio' != $grit_blog_style[0] ? ' ' . esc_attr( $grit_blog_style[0] )  . '_' . esc_attr( $grit_columns ) : '' )
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

	$grit_hover   = ! empty( $grit_template_args['hover'] ) && ! grit_is_inherit( $grit_template_args['hover'] )
								? $grit_template_args['hover']
								: grit_get_theme_option( 'image_hover' );

	if ( 'dots' == $grit_hover ) {
		$grit_post_link = empty( $grit_template_args['no_links'] )
								? ( ! empty( $grit_template_args['link'] )
									? $grit_template_args['link']
									: get_permalink()
									)
								: '';
		$grit_target    = ! empty( $grit_post_link ) && false === strpos( $grit_post_link, home_url() )
								? ' target="_blank" rel="nofollow"'
								: '';
	}
	
	// Meta parts
	$grit_components = ! empty( $grit_template_args['meta_parts'] )
							? ( is_array( $grit_template_args['meta_parts'] )
								? $grit_template_args['meta_parts']
								: explode( ',', $grit_template_args['meta_parts'] )
								)
							: grit_array_get_keys_by_value( grit_get_theme_option( 'meta_parts' ) );

	// Featured image
	grit_show_post_featured( apply_filters( 'grit_filter_args_featured',
		array(
			'hover'         => $grit_hover,
			'no_links'      => ! empty( $grit_template_args['no_links'] ),
			'thumb_size'    => ! empty( $grit_template_args['thumb_size'] )
								? $grit_template_args['thumb_size']
								: grit_get_thumb_size(
									grit_is_blog_style_use_masonry( $grit_blog_style[0] )
										? (	strpos( grit_get_theme_option( 'body_style' ), 'full' ) !== false || $grit_columns < 3
											? 'masonry-big'
											: 'masonry'
											)
										: (	strpos( grit_get_theme_option( 'body_style' ), 'full' ) !== false || $grit_columns < 3
											? 'square'
											: 'square'
											)
								),
			'thumb_bg' => grit_is_blog_style_use_masonry( $grit_blog_style[0] ) ? false : true,
			'show_no_image' => true,
			'meta_parts'    => $grit_components,
			'class'         => 'dots' == $grit_hover ? 'hover_with_info' : '',
			'post_info'     => 'dots' == $grit_hover
										? '<div class="post_info"><h5 class="post_title">'
											. ( ! empty( $grit_post_link )
												? '<a href="' . esc_url( $grit_post_link ) . '"' . ( ! empty( $target ) ? $target : '' ) . '>'
												: ''
												)
												. esc_html( get_the_title() ) 
											. ( ! empty( $grit_post_link )
												? '</a>'
												: ''
												)
											. '</h5></div>'
										: '',
            'thumb_ratio'   => 'info' == $grit_hover ?  '100:102' : '',
        ),
        'content-portfolio',
        $grit_template_args
    ) );
	?>
</article></div><?php
// Need opening PHP-tag above, because <article> is a inline-block element (used as column)!