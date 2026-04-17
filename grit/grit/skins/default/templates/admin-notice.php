<?php
/**
 * The template to display Admin notices
 *
 * @package GRIT
 * @since GRIT 1.0.1
 */

$grit_theme_slug = get_option( 'template' );
$grit_theme_obj  = wp_get_theme( $grit_theme_slug );
?>
<div class="grit_admin_notice grit_welcome_notice notice notice-info is-dismissible" data-notice="admin">
	<?php
	// Theme image
	$grit_theme_img = grit_get_file_url( 'screenshot.jpg' );
	if ( '' != $grit_theme_img ) {
		?>
		<div class="grit_notice_image"><img src="<?php echo esc_url( $grit_theme_img ); ?>" alt="<?php esc_attr_e( 'Theme screenshot', 'grit' ); ?>"></div>
		<?php
	}

	// Title
	?>
	<h3 class="grit_notice_title">
		<?php
		echo esc_html(
			sprintf(
				// Translators: Add theme name and version to the 'Welcome' message
				__( 'Welcome to %1$s v.%2$s', 'grit' ),
				$grit_theme_obj->get( 'Name' ) . ( GRIT_THEME_FREE ? ' ' . __( 'Free', 'grit' ) : '' ),
				$grit_theme_obj->get( 'Version' )
			)
		);
		?>
	</h3>
	<?php

	// Description
	?>
	<div class="grit_notice_text">
		<p class="grit_notice_text_description">
			<?php
			echo str_replace( '. ', '.<br>', wp_kses_data( $grit_theme_obj->description ) );
			?>
		</p>
		<p class="grit_notice_text_info">
			<?php
			echo wp_kses_data( __( 'Attention! Plugin "ThemeREX Addons" is required! Please, install and activate it!', 'grit' ) );
			?>
		</p>
	</div>
	<?php

	// Buttons
	?>
	<div class="grit_notice_buttons">
		<?php
		// Link to the page 'About Theme'
		?>
		<a href="<?php echo esc_url( admin_url() . 'themes.php?page=grit_about' ); ?>" class="button button-primary"><i class="dashicons dashicons-nametag"></i> 
			<?php
			echo esc_html__( 'Install plugin "ThemeREX Addons"', 'grit' );
			?>
		</a>
	</div>
</div>
