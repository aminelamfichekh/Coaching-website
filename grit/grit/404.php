<?php
/**
 * The template to display the 404 page
 *
 * @package GRIT
 * @since GRIT 1.0
 */

get_header();

get_template_part( apply_filters( 'grit_filter_get_template_part', 'templates/content', '404' ), '404' );

get_footer();
