<?php
/**
 * The custom template to display the content
 *
 * Used for index/archive/search.
 *
 * @package GRIT
 * @since GRIT 1.0.50
 */

$grit_template_args = get_query_var( 'grit_template_args' );
if ( is_array( $grit_template_args ) ) {
	$grit_columns    = empty( $grit_template_args['columns'] ) ? 2 : max( 1, $grit_template_args['columns'] );
	$grit_blog_style = array( $grit_template_args['type'], $grit_columns );
} else {
	$grit_template_args = array();
	$grit_blog_style = explode( '_', grit_get_theme_option( 'blog_style' ) );
	$grit_columns    = empty( $grit_blog_style[1] ) ? 2 : max( 1, $grit_blog_style[1] );
}
$grit_blog_id       = grit_get_custom_blog_id( join( '_', $grit_blog_style ) );
$grit_blog_style[0] = str_replace( 'blog-custom-', '', $grit_blog_style[0] );
$grit_expanded      = ! grit_sidebar_present() && grit_get_theme_option( 'expand_content' ) == 'expand';
$grit_components    = ! empty( $grit_template_args['meta_parts'] )
							? ( is_array( $grit_template_args['meta_parts'] )
								? join( ',', $grit_template_args['meta_parts'] )
								: $grit_template_args['meta_parts']
								)
							: grit_array_get_keys_by_value( grit_get_theme_option( 'meta_parts' ) );
$grit_post_format   = get_post_format();
$grit_post_format   = empty( $grit_post_format ) ? 'standard' : str_replace( 'post-format-', '', $grit_post_format );

$grit_blog_meta     = grit_get_custom_layout_meta( $grit_blog_id );
$grit_custom_style  = ! empty( $grit_blog_meta['scripts_required'] ) ? $grit_blog_meta['scripts_required'] : 'none';

if ( ! empty( $grit_template_args['slider'] ) || $grit_columns > 1 || ! grit_is_off( $grit_custom_style ) ) {
	?><div class="
		<?php
		if ( ! empty( $grit_template_args['slider'] ) ) {
			echo 'slider-slide swiper-slide';
		} else {
			echo esc_attr( ( grit_is_off( $grit_custom_style ) ? 'column' : sprintf( '%1$s_item %1$s_item', $grit_custom_style ) ) . "-1_{$grit_columns}" );
		}
		?>
	">
	<?php
}
?>
<article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class(
			'post_item post_item_container post_format_' . esc_attr( $grit_post_format )
					. ' post_layout_custom post_layout_custom_' . esc_attr( $grit_columns )
					. ' post_layout_' . esc_attr( $grit_blog_style[0] )
					. ' post_layout_' . esc_attr( $grit_blog_style[0] ) . '_' . esc_attr( $grit_columns )
					. ( ! grit_is_off( $grit_custom_style )
						? ' post_layout_' . esc_attr( $grit_custom_style )
							. ' post_layout_' . esc_attr( $grit_custom_style ) . '_' . esc_attr( $grit_columns )
						: ''
						)
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
	// Custom layout
	do_action( 'grit_action_show_layout', $grit_blog_id, get_the_ID() );
	?>
</article><?php
if ( ! empty( $grit_template_args['slider'] ) || $grit_columns > 1 || ! grit_is_off( $grit_custom_style ) ) {
	?></div><?php
	// Need opening PHP-tag above just after </div>, because <div> is a inline-block element (used as column)!
}
