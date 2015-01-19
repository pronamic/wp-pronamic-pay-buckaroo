<?php

/**
 * Title: Buckaroo config factory
 * Description:
 * Copyright: Copyright (c) 2005 - 2014
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0.0
 */
class Pronamic_WP_Pay_Buckaroo_ConfigFactory extends Pronamic_WP_Pay_GatewayConfigFactory {
	public function get_config( $post_id ) {
		$config = new Pronamic_WP_Pay_Buckaroo_Config();

		$config->website_key = get_post_meta( $post_id, '_pronamic_gateway_buckaroo_website_key', true );
		$config->secret_key  = get_post_meta( $post_id, '_pronamic_gateway_buckaroo_secret_key', true );

		$config->mode        = get_post_meta( $post_id, '_pronamic_gateway_mode', true );

		return $config;
	}
}
