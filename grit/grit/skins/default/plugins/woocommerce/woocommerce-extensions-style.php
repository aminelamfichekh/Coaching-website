<?php
// Add plugin-specific colors and fonts to the custom CSS
if ( ! function_exists( 'grit_woocommerce_extensions_get_css' ) ) {
	add_filter( 'grit_filter_get_css', 'grit_woocommerce_extensions_get_css', 10, 2 );
	function grit_woocommerce_extensions_get_css( $css, $args ) {

		if ( isset( $css['fonts'] ) && isset( $args['fonts'] ) ) {
			$fonts         = $args['fonts'];
			$css['fonts'] .= <<<CSS

.woocommerce-accordion.grit_accordion .woocommerce-accordion-title {
	{$fonts['h5_font-family']}
	{$fonts['h5_font-weight']}
	{$fonts['h5_text-transform']}
	{$fonts['h5_letter-spacing']}
}
.woocommerce #reviews .rating_details .rating_details_avg,
.single_product_custom_text_style {
	{$fonts['h5_font-family']}
}
.woocommerce_extensions_brand,
.woocommerce .summary .woocommerce_extensions_brand,
.woocommerce-product-attributes-item__value,
.woocommerce-product-attributes-item__label {
	{$fonts['p_font-family']}
}	

CSS;
		}

		return $css;
	}
}