<?php

namespace Pronamic\WordPress\Pay\Gateways\Buckaroo;

use Pronamic\WordPress\Pay\Gateways\Common\AbstractIntegration;

/**
 * Title: Buckaroo integration
 * Description:
 * Copyright: 2005-2019 Pronamic
 * Company: Pronamic
 *
 * @author Reüel van der Steege
 * @version 2.0.0
 * @since 1.0.0
 */
class Integration extends AbstractIntegration {
	public function __construct() {
		$this->id            = 'buckaroo';
		$this->name          = 'Buckaroo - HTML';
		$this->url           = 'https://plaza.buckaroo.nl/';
		$this->product_url   = __( 'http://www.buckaroo-payments.com', 'pronamic_ideal' );
		$this->dashboard_url = 'https://plaza.buckaroo.nl/';
		$this->provider      = 'buckaroo';
		$this->supports      = array(
			'webhook',
			'webhook_log',
			'webhook_no_config',
		);

		// Actions
		$function = array( __NAMESPACE__ . '\Listener', 'listen' );

		if ( ! has_action( 'wp_loaded', $function ) ) {
			add_action( 'wp_loaded', $function );
		}
	}

	public function get_settings_fields() {
		$fields = array();

		// Website Key.
		$fields[] = array(
			'section'  => 'general',
			'filter'   => FILTER_SANITIZE_STRING,
			'meta_key' => '_pronamic_gateway_buckaroo_website_key',
			'title'    => __( 'Website Key', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'code' ),
			'tooltip'  => __( 'Website key as mentioned in the Buckaroo dashboard on the page "Profile » Website".', 'pronamic_ideal' ),
		);

		// Secret Key.
		$fields[] = array(
			'section'  => 'general',
			'filter'   => FILTER_SANITIZE_STRING,
			'meta_key' => '_pronamic_gateway_buckaroo_secret_key',
			'title'    => __( 'Secret Key', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'regular-text', 'code' ),
			'tooltip'  => __( 'Secret key as mentioned in the Buckaroo dashboardb on the page "Configuration » Secret Key for Digital Signature".', 'pronamic_ideal' ),
		);

		// Excluded services.
		$fields[] = array(
			'section'  => 'advanced',
			'filter'   => FILTER_SANITIZE_STRING,
			'meta_key' => '_pronamic_gateway_buckaroo_excluded_services',
			'title'    => __( 'Excluded services', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'regular-text', 'code' ),
			'tooltip'  => sprintf(
				/* translators: %s: <code>brq_exludedservices</code> */
				__( 'This controls the Buckaroo %s parameter.', 'pronamic_ideal' ),
				sprintf( '<code>%s</code>', 'brq_exludedservices' )
			),
		);

		// Invoice number.
		$fields[] = array(
			'section'     => 'advanced',
			'filter'      => FILTER_SANITIZE_STRING,
			'meta_key'    => '_pronamic_gateway_buckaroo_invoice_number',
			'title'       => __( 'Invoice number', 'pronamic_ideal' ),
			'type'        => 'text',
			'classes'     => array( 'regular-text', 'code' ),
			'tooltip'     => sprintf(
				/* translators: %s: <code>brq_invoicenumber</code> */
				__( 'This controls the Buckaroo %s parameter.', 'pronamic_ideal' ),
				sprintf( '<code>%s</code>', 'brq_invoicenumber' )
			),
			'description' => sprintf(
				'%s<br />%s',
				/* translators: %s: <code>{order_id}</code> <code>{payment_id}</code> */
				sprintf( __( 'Available tags: %s', 'pronamic_ideal' ), sprintf( '<code>%s</code> <code>%s</code>', '{order_id}', '{payment_id}' ) ),
				/* translators: %s: <code>{payment_id}</code> */
				sprintf( __( 'Default: <code>%s</code>', 'pronamic_ideal' ), '{payment_id}' )
			),
		);

		// Push URL.
		$fields[] = array(
			'section'  => 'feedback',
			'title'    => __( 'Push URL', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'large-text', 'code' ),
			'value'    => add_query_arg( 'buckaroo_push', '', home_url( '/' ) ),
			'readonly' => true,
			'tooltip'  => __( 'The Push URL as sent with each transaction to receive automatic payment status updates on.', 'pronamic_ideal' ),
		);

		return $fields;
	}

	public function get_config( $post_id ) {
		$config = new Config();

		$config->website_key       = get_post_meta( $post_id, '_pronamic_gateway_buckaroo_website_key', true );
		$config->secret_key        = get_post_meta( $post_id, '_pronamic_gateway_buckaroo_secret_key', true );
		$config->excluded_services = get_post_meta( $post_id, '_pronamic_gateway_buckaroo_excluded_services', true );
		$config->invoice_number    = get_post_meta( $post_id, '_pronamic_gateway_buckaroo_invoice_number', true );
		$config->mode              = get_post_meta( $post_id, '_pronamic_gateway_mode', true );

		return $config;
	}

	/**
	 * Get gateway.
	 *
	 * @param int $post_id Post ID.
	 * @return Gateway
	 */
	public function get_gateway( $post_id ) {
		return new Gateway( $this->get_config( $post_id ) );
	}
}
