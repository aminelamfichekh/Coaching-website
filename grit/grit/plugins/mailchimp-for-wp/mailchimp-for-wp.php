<?php
/* Mail Chimp support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'grit_mailchimp_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'grit_mailchimp_theme_setup9', 9 );
	function grit_mailchimp_theme_setup9() {
		if ( grit_exists_mailchimp() ) {
			add_action( 'wp_enqueue_scripts', 'grit_mailchimp_frontend_scripts', 1100 );
			add_action( 'trx_addons_action_load_scripts_front_mailchimp', 'grit_mailchimp_frontend_scripts', 10, 1 );
			add_filter( 'grit_filter_merge_styles', 'grit_mailchimp_merge_styles' );
		}
		if ( is_admin() ) {
			add_filter( 'grit_filter_tgmpa_required_plugins', 'grit_mailchimp_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'grit_mailchimp_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('grit_filter_tgmpa_required_plugins',	'grit_mailchimp_tgmpa_required_plugins');
	function grit_mailchimp_tgmpa_required_plugins( $list = array() ) {
		if ( grit_storage_isset( 'required_plugins', 'mailchimp-for-wp' ) && grit_storage_get_array( 'required_plugins', 'mailchimp-for-wp', 'install' ) !== false ) {
			$list[] = array(
				'name'     => grit_storage_get_array( 'required_plugins', 'mailchimp-for-wp', 'title' ),
				'slug'     => 'mailchimp-for-wp',
				'required' => false,
			);
		}
		return $list;
	}
}

// Check if plugin installed and activated
if ( ! function_exists( 'grit_exists_mailchimp' ) ) {
	function grit_exists_mailchimp() {
		return function_exists( '__mc4wp_load_plugin' ) || defined( 'MC4WP_VERSION' );
	}
}



// Custom styles and scripts
//------------------------------------------------------------------------

// Enqueue styles for frontend
if ( ! function_exists( 'grit_mailchimp_frontend_scripts' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'grit_mailchimp_frontend_scripts', 1100 );
	//Handler of the add_action( 'trx_addons_action_load_scripts_front_mailchimp', 'grit_mailchimp_frontend_scripts', 10, 1 );
	function grit_mailchimp_frontend_scripts( $force = false ) {
		grit_enqueue_optimized( 'mailchimp', $force, array(
			'css' => array(
				'grit-mailchimp-for-wp' => array( 'src' => 'plugins/mailchimp-for-wp/mailchimp-for-wp.css' ),
			)
		) );
	}
}

// Merge custom styles
if ( ! function_exists( 'grit_mailchimp_merge_styles' ) ) {
	//Handler of the add_filter( 'grit_filter_merge_styles', 'grit_mailchimp_merge_styles');
	function grit_mailchimp_merge_styles( $list ) {
		$list[ 'plugins/mailchimp-for-wp/mailchimp-for-wp.css' ] = false;
		return $list;
	}
}


// Add plugin-specific colors and fonts to the custom CSS
if ( grit_exists_mailchimp() ) {
	$grit_fdir = grit_get_file_dir( 'plugins/mailchimp-for-wp/mailchimp-for-wp-style.php' );
	if ( ! empty( $grit_fdir ) ) {
		require_once $grit_fdir;
	}
}

