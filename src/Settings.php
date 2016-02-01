<?php

/**
 * Title: Buckaroo gateway settings
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.2.1
 * @since 1.2.1
 */
class Pronamic_WP_Pay_Gateways_Buckaroo_Settings extends Pronamic_WP_Pay_GatewaySettings {
	public function __construct() {
		add_filter( 'pronamic_pay_gateway_sections', array( $this, 'sections' ) );
		add_filter( 'pronamic_pay_gateway_fields', array( $this, 'fields' ) );
	}

	public function sections( array $sections ) {
		// Buckaroo
		$sections['buckaroo'] = array(
			'title'   => __( 'Buckaroo', 'pronamic_ideal' ),
			'methods' => array( 'buckaroo' ),
		);

		return $sections;
	}

	public function fields( array $fields ) {
		// Website Key
		$fields[] = array(
			'filter'      => FILTER_SANITIZE_STRING,
			'section'     => 'buckaroo',
			'meta_key'    => '_pronamic_gateway_buckaroo_website_key',
			'title'       => __( 'Website Key', 'pronamic_ideal' ),
			'type'        => 'text',
			'classes'     => array( 'code' ),
			'description' => sprintf(
				__( 'You can find your Buckaroo website keys in the <a href="%s" target="_blank">Buckaroo Payment Plaza</a> under "Profile" » "Website".', 'pronamic_ideal' ),
				'https://payment.buckaroo.nl/'
			),
		);

		// Secret Key
		$fields[] = array(
			'filter'      => FILTER_SANITIZE_STRING,
			'section'     => 'buckaroo',
			'meta_key'    => '_pronamic_gateway_buckaroo_secret_key',
			'title'       => __( 'Secret Key', 'pronamic_ideal' ),
			'type'        => 'text',
			'classes'     => array( 'regular-text', 'code' ),
			'description' => sprintf(
				__( 'You can find your Buckaroo secret key in the <a href="%s" target="_blank">Buckaroo Payment Plaza</a> under "Configuration" » "Secret Key for Digital Signature".', 'pronamic_ideal' ),
				'https://payment.buckaroo.nl/'
			),
		);

		// Push URI
		$fields[] = array(
			'section'     => 'buckaroo',
			'title'       => __( 'Push URI', 'pronamic_ideal' ),
			'type'        => 'text',
			'classes'     => array( 'large-text', 'code' ),
			'value'       => add_query_arg( 'buckaroo_push', '', home_url( '/' ) ),
			'readonly'    => true,
		);

		return $fields;
	}
}
