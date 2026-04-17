<?php
/**
 * The template to display the copyright info in the footer
 *
 * @package GRIT
 * @since GRIT 1.0.10
 */

// Copyright area
?> 
<div class="footer_copyright_wrap
<?php
$grit_copyright_scheme = grit_get_theme_option( 'copyright_scheme' );
if ( ! empty( $grit_copyright_scheme ) && ! grit_is_inherit( $grit_copyright_scheme  ) ) {
	echo ' scheme_' . esc_attr( $grit_copyright_scheme );
}
?>
				">
	<div class="footer_copyright_inner">
		<div class="content_wrap">
			<div class="copyright_text">
			<?php
				$grit_copyright = grit_get_theme_option( 'copyright' );
			if ( ! empty( $grit_copyright ) ) {
				// Replace {{Y}} or {Y} with the current year
				$grit_copyright = str_replace( array( '{{Y}}', '{Y}' ), date( 'Y' ), $grit_copyright );
				// Replace {{...}} and ((...)) on the <i>...</i> and <b>...</b>
				$grit_copyright = grit_prepare_macros( $grit_copyright );
				// Display copyright
				echo wp_kses( nl2br( $grit_copyright ), 'grit_kses_content' );
			}
			?>
			</div>
		</div>
	</div>
</div>
