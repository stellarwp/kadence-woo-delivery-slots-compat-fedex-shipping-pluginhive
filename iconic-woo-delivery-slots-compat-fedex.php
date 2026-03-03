<?php
/**
 * Plugin Name:     WooCommerce Delivery Slots by Kadence [WooCommerce FedEx Shipping Plugin with Print Label]
 * Plugin URI:      https://iconicwp.com/products/woocommerce-delivery-slots/
 * Description:     Compatibility between WooCommerce Delivery Slots by Kadence and WooCommerce FedEx Shipping Plugin with Print Label by PluginHive.
 * Author:          Kadence
 * Author URI:      https://www.kadencewp.com/
 * Text Domain:     iconic-woo-delivery-slots-compat-fedex-shipping-pluginhive
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Iconic_Woo_Delivery_Slots_Compat_Fedex_Pluginhive
 */

/**
 * Is WooCommerce Table Rate Shipping Pro active?
 *
 * @return bool
 */
function iconic_compat_fedex_is_active() {
	return class_exists( 'Ph_Fedex_Woocommerce_Shipping_Common' );
}

/**
 * Remove default options.
 *
 * @return array
 */
function iconic_compat_fedex_remove_default_shipping_method_options( $shipping_method_options ) {
	if ( ! iconic_compat_fedex_is_active() ) {
		return $shipping_method_options;
	}

	unset( $shipping_method_options['wf_fedex_woocommerce_shipping_method'] );

	$shipping_method_options = $shipping_method_options + iconic_compat_fedex_get_zoneless_rates();

	return $shipping_method_options;
}

add_filter( 'iconic_wds_shipping_method_options', 'iconic_compat_fedex_remove_default_shipping_method_options', 10 );

/**
 * Get zoneless shipping rates/methods.
 *
 * @return array
 */
function iconic_compat_fedex_get_zoneless_rates() {
	$rates = array();

	if ( ! class_exists( 'wf_fedex_woocommerce_shipping_method' ) ) {
		return $rates;
	}

	$method = new wf_fedex_woocommerce_shipping_method();

	if ( ! $method ) {
		return $rates;
	}

	$services = $method->get_option( 'services' );

	if ( empty( $services ) ) {
		return $rates;
	}

	foreach ( $services as $service_id => $service ) {
		$method_id           = sprintf( 'wf_fedex_woocommerce_shipping:%s', $service_id );
		$shipping_name       = ! empty( $service['name'] ) ? $service['name'] : $service_id;
		$rates[ $method_id ] = $shipping_name;
	}

	return $rates;
}


/**
 * Activate the plugin.
 */
function iconic_compat_fedex__activate() {
	delete_transient( 'iconic-wds-shipping-methods' );
}
register_activation_hook( __FILE__, 'iconic_compat_fedex__activate' );
