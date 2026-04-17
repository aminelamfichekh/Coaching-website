<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: //codex.wordpress.org/Template_Hierarchy
 *
 * @package GRIT
 * @since GRIT 1.0
 */

$grit_template = apply_filters( 'grit_filter_get_template_part', grit_blog_archive_get_template() );

if ( ! empty( $grit_template ) && 'index' != $grit_template ) {

	get_template_part( $grit_template );

} else {

	grit_storage_set( 'blog_archive', true );

	get_header();

	if ( have_posts() ) {

		// Query params
		$grit_stickies   = is_home()
								|| ( in_array( grit_get_theme_option( 'post_type' ), array( '', 'post' ) )
									&& (int) grit_get_theme_option( 'parent_cat' ) == 0
									)
										? get_option( 'sticky_posts' )
										: false;
		$grit_post_type  = grit_get_theme_option( 'post_type' );
		$grit_args       = array(
								'blog_style'     => grit_get_theme_option( 'blog_style' ),
								'post_type'      => $grit_post_type,
								'taxonomy'       => grit_get_post_type_taxonomy( $grit_post_type ),
								'parent_cat'     => grit_get_theme_option( 'parent_cat' ),
								'posts_per_page' => grit_get_theme_option( 'posts_per_page' ),
								'sticky'         => grit_get_theme_option( 'sticky_style' ) == 'columns'
															&& is_array( $grit_stickies )
															&& count( $grit_stickies ) > 0
															&& get_query_var( 'paged' ) < 1
								);

		grit_blog_archive_start();

		do_action( 'grit_action_blog_archive_start' );

		if ( is_author() ) {
			do_action( 'grit_action_before_page_author' );
			get_template_part( apply_filters( 'grit_filter_get_template_part', 'templates/author-page' ) );
			do_action( 'grit_action_after_page_author' );
		}

		if ( grit_get_theme_option( 'show_filters' ) ) {
			do_action( 'grit_action_before_page_filters' );
			grit_show_filters( $grit_args );
			do_action( 'grit_action_after_page_filters' );
		} else {
			do_action( 'grit_action_before_page_posts' );
			grit_show_posts( array_merge( $grit_args, array( 'cat' => $grit_args['parent_cat'] ) ) );
			do_action( 'grit_action_after_page_posts' );
		}

		do_action( 'grit_action_blog_archive_end' );

		grit_blog_archive_end();

	} else {

		if ( is_search() ) {
			get_template_part( apply_filters( 'grit_filter_get_template_part', 'templates/content', 'none-search' ), 'none-search' );
		} else {
			get_template_part( apply_filters( 'grit_filter_get_template_part', 'templates/content', 'none-archive' ), 'none-archive' );
		}
	}

	get_footer();
}
