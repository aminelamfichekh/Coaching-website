<?php
/**
 * The template to display Admin notices
 *
 * @package GRIT
 * @since GRIT 1.0.64
 */

$grit_skins_url  = get_admin_url( null, 'admin.php?page=trx_addons_theme_panel#trx_addons_theme_panel_section_skins' );
$grit_skins_args = get_query_var( 'grit_skins_notice_args' );
?>
<div class="grit_admin_notice grit_skins_notice notice notice-info is-dismissible" data-notice="skins">
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
		<?php esc_html_e( 'New skins are available', 'grit' ); ?>
	</h3>
	<?php

	// Description
	$grit_total      = $grit_skins_args['update'];	// Store value to the separate variable to avoid warnings from ThemeCheck plugin!
	$grit_skins_msg  = $grit_total > 0
							// Translators: Add new skins number
							? '<strong>' . sprintf( _n( '%d new version', '%d new versions', $grit_total, 'grit' ), $grit_total ) . '</strong>'
							: '';
	$grit_total      = $grit_skins_args['free'];
	$grit_skins_msg .= $grit_total > 0
							? ( ! empty( $grit_skins_msg ) ? ' ' . esc_html__( 'and', 'grit' ) . ' ' : '' )
								// Translators: Add new skins number
								. '<strong>' . sprintf( _n( '%d free skin', '%d free skins', $grit_total, 'grit' ), $grit_total ) . '</strong>'
							: '';
	$grit_total      = $grit_skins_args['pay'];
	$grit_skins_msg .= $grit_skins_args['pay'] > 0
							? ( ! empty( $grit_skins_msg ) ? ' ' . esc_html__( 'and', 'grit' ) . ' ' : '' )
								// Translators: Add new skins number
								. '<strong>' . sprintf( _n( '%d paid skin', '%d paid skins', $grit_total, 'grit' ), $grit_total ) . '</strong>'
							: '';
	?>
	<div class="grit_notice_text">
		<p>
			<?php
			// Translators: Add new skins info
			echo wp_kses_data( sprintf( __( "We are pleased to announce that %s are available for your theme", 'grit' ), $grit_skins_msg ) );
			?>
		</p>
	</div>
	<?php

	// Buttons
	?>
	<div class="grit_notice_buttons">
		<?php
		// Link to the theme dashboard page
		?>
		<a href="<?php echo esc_url( $grit_skins_url ); ?>" class="button button-primary"><i class="dashicons dashicons-update"></i> 
			<?php
			// Translators: Add theme name
			esc_html_e( 'Go to Skins manager', 'grit' );
			?>
		</a>
	</div>
</div>
