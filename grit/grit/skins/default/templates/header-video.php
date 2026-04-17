<?php
/**
 * The template to display the background video in the header
 *
 * @package GRIT
 * @since GRIT 1.0.14
 */
$grit_header_video = grit_get_header_video();
$grit_embed_video  = '';
if ( ! empty( $grit_header_video ) && ! grit_is_from_uploads( $grit_header_video ) ) {
	if ( grit_is_youtube_url( $grit_header_video ) && preg_match( '/[=\/]([^=\/]*)$/', $grit_header_video, $matches ) && ! empty( $matches[1] ) ) {
		?><div id="background_video" data-youtube-code="<?php echo esc_attr( $matches[1] ); ?>"></div>
		<?php
	} else {
		?>
		<div id="background_video"><?php grit_show_layout( grit_get_embed_video( $grit_header_video ) ); ?></div>
		<?php
	}
}
