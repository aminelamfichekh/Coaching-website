<?php
/* The GDPR Framework support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'grit_gdpr_framework_feed_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'grit_gdpr_framework_theme_setup9', 9 );
	function grit_gdpr_framework_theme_setup9() {
		if ( is_admin() ) {
			add_filter( 'grit_filter_tgmpa_required_plugins', 'grit_gdpr_framework_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'grit_gdpr_framework_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('grit_filter_tgmpa_required_plugins',	'grit_gdpr_framework_tgmpa_required_plugins');
	function grit_gdpr_framework_tgmpa_required_plugins( $list = array() ) {
		if ( grit_storage_isset( 'required_plugins', 'gdpr-framework' ) && grit_storage_get_array( 'required_plugins', 'gdpr-framework', 'install' ) !== false ) {
			$list[] = array(
				'name'     => grit_storage_get_array( 'required_plugins', 'gdpr-framework', 'title' ),
				'slug'     => 'gdpr-framework',
				'required' => false,
			);
		}
		return $list;
	}
}

// Check if this plugin installed and activated
if ( ! function_exists( 'grit_exists_gdpr_framework' ) ) {
	function grit_exists_gdpr_framework() {
		return defined( 'GDPR_FRAMEWORK_VERSION' );
	}
}
