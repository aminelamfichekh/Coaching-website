<?php
/**
 * The template to display single post
 *
 * @package GRIT
 * @since GRIT 1.0
 */

// Full post loading
$full_post_loading          = grit_get_value_gp( 'action' ) == 'full_post_loading';

// Prev post loading
$prev_post_loading          = grit_get_value_gp( 'action' ) == 'prev_post_loading';
$prev_post_loading_type     = grit_get_theme_option( 'posts_navigation_scroll_which_block' );

// Position of the related posts
$grit_related_position   = grit_get_theme_option( 'related_position' );

// Type of the prev/next post navigation
$grit_posts_navigation   = grit_get_theme_option( 'posts_navigation' );
$grit_prev_post          = false;
$grit_prev_post_same_cat = grit_get_theme_option( 'posts_navigation_scroll_same_cat' );

// Rewrite style of the single post if current post loading via AJAX and featured image and title is not in the content
if ( ( $full_post_loading 
		|| 
		( $prev_post_loading && 'article' == $prev_post_loading_type )
	) 
	&& 
	! in_array( grit_get_theme_option( 'single_style' ), array( 'style-6' ) )
) {
	grit_storage_set_array( 'options_meta', 'single_style', 'style-6' );
}

do_action( 'grit_action_prev_post_loading', $prev_post_loading, $prev_post_loading_type );

get_header();

while ( have_posts() ) {

	the_post();

	// Type of the prev/next post navigation
	if ( 'scroll' == $grit_posts_navigation ) {
		$grit_prev_post = get_previous_post( $grit_prev_post_same_cat );  // Get post from same category
		if ( ! $grit_prev_post && $grit_prev_post_same_cat ) {
			$grit_prev_post = get_previous_post( false );                    // Get post from any category
		}
		if ( ! $grit_prev_post ) {
			$grit_posts_navigation = 'links';
		}
	}

	// Override some theme options to display featured image, title and post meta in the dynamic loaded posts
	if ( $full_post_loading || ( $prev_post_loading && $grit_prev_post ) ) {
		grit_sc_layouts_showed( 'featured', false );
		grit_sc_layouts_showed( 'title', false );
		grit_sc_layouts_showed( 'postmeta', false );
	}

	// If related posts should be inside the content
	if ( strpos( $grit_related_position, 'inside' ) === 0 ) {
		ob_start();
	}

	// Display post's content
	get_template_part( apply_filters( 'grit_filter_get_template_part', 'templates/content', 'single-' . grit_get_theme_option( 'single_style' ) ), 'single-' . grit_get_theme_option( 'single_style' ) );

	// If related posts should be inside the content
	if ( strpos( $grit_related_position, 'inside' ) === 0 ) {
		$grit_content = ob_get_contents();
		ob_end_clean();

		ob_start();
		do_action( 'grit_action_related_posts' );
		$grit_related_content = ob_get_contents();
		ob_end_clean();

		if ( ! empty( $grit_related_content ) ) {
			$grit_related_position_inside = max( 0, min( 9, grit_get_theme_option( 'related_position_inside' ) ) );
			if ( 0 == $grit_related_position_inside ) {
				$grit_related_position_inside = mt_rand( 1, 9 );
			}

			$grit_p_number         = 0;
			$grit_related_inserted = false;
			$grit_in_block         = false;
			$grit_content_start    = strpos( $grit_content, '<div class="post_content' );
			$grit_content_end      = strrpos( $grit_content, '</div>' );

			for ( $i = max( 0, $grit_content_start ); $i < min( strlen( $grit_content ) - 3, $grit_content_end ); $i++ ) {
				if ( $grit_content[ $i ] != '<' ) {
					continue;
				}
				if ( $grit_in_block ) {
					if ( strtolower( substr( $grit_content, $i + 1, 12 ) ) == '/blockquote>' ) {
						$grit_in_block = false;
						$i += 12;
					}
					continue;
				} else if ( strtolower( substr( $grit_content, $i + 1, 10 ) ) == 'blockquote' && in_array( $grit_content[ $i + 11 ], array( '>', ' ' ) ) ) {
					$grit_in_block = true;
					$i += 11;
					continue;
				} else if ( 'p' == $grit_content[ $i + 1 ] && in_array( $grit_content[ $i + 2 ], array( '>', ' ' ) ) ) {
					$grit_p_number++;
					if ( $grit_related_position_inside == $grit_p_number ) {
						$grit_related_inserted = true;
						$grit_content = ( $i > 0 ? substr( $grit_content, 0, $i ) : '' )
											. $grit_related_content
											. substr( $grit_content, $i );
					}
				}
			}
			if ( ! $grit_related_inserted ) {
				if ( $grit_content_end > 0 ) {
					$grit_content = substr( $grit_content, 0, $grit_content_end ) . $grit_related_content . substr( $grit_content, $grit_content_end );
				} else {
					$grit_content .= $grit_related_content;
				}
			}
		}

		grit_show_layout( $grit_content );
	}

	// Comments
	do_action( 'grit_action_before_comments' );
	comments_template();
	do_action( 'grit_action_after_comments' );

	// Related posts
	if ( 'below_content' == $grit_related_position
		&& ( 'scroll' != $grit_posts_navigation || grit_get_theme_option( 'posts_navigation_scroll_hide_related' ) == 0 )
		&& ( ! $full_post_loading || grit_get_theme_option( 'open_full_post_hide_related' ) == 0 )
	) {
		do_action( 'grit_action_related_posts' );
	}

	// Post navigation: type 'scroll'
	if ( 'scroll' == $grit_posts_navigation && ! $full_post_loading ) {
		?>
		<div class="nav-links-single-scroll"
			data-post-id="<?php echo esc_attr( get_the_ID( $grit_prev_post ) ); ?>"
			data-post-link="<?php echo esc_attr( get_permalink( $grit_prev_post ) ); ?>"
			data-post-title="<?php the_title_attribute( array( 'post' => $grit_prev_post ) ); ?>"
			<?php do_action( 'grit_action_nav_links_single_scroll_data', $grit_prev_post ); ?>
		></div>
		<?php
	}
}

get_footer();
