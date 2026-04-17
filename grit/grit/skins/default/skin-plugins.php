<?php
/**
 * Required plugins
 *
 * @package GRIT
 * @since GRIT 1.76.0
 */

// THEME-SUPPORTED PLUGINS
// If plugin not need - remove its settings from next array
//----------------------------------------------------------
$grit_theme_required_plugins_groups = array(
	'core'          => esc_html__( 'Core', 'grit' ),
	'page_builders' => esc_html__( 'Page Builders', 'grit' ),
	'ecommerce'     => esc_html__( 'E-Commerce & Donations', 'grit' ),
	'socials'       => esc_html__( 'Socials and Communities', 'grit' ),
	'events'        => esc_html__( 'Events and Appointments', 'grit' ),
	'content'       => esc_html__( 'Content', 'grit' ),
	'other'         => esc_html__( 'Other', 'grit' ),
);
$grit_theme_required_plugins        = array(
	'trx_addons'                 => array(
		'title'       => esc_html__( 'ThemeREX Addons', 'grit' ),
		'description' => esc_html__( "Will allow you to install recommended plugins, demo content, and improve the theme's functionality overall with multiple theme options", 'grit' ),
		'required'    => true,
		'logo'        => 'trx_addons.png',
		'group'       => $grit_theme_required_plugins_groups['core'],
	),
	'elementor'                  => array(
		'title'       => esc_html__( 'Elementor', 'grit' ),
		'description' => esc_html__( "Is a beautiful PageBuilder, even the free version of which allows you to create great pages using a variety of modules.", 'grit' ),
		'required'    => false,
		'logo'        => 'elementor.png',
		'group'       => $grit_theme_required_plugins_groups['page_builders'],
	),
	'gutenberg'                  => array(
		'title'       => esc_html__( 'Gutenberg', 'grit' ),
		'description' => esc_html__( "It's a posts editor coming in place of the classic TinyMCE. Can be installed and used in parallel with Elementor", 'grit' ),
		'required'    => false,
		'install'     => false,          // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
		'logo'        => 'gutenberg.png',
		'group'       => $grit_theme_required_plugins_groups['page_builders'],
	),
	'js_composer'                => array(
		'title'       => esc_html__( 'WPBakery PageBuilder', 'grit' ),
		'description' => esc_html__( "Popular PageBuilder which allows you to create excellent pages", 'grit' ),
		'required'    => false,
		'install'     => false,          // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
		'logo'        => 'js_composer.jpg',
		'group'       => $grit_theme_required_plugins_groups['page_builders'],
	),
	'woocommerce'                => array(
		'title'       => esc_html__( 'WooCommerce', 'grit' ),
		'description' => esc_html__( "Connect the store to your website and start selling now", 'grit' ),
		'required'    => false,
		'logo'        => 'woocommerce.png',
		'group'       => $grit_theme_required_plugins_groups['ecommerce'],
	),
	'elegro-payment'             => array(
		'title'       => esc_html__( 'Elegro Crypto Payment', 'grit' ),
		'description' => esc_html__( "Extends WooCommerce Payment Gateways with an elegro Crypto Payment", 'grit' ),
		'required'    => false,
		'logo'        => 'elegro-payment.png',
		'group'       => $grit_theme_required_plugins_groups['ecommerce'],
	),
	'instagram-feed'             => array(
		'title'       => esc_html__( 'Instagram Feed', 'grit' ),
		'description' => esc_html__( "Displays the latest photos from your profile on Instagram", 'grit' ),
		'required'    => false,
		'logo'        => 'instagram-feed.png',
		'group'       => $grit_theme_required_plugins_groups['socials'],
	),
	'mailchimp-for-wp'           => array(
		'title'       => esc_html__( 'MailChimp for WP', 'grit' ),
		'description' => esc_html__( "Allows visitors to subscribe to newsletters", 'grit' ),
		'required'    => false,
		'logo'        => 'mailchimp-for-wp.png',
		'group'       => $grit_theme_required_plugins_groups['socials'],
	),
	'booked'                     => array(
		'title'       => esc_html__( 'Booked Appointments', 'grit' ),
		'description' => '',
		'required'    => false,
		'install'     => false,
		'logo'        => 'booked.png',
		'group'       => $grit_theme_required_plugins_groups['events'],
	),
	'quickcal'                     => array(
		'title'       => esc_html__( 'QuickCal', 'grit' ),
		'description' => '',
		'required'    => false,
		'logo'        => 'quickcal.png',
		'group'       => $grit_theme_required_plugins_groups['events'],
	),
	'the-events-calendar'        => array(
		'title'       => esc_html__( 'The Events Calendar', 'grit' ),
		'description' => '',
		'required'    => false,
        	'install'     => false,
		'logo'        => 'the-events-calendar.png',
		'group'       => $grit_theme_required_plugins_groups['events'],
	),
	'contact-form-7'             => array(
		'title'       => esc_html__( 'Contact Form 7', 'grit' ),
		'description' => esc_html__( "CF7 allows you to create an unlimited number of contact forms", 'grit' ),
		'required'    => false,
		'logo'        => 'contact-form-7.png',
		'group'       => $grit_theme_required_plugins_groups['content'],
	),

	'latepoint'                  => array(
		'title'       => esc_html__( 'LatePoint', 'grit' ),
		'description' => '',
		'required'    => false,
        	'install'     => false,
		'logo'        => grit_get_file_url( 'plugins/latepoint/latepoint.png' ),
		'group'       => $grit_theme_required_plugins_groups['events'],
	),
	'advanced-popups'                  => array(
		'title'       => esc_html__( 'Advanced Popups', 'grit' ),
		'description' => '',
		'required'    => false,
		'logo'        => grit_get_file_url( 'plugins/advanced-popups/advanced-popups.jpg' ),
		'group'       => $grit_theme_required_plugins_groups['content'],
	),
	'devvn-image-hotspot'                  => array(
		'title'       => esc_html__( 'Image Hotspot by DevVN', 'grit' ),
		'description' => '',
		'required'    => false,
        	'install'     => false,
		'logo'        => grit_get_file_url( 'plugins/devvn-image-hotspot/devvn-image-hotspot.png' ),
		'group'       => $grit_theme_required_plugins_groups['content'],
	),
	'ti-woocommerce-wishlist'                  => array(
		'title'       => esc_html__( 'TI WooCommerce Wishlist', 'grit' ),
		'description' => '',
		'required'    => false,
		'logo'        => grit_get_file_url( 'plugins/ti-woocommerce-wishlist/ti-woocommerce-wishlist.png' ),
		'group'       => $grit_theme_required_plugins_groups['ecommerce'],
	),
	'woo-smart-quick-view'                  => array(
		'title'       => esc_html__( 'WPC Smart Quick View for WooCommerce', 'grit' ),
		'description' => '',
		'required'    => false,
         	'install'     => false,
		'logo'        => grit_get_file_url( 'plugins/woo-smart-quick-view/woo-smart-quick-view.png' ),
		'group'       => $grit_theme_required_plugins_groups['ecommerce'],
	),
	'twenty20'                  => array(
		'title'       => esc_html__( 'Twenty20 Image Before-After', 'grit' ),
		'description' => '',
		'required'    => false,
        	'install'     => false,
		'logo'        => grit_get_file_url( 'plugins/twenty20/twenty20.png' ),
		'group'       => $grit_theme_required_plugins_groups['content'],
	),
	'essential-grid'             => array(
		'title'       => esc_html__( 'Essential Grid', 'grit' ),
		'description' => '',
		'required'    => false,
		'install'     => false,
		'logo'        => 'essential-grid.png',
		'group'       => $grit_theme_required_plugins_groups['content'],
	),
	'revslider'                  => array(
		'title'       => esc_html__( 'Revolution Slider', 'grit' ),
		'description' => '',
		'required'    => false,
		'logo'        => 'revslider.png',
		'group'       => $grit_theme_required_plugins_groups['content'],
	),
	'sitepress-multilingual-cms' => array(
		'title'       => esc_html__( 'WPML - Sitepress Multilingual CMS', 'grit' ),
		'description' => esc_html__( "Allows you to make your website multilingual", 'grit' ),
		'required'    => false,
		'install'     => false,      // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
		'logo'        => 'sitepress-multilingual-cms.png',
		'group'       => $grit_theme_required_plugins_groups['content'],
	),
	'wp-gdpr-compliance'         => array(
		'title'       => esc_html__( 'Cookie Information', 'grit' ),
		'description' => esc_html__( "Allow visitors to decide for themselves what personal data they want to store on your site", 'grit' ),
		'required'    => false,
		'logo'        => 'wp-gdpr-compliance.png',
		'group'       => $grit_theme_required_plugins_groups['other'],
	),
	'trx_updater'                => array(
		'title'       => esc_html__( 'ThemeREX Updater', 'grit' ),
		'description' => esc_html__( "Update theme and theme-specific plugins from developer's upgrade server.", 'grit' ),
		'required'    => false,
		'logo'        => 'trx_updater.png',
		'group'       => $grit_theme_required_plugins_groups['other'],
	),
);

if ( GRIT_THEME_FREE ) {
	unset( $grit_theme_required_plugins['js_composer'] );
	unset( $grit_theme_required_plugins['booked'] );
	unset( $grit_theme_required_plugins['quickcal'] );
	unset( $grit_theme_required_plugins['the-events-calendar'] );
	unset( $grit_theme_required_plugins['calculated-fields-form'] );
	unset( $grit_theme_required_plugins['essential-grid'] );
	unset( $grit_theme_required_plugins['revslider'] );
	unset( $grit_theme_required_plugins['sitepress-multilingual-cms'] );
	unset( $grit_theme_required_plugins['trx_updater'] );
	unset( $grit_theme_required_plugins['trx_popup'] );
}

// Add plugins list to the global storage
grit_storage_set( 'required_plugins', $grit_theme_required_plugins );
