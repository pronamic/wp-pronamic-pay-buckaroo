<?php

namespace Pronamic\WordPress\Pay\Gateways\Buckaroo;

use Pronamic\WordPress\Pay\Core\GatewayConfigFactory;

/**
 * Title: Buckaroo config factory
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 2.0.0
 * @since 1.0.0
 */
class ConfigFactory extends GatewayConfigFactory {
	public function get_config( $post_id ) {
		$config = new Config();

		$config->website_key       = get_post_meta( $post_id, '_pronamic_gateway_buckaroo_website_key', true );
		$config->secret_key        = get_post_meta( $post_id, '_pronamic_gateway_buckaroo_secret_key', true );
		$config->excluded_services = get_post_meta( $post_id, '_pronamic_gateway_buckaroo_excluded_services', true );
		$config->invoice_number    = get_post_meta( $post_id, '_pronamic_gateway_buckaroo_invoice_number', true );
		$config->mode              = get_post_meta( $post_id, '_pronamic_gateway_mode', true );

		return $config;
	}
}
