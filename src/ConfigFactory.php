<?php

/**
 * Title: Buckaroo config factory
 * Description:
 * Copyright: Copyright (c) 2005 - 2017
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.2.3
 * @since 1.0.0
 */
class Pronamic_WP_Pay_Gateways_Buckaroo_ConfigFactory extends Pronamic_WP_Pay_GatewayConfigFactory {
	public function get_config( $post_id ) {
		$config = new Pronamic_WP_Pay_Gateways_Buckaroo_Config();

		$config->website_key       = get_post_meta( $post_id, '_pronamic_gateway_buckaroo_website_key', true );
		$config->secret_key        = get_post_meta( $post_id, '_pronamic_gateway_buckaroo_secret_key', true );

		$config->excluded_services = get_post_meta( $post_id, '_pronamic_gateway_buckaroo_excluded_services', true );

		$config->invoice_number    = get_post_meta( $post_id, '_pronamic_gateway_buckaroo_invoice_number', true );

		$config->mode              = get_post_meta( $post_id, '_pronamic_gateway_mode', true );

		return $config;
	}
}
