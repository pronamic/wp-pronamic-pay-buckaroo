<?php

/**
 * Title: Buckaroo gateway settings
 * Description:
 * Copyright: Copyright (c) 2005 - 2017
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.2.7
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
			'title'       => __( 'Buckaroo', 'pronamic_ideal' ),
			'methods'     => array( 'buckaroo' ),
			'description' => sprintf(
				__( 'Account details are provided by %s after registration. These settings need to match with the %1$s dashboard.', 'pronamic_ideal' ),
				__( 'Buckaroo', 'pronamic_ideal' )
			),
		);

		$sections['buckaroo_advanced'] = array(
			'title'       => __( 'Advanced', 'pronamic_ideal' ),
			'methods'     => array( 'buckaroo' ),
			'description' => __( 'Optional settings for advanced usage only.', 'pronamic_ideal' ),
		);

		// Transaction feedback
		$sections['buckaroo_feedback'] = array(
			'title'       => __( 'Transaction feedback', 'pronamic_ideal' ),
			'methods'     => array( 'buckaroo' ),
			'description' => __( 'Payment status updates will be processed without any additional configuration. The <em>Push URL</em> is being used to receive the status updates.', 'pronamic_ideal' ),
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
			'tooltip'     => __( 'Website key as mentioned in the Buckaroo dashboard on the page "Profile » Website".', 'pronamic_ideal' ),
		);

		// Secret Key
		$fields[] = array(
			'filter'      => FILTER_SANITIZE_STRING,
			'section'     => 'buckaroo',
			'meta_key'    => '_pronamic_gateway_buckaroo_secret_key',
			'title'       => __( 'Secret Key', 'pronamic_ideal' ),
			'type'        => 'text',
			'classes'     => array( 'regular-text', 'code' ),
			'tooltip'     => __( 'Secret key as mentioned in the Buckaroo dashboardb on the page "Configuration » Secret Key for Digital Signature".', 'pronamic_ideal' ),
		);

		// Transaction feedback
		$fields[] = array(
			'section'     => 'buckaroo',
			'title'       => __( 'Transaction feedback', 'pronamic_ideal' ),
			'type'        => 'description',
			'html'        => sprintf(
				'<span class="dashicons dashicons-yes"></span> %s',
				__( 'Payment status updates will be processed without any additional configuration.', 'pronamic_ideal' )
			),
		);

		// Excluded services
		$fields[] = array(
			'filter'      => FILTER_SANITIZE_STRING,
			'section'     => 'buckaroo_advanced',
			'meta_key'    => '_pronamic_gateway_buckaroo_excluded_services',
			'title'       => __( 'Excluded services', 'pronamic_ideal' ),
			'type'        => 'text',
			'classes'     => array( 'regular-text', 'code' ),
			'tooltip'     => sprintf(
				__( 'This controls the Buckaroo %s parameter.', 'pronamic_ideal' ),
				sprintf( '<code>%s</code>', 'brq_exludedservices' )
			),
		);

		// Invoice number
		$fields[] = array(
			'filter'      => FILTER_SANITIZE_STRING,
			'section'     => 'buckaroo_advanced',
			'meta_key'    => '_pronamic_gateway_buckaroo_invoice_number',
			'title'       => __( 'Invoice number', 'pronamic_ideal' ),
			'type'        => 'text',
			'classes'     => array( 'regular-text', 'code' ),
			'tooltip'     => sprintf(
				__( 'This controls the Buckaroo %s parameter.', 'pronamic_ideal' ),
				sprintf( '<code>%s</code>', 'brq_invoicenumber' )
			),
			'description' => sprintf(
				'%s<br />%s',
				sprintf( __( 'Available tags: %s', 'pronamic_ideal' ), sprintf( '<code>%s</code> <code>%s</code>', '{order_id}', '{payment_id}' ) ),
				sprintf( __( 'Default: <code>%s</code>', 'pronamic_ideal' ), '{payment_id}' )
			),
		);

		// Push URL
		$fields[] = array(
			'section'     => 'buckaroo_feedback',
			'title'       => __( 'Push URL', 'pronamic_ideal' ),
			'type'        => 'text',
			'classes'     => array( 'large-text', 'code' ),
			'value'       => add_query_arg( 'buckaroo_push', '', home_url( '/' ) ),
			'readonly'    => true,
			'tooltip'     => __( 'The Push URL as sent with each transaction to receive automatic payment status updates on.', 'pronamic_ideal' ),
		);

		return $fields;
	}
}
